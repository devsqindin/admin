<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SolicitacaoParcelamento;
use App\Fatura;
use App\Usuario;
use App\Taxa;
use Auth;
use Notification;
use App\Notifications\Analyze;
use App\Notifications\ClosedInvoice;
use App\Notifications\DelayedInvoice;
use DB;
use Storage;
use Illuminate\Support\Facades\Log;

class ParcelamentoController extends Controller
{
    
    public $maxdecimal = 7;
    public $initcet = '';
    public $arrdias = [];

    public function fNum ($num) {
        $num = str_replace(".","",$num);
        return str_replace(",",".",$num);
    }

    public function diasIntervalo($dtini,$qtdparcelas) {
        $this->arrdias = [];
        for($p=1;$p<=$qtdparcelas;$p++) {
            $dtatual = strtotime($dtini);
            $dtatual = date("Y-m-d", strtotime("+$p month", $dtatual));
            
            $datetime1 = new \DateTime($dtini);
            $datetime2 = new \DateTime($dtatual);
            $interval = $datetime1->diff($datetime2)->format('%a');
            
            $this->arrdias[] = intval($interval);
        }
    }

    public function calculaCET($credito,$fcj) {
        $this->initcet='';
        for($c=0;$c<$this->maxdecimal;$c++) {
            $this->initcet .= '0';
        }
        return $this->procurarCET($credito,$fcj,$this->initcet)/10000;
    }

    public function procurarCET($credito,$fcj,$cet,$decimal=0) {
        //global $arrdias, $fcj, $credito, $maxdecimal;
        if ($decimal > $this->maxdecimal) {
            return $cet;
        } else {
            for($i=0;$i<=9;$i++) {
                $cet[$decimal] = $i;
                
                $resultado = 0;
                foreach($this->arrdias as $dia) {
                    $resultado += $fcj/((1+floatval("0.$cet")*(10**($this->maxdecimal-5)))**($dia/365));
                }
                
                if (($resultado - $credito) == 0) {
                    return $cet;
                } else if (($resultado - $credito) > 0) {
                    // continue
                } else if (($resultado - $credito) < 0) {
                    if ($i == 0) {
                        return $cet;
                    } else {
                        $cet[$decimal] = $i - 1;
                    }
                    break;
                }
            }
            return $this->procurarCET($credito,$fcj,$cet,$decimal+1);
        }
    }

    public function getLoanPreview(
    $token, 
    $schedule_type, 
    $interest_rate, 
    $requested_amount, 
    $tac_amount, 
    $finance_fee, 
    $num_payments, 
    $first_payment_date, 
    $disbursement_date, 
    $iof_type){

        $celcoinTokenRequest = $this->getCelcoinToken();

        $client = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));

        $response = $client->request('POST', 'https://sandbox.platform.flowfinance.com.br/banking/originator/applications/preview', [
            'body' => '{
            "schedule_type":"MONTHLY",
            "interest_rate":1.5,
            "requested_amount":50000,
            "tac_amount":1.5,
            "finance_fee":1.5,
            "num_payments":1,
            "first_payment_date":"date("Y-m-d")+2",
            "disbursement_date":"02-03-2023",
            "iof_type":"PERSON"
            }',

  'headers' => [
    'accept' => 'application/json',
    'authorization' => 'Bearer '.$token,
    'content-type' => 'application/json',
  ],
]);

    echo $response->getBody();

    $previewResponse = json_decode($response->getBody(),true);
    
    return $previewResponse;

    }

    public function apiSimularFiducia($valor,$valor_tac,$vencimento,$meses,$juros) {
        Log::debug('QINDIN-PARCELAMENTO - calling apiSimularFiducia: ');
        $curlHandler = curl_init();
        $userName = env('FIDUCIA_USER');
        $password = env('FIDUCIA_PASS');

        $postfield = '{
            "tipo_pessoa": "PF",
            "valor_liberado": '.number_format($valor,2,'.','').',
            "primeiro_vencimento": "'.$vencimento.'",
            "parcelas": '.$meses.',
            "taxa_juros": '.($juros*100).',
            "TAC": '.$valor_tac.',
            "periodicidade": "M"
        }';

        curl_setopt_array($curlHandler, [
            CURLOPT_URL => env('FIDUCIA_URL_SIMULAR','https://api.bancarizacao.fiducia.digital/api/v1/bancarizacao/simular'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            //CURLOPT_USERPWD => $userName . ':' . $password,
            /**
             * Specify POST method
             */
            CURLOPT_POST => true,
            /**
             * Specify request headers
             */
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic UUlORElOOnRBclN4cThV',
                //'User: '.env('FIDUCIA_HEADER')
                'User: 40076375000150'
            ],
            /**
             * Specify request content
             */
            CURLOPT_POSTFIELDS => $postfield,
        ]);
 
        $response = curl_exec($curlHandler);
        curl_close($curlHandler);

        $aresponse = json_decode($response,1);
        $aresponse['postfield'] = $postfield;

        return ($aresponse['status'] == 'sucesso') ? $aresponse : false;
    }

    public function dataContrato($dtParam=null) {
        $diasem = null;

        // dia semana php / dias para somar
        $arrDiasContrato = [
            0=>2, //dom
            1=>2,
            2=>2,
            3=>2,
            4=>4, //qui
            5=>4, 
            6=>3, //sab
        ];

        if ($dtParam) {
            $diasem = date("w",strtotime($dtParam));
        } else {
            $diasem = date("w");
        }

        return date("U",strtotime('+'.$arrDiasContrato[$diasem].' days')); //timestamp
    }

    public function financiar($valor,$taxa_juros,$meses,$userId) {
        // referencia

        // $valor = 100;
        // $taxa_juros = 0.0999;
        // $meses = 3;

        $valor = number_format($valor,2,'.','');

        // taxas e txint

        $taxas = Taxa::where('slug','!=','taxaint')->get()->pluck('valor','slug');
        $txint = Taxa::where('slug','=','taxaint')->where('nparcela',$meses)->first();

        // elementos variaveis da operação

        $taxa_variavel_intermediador = $txint['valor'];
        $valor_base_cardholder = $valor / (1 - $taxas['taxa_intermedia']);
        $valor_base_remuneracao = $valor_base_cardholder * (1+$taxa_variavel_intermediador);

        // elementos componentes da tac

        $valor_tar_core = $taxas['tarifa_core'];
        $valor_cons_tomador = $taxas['tarifa_serasa'];
        $valor_cred_intermediador = $valor_base_cardholder - $valor;
        $valor_intermediador_cardholder = $valor_base_cardholder * $taxa_variavel_intermediador;
        $valor_remuneracao_cardholder = $valor_base_remuneracao * $taxas['taxa_cardholder'];
        $valores_custos_bdi = $valor * $taxas['custos_fixo_bdi'];

        $valor_tx_core = ($valor_base_cardholder + $valor_tar_core + $valor_cons_tomador + $valor_cred_intermediador + $valor_intermediador_cardholder + $valor_remuneracao_cardholder) * $taxas['taxa_core'];

        $subtotal_base_tac = $valor_tar_core + $valor_tx_core + $valor_cons_tomador + $valor_cred_intermediador + $valor_intermediador_cardholder + $valor_remuneracao_cardholder + $valores_custos_bdi;

        $valor_tac = $subtotal_base_tac / (1 - ($taxas['taxa_simples_nac']+$taxas['taxa_imp_tac']));

        $valor_tac = number_format($valor_tac,2,'.','');

        $user = Usuario::find($userId);
        $fatura = Fatura::where('id_usuario',$userId)->where('fechado',0)->first();

        Storage::put('fatura-'.$userId.'.txt',$userId.' '.json_encode($fatura));

        $dtvencimento = null;

        if (isset($fatura->vencimento)) {
            if ($this->dataContrato() > date("U",strtotime($fatura->vencimento))) {
                $dtvencimento = date("Y-m-",strtotime($fatura->vencimento." +1 month")).$user->vence_fatura;
            } else {
                $dtvencimento = $fatura->vencimento;
            }
        } else {
            $dtvencimento = date("Y-m-").$user->vence_fatura;
            // se data solicitação for igual ou maior que o limite de fechamento da fatura = +1 mês
            if (date("U") >= date("U",strtotime($dtvencimento." -5 days"))) {
                $dtvencimento = date("Y-m-",strtotime("+1 month")).$user->vence_fatura;
            }
        }

        // consulta api fiducia - bancarização
        $consulta = $this->apiSimularFiducia($valor,$valor_tac,$dtvencimento,$meses,$taxa_juros);

        $cet = null;
        $iof = null;
        $parcela = null;

        if ($consulta) {
            $cet = $consulta['resposta']['CET_FI'];
            $iof = $consulta['resposta']['IOF_FI'];
            $parcela = $consulta['resposta']['parcela_FI'];
        }

        return compact('cet','iof','parcela','dtvencimento','valor_tac','consulta');
    }

    public function calcular2(Request $request) {
        $user = Usuario::find(1);
        Log::debug('QINDIN-PARCELAMENTO - calcular2, user requesting credits: ');
        if ($user) {

            $parcelas = [];
            $parcelaIof = [];
            $parcelaCet = [];

            $valor_solicitado = floatval($this->fNum($request->valor_solicitado));

            $juros_padrao = Taxa::where('slug','=','juros_padrao')->first();
            $juros = ($user->taxa_juros && $user->taxa_juros > 0) ? $user->taxa_juros : $juros_padrao->valor;

            for($p=3;$p<=12;$p++) {
                $financiar = $this->financiar($valor_solicitado,$juros,$p,$user->id);
                $parcelas[$p] = $financiar['parcela'];
                $parcelaIof[$p] = $financiar['iof'];
                $parcelaCet[$p] = $financiar['cet'];
                $dtVencimentoPrimeira = $financiar['dtvencimento'];
                $valorTac[$p] = $financiar['valor_tac'];
            }

            return response()->json(['success'=>true,'dtvencimento'=>date("d/m/Y",strtotime($dtVencimentoPrimeira)),'juros'=>$juros,'valor_solicitado'=>$valor_solicitado,'parcelas'=>$parcelas,'iof'=>$parcelaIof,'cet'=>$parcelaCet,'valor_tac'=>$valorTac]);
        } else {
            return response()->json(['success'=>false]);
        }
    }

    public function calcular(Request $request) {
    	$user = Auth::user();

        // Return false para travar do lado da API o pedido de crédito.
        //return response()->json(['success'=>false]);

    	if ($user) {

            $parcelas = [];
            $parcelaIof = [];
            $parcelaCet = [];

            $valor_solicitado = floatval($this->fNum($request->valor_solicitado));

            if ($valor_solicitado < 100) {
                return response()->json(['success'=>false,'message'=>'Valor solicitado deve ser igual ou maior que R$ 100']);
            }

            $juros_padrao = Taxa::where('slug','=','juros_padrao')->first();
            $juros = ($user->taxa_juros && $user->taxa_juros > 0) ? $user->taxa_juros : $juros_padrao->valor;

    		for($p=3;$p<=12;$p++) {
                $financiar = $this->financiar($valor_solicitado,$juros,$p,$user->id);
                $parcelas[$p] = $financiar['parcela'];
                $parcelaIof[$p] = $financiar['iof'];
                $parcelaCet[$p] = $financiar['cet'];
                $dtVencimentoPrimeira = $financiar['dtvencimento'];
    		}

            if($parcelas[3] == null) {
                return response()->json(['success'=>false,'message'=>'A API Fidúcia está apresentando problemas para retornar o cálculo.']);
            }

    		return response()->json(['success'=>true,'dtvencimento'=>date("d/m/Y",strtotime($dtVencimentoPrimeira)),'juros'=>$juros,'valor_solicitado'=>$valor_solicitado,'parcelas'=>$parcelas,'iof'=>$parcelaIof,'cet'=>$parcelaCet]);
    	} else {
    		return response()->json(['success'=>false]);
    	}
    }

    public function confirmar(Request $request) {
        $user = Auth::user();
        Log::debug('QINDIN-PARCELAMENTO - calcular2, user confirming credits: start');
        if ($user) {
            $token = md5("A".$user->id."XX".$request->parcelas."XX".$request->valor_solicitado);
            return response()->json(['success'=>true,'token'=>$token]);
        }
        Log::debug('QINDIN-PARCELAMENTO - calcular2, user confirming credits: success');
        return response()->json(['success'=>false]);
    }

    public function registrar(Request $request) {
        $user = Auth::user();
        Log::debug('QINDIN-PARCELAMENTO - calcular2, user registering credits: start');
        if ($user && $user->validateForPassportPasswordGrant($request->password)) {
            DB::beginTransaction();

            $valor_solicitado = number_format($request->valor_solicitado,2,'.','');
            
            $juros_padrao = Taxa::where('slug','=','juros_padrao')->first();
            $juros = ($user->taxa_juros && $user->taxa_juros > 0) ? $user->taxa_juros : $juros_padrao->valor;
            $financiar = $this->financiar($valor_solicitado,$juros,$request->parcelas,$user->id);
            if ($financiar) {
                $dtVencimentoPrimeira = $financiar['dtvencimento'];
                if ($financiar['parcela'] != $request->valor_parcela) {
                    die('erro');
                }
            }

            // conferir valores e token
            if ($request->token == md5("A".$user->id."XX".$request->parcelas."XX".$request->valor_solicitado)) {

                // registrar parcelamento
                $parcela = SolicitacaoParcelamento::create([
                    'valor_solicitado' => $request->valor_solicitado,
                    'valor_parcela' => $request->valor_parcela,
                    'valor_tac' => $financiar['valor_tac'],
                    'parcelas' => $request->parcelas,
                    'taxa_juros' => $juros,
                    'reg_date'=>date("Y-m-d"),
                    'id_usuario'=>$user->id,
                    'primeira_parcela'=>$dtVencimentoPrimeira,
                    // vencimento da última parcela (nº parcelas + 1 para o próximo mês)
                    'ultima_parcela'=>date("Y-m-d",strtotime($dtVencimentoPrimeira." +".($request->parcelas)." month"))
                ]);

                // atualização de saldos
                $user->limite_utilizado = $user->limite_utilizado + $request->valor_solicitado;
                $user->limite_disponivel = $user->limite_disponivel - $request->valor_solicitado;
                $user->save();

                $postfield = $financiar['consulta']['postfield'];
                unset($financiar['consulta']['postfield']);

                $user->historicos()->create([
                    'id_acao_historico'=>12,
                    'descricao'=>strval($postfield),
                    'valor'=>strval(json_encode($financiar['consulta'])),
                    'datahora'=>date("Y-m-d H:i:s"),
                    'id_operador'=>null,
                ]);

                for ($p=0;$p<=($request->parcelas-1);$p++) { //partindo de 0

                    $fatura = Fatura::firstOrNew(['anomes'=>date("Ym",strtotime($dtVencimentoPrimeira." +".($p)." month")),'id_usuario'=>$user->id]);
                    $fatura->id_usuario = $user->id;
                    $fatura->anomes = date("Ym",strtotime($dtVencimentoPrimeira." +".($p)." month"));
                    $fatura->reg_date = date("Y-m-d");
                    $fatura->vencimento = date("Y-m-d",strtotime($dtVencimentoPrimeira." +".($p)." month"));
                    $fatura->fechamento = date("Y-m-d",strtotime($fatura->vencimento." -5 days"));
                    $fatura->valor_total = ($fatura->valor_total) ? $fatura->valor_total : 0;
                    $fatura->valor_total = $fatura->valor_total + $request->valor_parcela;
                    $fatura->save();

                    $parcela->parcelaFatura()->create([
                        'numparcela'=>$p+1,
                        'id_fatura'=>$fatura->id,
                        'id_usuario'=>$user->id,
                    ]);

                }

                //Notification::send($user, new Analyze($user));

                DB::commit();
                Log::debug('QINDIN-PARCELAMENTO - calcular2, user registering credits: success');
                return response()->json(['success'=>true,'data_contrato'=>date("d/m/Y",$this->dataContrato())]);
            }
            Log::debug('QINDIN-PARCELAMENTO - calcular2, user registering credits: fail token');
            return response()->json(['success'=>false]);
        } else {
            Log::debug('QINDIN-PARCELAMENTO - calcular2, user registering credits: fail password');
            return response()->json(['success'=>false,'error'=>'Senha inválida']);
        }
    }

    public function cCheckBill() {
        // fechar faturas
        $faturas = Fatura::where('fechamento','<=',date("Y-m-d"))->where('fechado',0)->get();
        foreach ($faturas as $fatura) {
            $fatura->fechado = 1;
            $fatura->save();

            $user = $fatura->usuario;
            Notification::send($user, new ClosedInvoice($user));
        }

        $faturas = Fatura::where('fechado',1)->where('pago',0)->get();
        foreach ($faturas as $fatura) {
            if (Carbon::parse($fatura->vencimento)->addDays(3)->format('Y-m-d') == date("Y-m-d")) {
                $user = $fatura->usuario;
                Notification::send($user, new DelayedInvoice($user));
            }
        }

        return response()->json(['success'=>true]);
    }
}
