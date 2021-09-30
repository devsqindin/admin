<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuario;
use App\UsuarioBelvo;
use App\UsuarioHistorico;
use App\Documento;
use App\Fatura;
use App\Pergunta;
use App\Taxa;
use App\TipoDocumento;
use App\ParcelasFatura;
use App\Cobranca;
use App\Operador;
use App\Motivo;
use App\OperadorPermissao;
use App\DocumentoFiducia;
use App\SolicitacaoParcelamento;
use App\Notifications\Approved;
use App\Notifications\Invited;
use App\Notifications\Rejected;
use App\Notifications\Blocked;
use App\Notifications\Unregister;
//use App\Notifications\ClosedInvoice;
use App\Mail\ClosedInvoice;
use App\Mail\DelayedInvoice;
use App\Notifications\Receipt;
use DataTables;
use DB;
use Storage;
use Auth;
use Notification;
use Mail;
use URL;
use Image;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;

class AdminController extends Controller
{
    
    public $userId = 24;
    public $maxrecord = 2000;

    public function fNum($num) {
        $num = str_replace(".","",$num);
        return str_replace(",",".",$num);
    }
    
    public function fData($dataf) {
        return substr($dataf,6,4)."-".substr($dataf,3,2)."-".substr($dataf,0,2);
    }

    public function somenteNumeros($value) {
        return preg_replace('/[^0-9]/', '', $value);;
    }

    public function fZero($value) {
        return ($value) ? $value : 0;
    }

    public function login() {
        return view('login');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin');
    }

    public function fazerLogin(Request $request) {
        if(Auth::guard('admin')->attempt($request->only('email','password'))){
            
            UsuarioHistorico::create([
                'descricao'=>'LOGIN',
                'valor'=>'LOGIN',
                'id_operador'=>Auth::guard('admin')->user()->id,
                'id_acao_historico'=>13,
                'datahora'=>date("Y-m-d H:i:s")
            ]);
            //Authentication passed...
            return redirect()->intended(route('admin.clientes'));
        }
        //Authentication failed...
        return $this->falhaLogin();
    }

    private function falhaLogin()
    {
        return redirect()
        ->back()
        ->withInput()
        ->with('error','Credenciais erradas');
    }

    public function checkContas() {
        $users = Usuario::where('status','>=',2)->get();
        foreach($users as $user) {
            $user->agencia = trim($user->agencia);
            if (strlen($user->agencia) < 4) {
                echo "MENOS 4 | ID: ".$user->id." - ".$user->nome_completo." ".$user->agencia."<br>";
            } else if (strlen($user->agencia) > 4) {
                echo "MAIS (".strlen($user->agencia).") | ID: ".$user->id." - ".$user->nome_completo." ".$user->agencia."<br>";
            }
        }
    }

    /*public function ler() {
        $usuario = Usuario::find(1);
        $arrCateg = [
                1=>4,
                2=>4,
                3=>3,
                4=>10,
                5=>7,
                6=>8,
                7=>5,
            ];
        $operacaoId = 9999;
        foreach($usuario->documentos()->whereNull('fiducia')->get() as $doc) {
            if ($doc->tipo == 'jpg') {
                $datahead = 'data:image/jpg;base64,';
            } else if ($doc->tipo == 'pdf') {
                $datahead = 'data:application/pdf;base64,';
            } else {
                die();
            }
            $postfield = '{
                "tipo_documento": "'.mb_strtoupper($doc->tipo).'",
                "categoria": '.$arrCateg[$doc->id_tipo_documento].',
                "operacao": '.$operacaoId.',
                "base64": "'.$datahead.base64_encode(Storage::get('documentos/'.$usuario->id.'/'.$doc->titulo)).'"
            }';
            echo $postfield.'<hr>';
        }
    }*/

    public function ler() {
        $img = Image::make(Storage::get('gau.png'));
        $img->resize(1920, 1920, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        if ($img->mime() == 'image/jpg' || $img->mime() == 'image/jpeg') {
            $basesend = (string) $img->encode('data-url');
        } else {
            $basesend = (string) $img->encode('jpg',90)->encode('data-url');
        }
        echo $basesend;
    }

    public function belvoCache($type,$userId) {
        $belvo = UsuarioBelvo::where('id_usuario',$userId)->where('type',$type)->first();
        $belvo->delete();
        return redirect('/admin/cliente/'.$userId);
    }

    public function belvoCheckLink($link,$cnt=1) {
        $userName = env('BELVO_ID');
        $password = env('BELVO_PASS');
        $curlHandler = curl_init();

        curl_setopt_array($curlHandler, [
            CURLOPT_URL => "https://".env('BELVO_URL')."/api/links/".$link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $userName . ':' . $password,
        ]);
 
        $response = curl_exec($curlHandler);
        curl_close($curlHandler);
        $aresponse = json_decode($response,1);

        if ($aresponse['status']!='valid') {
            if ($cnt < 3) {
                $this->belvoUpdateLink($link);
                sleep(1);
                $this->belvoCheckLink($link,($cnt+1));
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function belvoUpdateLink($link) {

        $userName = env('BELVO_ID');
        $password = env('BELVO_PASS');

        $postfieldJson = [
            'id'=>$userName,
            'password'=>$password,
            'link_id'=>$link,
            'scopes'=>'read_institutions,write_links,read_links',
        ];

        $postfield = json_encode($postfieldJson);
        $curlHandler = curl_init();

        curl_setopt_array($curlHandler, [
            CURLOPT_URL => "https://".env('BELVO_URL')."/api/token",
            CURLOPT_RETURNTRANSFER => true,
            /**
             * Specify POST method
             */
            CURLOPT_POST => true,
            /**
             * Specify request headers
             */
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Host: '.env('BELVO_URL'),
            ],
            /**
             * Specify request content
             */
            CURLOPT_POSTFIELDS => $postfield,
        ]);
 
        $response = curl_exec($curlHandler);
        curl_close($curlHandler);
    }

    public function belvoConsulta($type,$userId) {
        set_time_limit(0);
        if (!in_array($type, ['accounts','owners','balances','incomes','transactions'])) {
            return response()->json(['success'=>false,'message'=>'Tipo de dados inválido']);
        }

        $belvo = UsuarioBelvo::where('id_usuario',$userId)->where('type',$type)->first();

        if ($belvo) {
            return response()->json(['success'=>true,'data'=>base64_encode($belvo->json),'cache'=>true]);
        }

        $user = Usuario::find($userId);

        if (!$user || !$user->belvo_link) {
            return response()->json(['success'=>false,'message'=>'Usuário inválido']);
        }

        if (!$this->belvoCheckLink($user->belvo_link)) {
            return response()->json(['success'=>false,'message'=>'tentativas de renovar sem sucesso']);
        }

        $postfieldJson = [
            'link'=>$user->belvo_link
        ];

        // outros atributos
        if ($type == 'balances' || $type == 'transactions') {
            $postfieldJson['date_from'] = date("Y-m-d",strtotime("-12 months"));
            $postfieldJson['date_to'] = date("Y-m-d");
        }

        $userName = env('BELVO_ID');
        $password = env('BELVO_PASS');
        $postfield = json_encode($postfieldJson);
        $curlHandler = curl_init();

        curl_setopt_array($curlHandler, [
            CURLOPT_URL => "https://".env('BELVO_URL')."/api/".$type."/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $userName . ':' . $password,
            /**
             * Specify POST method
             */
            CURLOPT_POST => true,
            /**
             * Specify request headers
             */
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Host: '.env('BELVO_URL'),
            ],
            /**
             * Specify request content
             */
            CURLOPT_POSTFIELDS => $postfield,
        ]);
 
        $response = curl_exec($curlHandler);
        $http_code = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($curlHandler, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        curl_close($curlHandler);

        /* 
            Error handling: 
            https://docs.belvo.com/#operation/RetrieveBalances 
            https://docs.belvo.com/#operation/RetrieveTransactions
        */
        if($http_code === 200 || $http_code === 201) {
            
            UsuarioBelvo::create([
                'json'=>$response,
                'type'=>$type,
                'id_usuario'=>$user->id,
            ]);
        } else {

            return response()->json(['success'=>false,'data'=>base64_encode($response)]); 
        }
        
        return response()->json(['success'=>true,'data'=>base64_encode($response)]);
    }
    
    public function apiRegistroCredito($valor,$valor_tac,$vencimento,$meses,$juros,$usuario,$codSolicitacao) {
        $curlHandler = curl_init();
        $userName = env('FIDUCIA_USER');
        $password = env('FIDUCIA_PASS');

        $postfield = '{
            "numero_ccb": "40076'.$codSolicitacao.'",
            "modalidade": "FI",
            "valor_liberado": '.number_format($valor,2,'.','').',
            "taxa_juros": '.($juros*100).',
            "parcelas": '.$meses.',
            "primeiro_vencimento": "'.$vencimento.'",
            "periodicidade": "M",
            "TAC": '.$valor_tac.',
            "cliente": {
                "nome_razaosocial": "'.$usuario->nome_completo.'",
                "tipo_pessoa": "PF",
                "cpfcnpj": "'.$this->somenteNumeros($usuario->cpf).'",
                "rg": "'.$this->somenteNumeros($usuario->rg).'",
                "orgao_rg": "'.$usuario->rg_orgao.'",
                "UF_rg": "'.mb_strtoupper($usuario->rg_uf).'",
                "emissao_rg": "'.$this->fData($usuario->rg_dtemissao).'",
                "inscricao_estadual": null,
                "nascimento": "'.$this->fData($usuario->data_nascimento).'",
                "sexo": "'.$usuario->sexo.'",
                "email": "'.$usuario->email.'",
                "celular": "'.$usuario->whatsapp.'",
                "nacionalidade": "'.$usuario->nacionalidade.'",
                "naturalidade": "'.$usuario->habita_cidade.'",
                "endereco": {
                    "logradouro": "'.$usuario->endereco.'",
                    "numero": "'.$usuario->numero.'",
                    "complemento": "'.$usuario->complemento.'",
                    "bairro": "'.$usuario->bairro.'",
                    "cidade": "'.$usuario->cidade.'",
                    "UF": "'.$usuario->estado.'",
                    "CEP": "'.$usuario->cep.'"
                },
                "dados_bancarios": {
                    "agencia": "'.$usuario->agencia.'",
                    "dig_agencia": null,
                    "cod_banco": "'.$usuario->banco.'",
                    "conta": "'.$usuario->numero_conta.'",
                    "dig_conta": "'.$usuario->dv_conta.'",
                    "operacao": null,
                    "cpfcnpj_titular": "'.$this->somenteNumeros($usuario->cpf).'",
                    "tipo": "CC"
                }
            }
        }';

        // $usuario->nacionalidade != NULL && $usuario->habita_cidade != NULL &&
        if ($codSolicitacao != NULL && $valor != NULL && $juros != NULL && $meses != NULL && $vencimento != NULL && $valor_tac != NULL && $usuario->nome_completo != NULL && $usuario->cpf != NULL && $usuario->rg != NULL && $usuario->rg_orgao != NULL && $usuario->rg_uf != NULL && $usuario->rg_dtemissao != NULL && $usuario->data_nascimento != NULL && $usuario->sexo != NULL && $usuario->email != NULL && $usuario->whatsapp != NULL && $usuario->endereco != NULL && $usuario->numero != NULL && $usuario->bairro != NULL && $usuario->cidade != NULL && $usuario->estado != NULL && $usuario->cep != NULL && $usuario->agencia != NULL && $usuario->banco != NULL && $usuario->numero_conta != NULL && $usuario->cpf != NULL) {
        } else {
            $usuario->historicos()->create([
                'id_acao_historico'=>10,
                'descricao'=>$postfield,
                'valor'=>null,
                'datahora'=>date("Y-m-d H:i:s"),
            ]);
            return ['success'=>false,'message'=>'Dados Incompletos'];
        }

        curl_setopt_array($curlHandler, [
            CURLOPT_URL => env('FIDUCIA_URL_IMPORTAR','https://api.bancarizacao.fiducia.digital/api/v1/bancarizacao/importar'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $userName . ':' . $password,
            /**
             * Specify POST method
             */
            CURLOPT_POST => true,
            /**
             * Specify request headers
             */
            CURLOPT_HTTPHEADER => [
                'user: '.env('FIDUCIA_HEADER')
            ],
            /**
             * Specify request content
             */
            CURLOPT_POSTFIELDS => $postfield,
            CURLOPT_FAILONERROR=>true,
        ]);
 
        $response = '';
        $response = curl_exec($curlHandler);
        if (curl_errno($curlHandler)) {
            $response .= curl_error($ch);
        }
        curl_close($curlHandler);
        $aresponse = json_decode($response,1);

        $usuario->historicos()->create([
            'id_acao_historico'=>10,
            'descricao'=>$postfield,
            'valor'=>$response,
            'datahora'=>date("Y-m-d H:i:s"),
        ]);

        if ($aresponse['status']=='sucesso') {
            // atualizar parcela se necessário
            $sol = SolicitacaoParcelamento::find($codSolicitacao);
            if ($sol->valor_parcela != $aresponse['resposta']['valores']['valor_parcela']) {
                $sol->valor_parcela = $aresponse['resposta']['valores']['valor_parcela'];
                $sol->save();
            }
            $parcelas = $sol->parcelaFatura()->get();
            if ($parcelas && !env('FIDUCIA_HOMOLOG')) {
                foreach($parcelas as $parcela) {
                    $this->recalculaFatura($parcela->id_fatura);
                }
            }

            return ['success'=>true,'operacao'=>$aresponse['resposta']['operacao']['operacao']];
        } else {
            return ['success'=>false,'message'=>'Retorno: '.$response];
        }
    }

    public function apiDocumentosFiducia($usuario, $operacaoId, $creditoId) {
        $docTotal = $usuario->documentos()->where(function($qq) use ($creditoId) {
            $qq->whereDoesntHave('documento_fiducia', function($q) use ($creditoId) {
                $q->where('id_solicitacao_parcelamento',$creditoId);
            })->orWhereHas('documento_fiducia', function($q) use ($creditoId) {
                $q->where('id_solicitacao_parcelamento',$creditoId)->where('enviado','!=',1);
            });
        })->where('aceite',1);

        $totalCount = $docTotal->count();

        if ($totalCount < 1) {
            return ['success'=>false,'message'=>'Nenhum documento pendente'];
        }
        $docCount = 0;
        
        foreach($docTotal->get() as $doc) {
                $curlHandler = curl_init();
                $userName = env('FIDUCIA_USER');
                $password = env('FIDUCIA_PASS');

                $arrCateg = [
                    1=>4,
                    2=>4,
                    3=>3,
                    4=>10,
                    5=>7,
                    6=>8,
                    7=>5,
                    8=>1,
                    9=>10,
                ];

                if ($doc->tipo == 'pdf') {
                    $datahead = 'data:application/pdf;base64,';
                        $postfield = '{
                        "tipo_documento": "'.mb_strtoupper($doc->tipo).'",
                        "categoria": '.$arrCateg[$doc->id_tipo_documento].',
                        "operacao": '.$operacaoId.',
                        "base64": "'.$datahead.base64_encode(Storage::get('documentos/'.$usuario->id.'/'.$doc->titulo)).'"
                    }';
                } else if ($doc->tipo == 'jpg' || $doc->tipo == 'jpeg') {
                    $basesend = null;

                    $img = Image::make(Storage::get('documentos/'.$usuario->id.'/'.$doc->titulo));
                    $img->resize(1920, 1920, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    if ($img->mime() == 'image/jpg' || $img->mime() == 'image/jpeg') {
                        $basesend = (string) $img->encode('data-url');
                    } else {
                        $basesend = (string) $img->encode('jpg',90)->encode('data-url');
                    }

                    $postfield = '{
                        "tipo_documento": "JPG",
                        "categoria": '.$arrCateg[$doc->id_tipo_documento].',
                        "operacao": '.$operacaoId.',
                        "base64": "'.$basesend.'"
                    }';
                } else {
                    return ['success'=>false,'message'=>'Formato inválido'];
                }

                $postfieldHistorico = '{
                    "tipo_documento": "'.mb_strtoupper($doc->tipo).'",
                    "categoria": '.$arrCateg[$doc->id_tipo_documento].',
                    "operacao": '.$operacaoId.',
                }';

                curl_setopt_array($curlHandler, [
                    CURLOPT_URL => env('FIDUCIA_URL_DOCUMENTOS','https://api.bancarizacao.fiducia.digital/api/v1/bancarizacao/documentos'),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD => $userName . ':' . $password,
                    /**
                     * Specify POST method
                     */
                    CURLOPT_POST => true,
                    /**
                     * Specify request headers
                     */
                    CURLOPT_HTTPHEADER => [
                        'user: '.env('FIDUCIA_HEADER')
                    ],
                    /**
                     * Specify request content
                     */
                    CURLOPT_POSTFIELDS => $postfield,
                ]);
         
                $response = curl_exec($curlHandler);
                curl_close($curlHandler);
                $aresponse = json_decode($response,1);

                $usuario->historicos()->create([
                    'id_acao_historico'=>11,
                    'descricao'=>$postfieldHistorico,
                    'valor'=>$response,
                    'datahora'=>date("Y-m-d H:i:s"),
                ]);
                
                if ($aresponse['status'] == 'sucesso') {
                    DocumentoFiducia::updateOrCreate(
                        ['id_documento'=>$doc->id,'id_solicitacao_parcelamento'=>$creditoId],
                        ['enviado'=>1]
                    );
                    $docCount++;
                }
            }

        if ($totalCount == $docCount) {
            return ['success'=>true];
        }
        return ['success'=>false,'message'=>$response];
    }

    public function importaCredito(Request $request) {
        $usuario = Usuario::find($request->id_cliente);
        $credito = $usuario->parcelamentos()->find($request->id_credito);

        set_time_limit(0);

        if ($credito->fiducia_geral == null) {
            if ($credito->fiducia_credito == 1 && $credito->fiducia_operacao != null) {
                $regDocumentos = $this->apiDocumentosFiducia($usuario,$credito->fiducia_operacao,$credito->id);
                if ($regDocumentos['success']) {
                    $credito->fiducia_documentos = 1;
                    $credito->fiducia_geral = 1;
                    $credito->save();
                    return response()->json(['success'=>true]);
                } else {
                    return response()->json(['success'=>false,'message'=>'Erro #COD1 '.$regDocumentos['message']]);
                }
            } else {
                $regCredito = $this->apiRegistroCredito($credito->valor_solicitado,$credito->valor_tac,$credito->primeira_parcela,$credito->parcelas,$credito->taxa_juros,$usuario,$credito->id);
                if ($regCredito['success'] && $regCredito['operacao']) {
                    $credito->fiducia_credito = 1;
                    $credito->fiducia_operacao = $regCredito['operacao'];
                    $credito->save();

                    $regDocumentos = $this->apiDocumentosFiducia($usuario,$regCredito['operacao'],$credito->id);
                    if ($regDocumentos['success']) {
                        $credito->fiducia_documentos = 1;
                        $credito->fiducia_geral = 1;
                        $credito->save();
                        return response()->json(['success'=>true]);
                    } else {
                        $usuario->historicos()->create([
                            'id_acao_historico'=>11,
                            'descricao'=>$regDocumentos['message'],
                            'datahora'=>date("Y-m-d H:i:s"),
                            'id_operador'=>Auth::user()->id
                        ]);
                        return response()->json(['success'=>false,'message'=>'Erro #COD2 '.$regDocumentos['message']]);
                    }
                } else {
                    $msg = '';
                    if (isset($regCredito['message'])) {
                        $msg = ' - '.$regCredito['message'];
                    }
                    return response()->json(['success'=>false,'message'=>'Erro ao importar crédito - Processo inicial'.$msg]);
                }
            }
        }
        return response()->json(['success'=>false,'message'=>'Erro ao importar crédito - Já existe um registro']);
    }

    public function cancelaCredito(Request $request) {
        try {
            $usuario = Usuario::find($request->id_cliente);
            $credito = $usuario->parcelamentos()->find($request->id_credito);
            $credito->parcelaFatura()->delete();
            $credito->delete();

            // recalcular faturas do usuário
            foreach($usuario->faturas()->get() as $fatura) {
                $this->recalculaFatura($fatura->id);
            }
            $this->recalculaLimites($usuario);
            return response()->json(['success'=>true]);
        } catch(Error $e) {
            return response()->json(['success'=>false,'message'=>'Erro ao cancelar crédito - '.$e]);    
        }
        return response()->json(['success'=>false,'message'=>'Erro ao cancelar crédito']);
    }

    private function validaDocumentos($usuario) {
        if (($usuario->opcao_documento == 'rg' && $usuario->rg_frente==1 && $usuario->rg_verso==1 && $usuario->cpf_frente==1) || ($usuario->opcao_documento == 'cnh' && $usuario->cnh_frente==1)) {
                if ($usuario->foto_doc==1 && $usuario->comprovante_residencia==1 && $usuario->extrato_bancario==1) {
                    return true;
                }
        }
        return false;
    }

    public function index() {
    	$nomeTela = 'Dashboard';
    	return view('inicial',compact('nomeTela'));
    }

    public function clientes($total=null) {
        if (!Auth()->user()->temPermissao('clientes','acesso')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
    	$nomeTela = 'Clientes';
        $columns = [
            ['name'=>'nome_completo'],
            ['name'=>'cpf'],
            ['name'=>'renda_comprovada','render'=>'function(data,type,row){return \'R$ \'+moeda.formatarx(parseFloat(data));}'],
            ['name'=>'credito_aprovado','render'=>'function(data,type,row){return \'R$ \'+moeda.formatarx(parseFloat(data));}'],
            ['name'=>'limite_total','render'=>'function(data,type,row){return \'R$ \'+moeda.formatarx(parseFloat(data));}'],
            ['name'=>'limite_disponivel','render'=>'function(data,type,row){return \'R$ \'+moeda.formatarx(parseFloat(data));}'],
        ];

        $combos = [
            'status'=>[
                0=>"Cadastro Incompleto",
                1=>"Pendente Documentação",
                2=>"Análise Documentação",
                3=>"Cadastro Completo",
                4=>"Cadastro Bloqueado",
                5=>"Recusa de Crédito",
                6=>"Bloqueio Falta Pgto",
            ],
            'status_fatura'=>[
                0=>"Sem Fatura",
                1=>"Fatura Fechada",
                2=>"Fatura Emitida",
                3=>"Fatura Atrasada",
                4=>"Fatura Paga",
                5=>"Fatura Aberta",
            ],
            'fiducia'=>[
                0=>'Sem Solciitação',
                1=>'Envio Pendente',
            ],
        ];

        $totp = Usuario::count();
        $totp = round($totp/$this->maxrecord);
        $mtotal = ($total) ? $total : 0;
        if ($total) {
            $total = $total * $this->maxrecord;
        }

    	return view('clientes',compact('nomeTela','columns','combos','total','mtotal','totp'));
    }

    public function pegaClientes(Request $request,$total=0) {
        $usuarios = Usuario::with('fatura')->withCount(['parcelamentos as fiducia'=>function($query){
            $query->whereNull('fiducia_geral');
        }])->where('status','!=',7);
        $quick = DataTables::of($usuarios)->filterColumn('fiducia', function($query, $keyword) {
            $query->whereHas('parcelamentos',function($query){
                $query->whereNull('fiducia_geral');
            },'=',$keyword);
        })->addColumn('status_fatura', function($item) {
            $status_fatura = 0;
            if ($item->fatura) {
                if ($item->fatura->fechado) {
                    $status_fatura = 1;
                    $now = Carbon::now()->toDateString();
                    $end_date = $item->fatura->vencimento;
                    if (\DateTime::createFromFormat('Y-m-d H:i:s', $end_date) !== false) {
                        if ($item->fatura->url) {
                            $status_fatura = 2;
                        } else if ($end_date->diffInDays($now) >= -2) {
                            $status_fatura = 3;
                        } elseif ($item->fatura->pago) {
                            $status_fatura = 4;
                        }
                    }
                } else {
                    $status_fatura = 5;
                }
            }
            return $status_fatura;
        })->filterColumn('status_fatura',function($query, $keyword){
            switch($keyword){
                case 5:
                    $query->whereHas('fatura',function($query){
                        $query->where(function($query){
                            $query->whereNull('fechado')->orWhere('fechado',0);
                        });
                    });
                break;
                case 4:
                    $query->whereHas('fatura',function($query){
                        $query->where('fechado',1)->whereNotNull('vencimento')->whereNull('url')->where('pago',1);
                    });
                break;
                case 3:
                    $query->whereHas('fatura',function($query){
                        $query->where('fechado',1)->whereDate('vencimento','>=',now()->subDays(2))->whereNull('url');
                    });
                break;
                case 2:
                    $query->whereHas('fatura',function($query){
                        $query->where('fechado',1)->whereNotNull('vencimento')->whereNotNull('url');
                    });
                break;
                case 1:
                    $query->whereHas('fatura',function($query){
                        $query->where('fechado',1)->whereNull('vencimento');
                    });
                break;
                case 0:
                    $query->doesntHave('fatura');
                break;
                default:
            }
        });
        return $quick->make(true);
    }

    public function perguntas() {
        if (!Auth()->user()->temPermissao('faq','acesso')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        $categs = Pergunta::where('type','C')->orderBy('order')->orderBy('parent')->get();
        $scategs = Pergunta::where('type','S')->orderBy('parent')->get();
        $pergs = Pergunta::whereNull('parent')->get();
        $nomeTela = 'FAQ Aplicativo';
        $columns = [
            ['name'=>'pergunta','render'=>"function(data,type,row){
                console.log(row)
                if (row.type == 'S') {
                    return '<strong>'+data+'<strong>'
                } else {
                    return data
                }
            }"],
            ['name'=>'resposta'],
            //['name'=>'parentid'],
        ];
        return view('perguntas',compact('nomeTela','columns','categs','scategs','pergs'));
    }

    public function perguntasApp() {
        $arrfaq = [];
        $categs = Pergunta::where('type','C')->orderBy('order')->orderBy('parent')->get();
        foreach($categs as $categ) {

            $perguntas = DB::select("(SELECT id, pergunta, resposta, type, id AS parentid FROM faq WHERE type IS NULL AND parent = ?)
            UNION
            (SELECT id, pergunta, resposta, type, parent AS parentid FROM faq WHERE type='S' AND parent IN (SELECT id FROM faq WHERE type IS NULL AND parent = ?))
            ORDER BY parentid, type DESC",[$categ->id,$categ->id]);

            $arrfaq[$categ->id] = [
                'nome'=>$categ->pergunta,
                'perguntas'=>$perguntas,
            ];
        }
        return view('faq',compact('arrfaq'));
    }

    public function perguntasAppJson() {
        $arrfaq = [];
        $categs = Pergunta::where('type','C')->orderBy('order')->orderBy('parent')->get();
        foreach($categs as $categ) {

            $perguntas = DB::select("(SELECT id, pergunta, resposta, type, id AS parentid FROM faq WHERE type IS NULL AND parent = ?)
            UNION
            (SELECT id, pergunta, resposta, type, parent AS parentid FROM faq WHERE type='S' AND parent IN (SELECT id FROM faq WHERE type IS NULL AND parent = ?))
            ORDER BY parentid, type DESC",[$categ->id,$categ->id]);

            $arrfaq[$categ->pergunta] = [
                'perguntas'=>$perguntas
            ]; 
        }
        
        return response()->json($arrfaq);
    }

    public function pergunta($perguntaId) {
        $pergunta = Pergunta::find($perguntaId);
        $nomeTela = 'FAQ Aplicativo - '.$pergunta->pergunta;
        return view('pergunta',compact('pergunta','nomeTela'));
    }

    public function pegaPerguntas($parent) {
        $perguntas = DB::select("(SELECT id, pergunta, resposta, type, id AS parentid FROM faq WHERE type IS NULL AND parent = ?)
            UNION
            (SELECT id, pergunta, resposta, type, parent AS parentid FROM faq WHERE type='S' AND parent IN (SELECT id FROM faq WHERE type IS NULL AND parent = ?))
            ORDER BY parentid, type DESC",[$parent,$parent]);
        $quick = DataTables::of($perguntas);
        return $quick->make(true);
    }

    public function postPergunta(Request $request) {
        $record = $request->all();
        unset($record['_token']);
        if ($request->id) {
            $pergunta = Pergunta::find($request->id);
            $pergunta->update($record);
        } else {
            $pergunta = new Pergunta;
            $pergunta->insert($record);
        }
        return redirect()->route('admin.perguntas');
    }

    public function taxas() {
        if (!Auth()->user()->temPermissao('taxas','acesso')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        $taxas_multi = Taxa::where('slug','!=','taxaint')->get();
        $taxas_int = Taxa::where('slug','taxaint')->get();
        $arrtaxas = [
            "taxa_core"=>"Taxa Core (%)",
            "taxa_intermedia"=>"Taxa Intermediário (%)",
            "taxa_cardholder"=>"Taxa Cardholder (%)",
            "tarifa_core"=>"Tarifa Core (R$)",
            "tarifa_serasa"=>"Tarifa Serasa (R$)",
            "taxam_iof"=>"Taxa Mensal IOF (%)",
            "taxad_iof"=>"Taxa Diária IOF (%)",
            "taxa_imp_tac"=>"Taxa Imposto TAC (%)",
            "taxa_simples_nac"=>"Taxa Simples Nacional (%)",
            "custos_fixo_bdi"=>"Custos Fixos BDI (%)",
            "juros_padrao"=>"Taxa Juros Padrão - Todos Clientes (%)",
        ];
        $nomeTela = 'Taxas e Tarifas';
        return view('taxas',compact('nomeTela','taxas_multi','taxas_int','arrtaxas'));
    }

    public function postTaxas(Request $request) {
        $record = $request->all();
        $arrtxint = $record['taxaint'];
        unset($record['taxaint']);
        unset($record['_token']);
        foreach($record as $name=>$val) {
            $taxa = Taxa::where('slug',$name)->first();
            if ($taxa->type == 'perc') {
                $taxa->update(['valor'=>$this->fNum($val)/100]);
            } else {
                $taxa->update(['valor'=>$this->fNum($val)]);
            }
        }

        // taxaint
        foreach($arrtxint as $kint=>$vint) {
            $taxa = Taxa::where('slug','taxaint')->where('nparcela',$kint)->first();
            $taxa->update(['valor'=>$this->fNum($vint)/100]);
        }

        return redirect()->route('admin.taxas');
    }

    public function pegaCliente($clienteId) {
        $usuario = Usuario::where('id',$clienteId)->where('status','!=',7)->first();
        if ($usuario->taxa_juros) {
            $usuario->taxa_juros = number_format($usuario->taxa_juros*100,2,',','');
        }
        return response()->json($usuario);
    }

    public function pegaFaturas($clienteId) {
        $usuario = Usuario::find($clienteId);
        $quick = DataTables::of($usuario->faturas);
        return $quick->make(true);
    }

    public function carregaFatura($clienteId,$faturaId) {
        $usuario = Usuario::find($clienteId);
        $fatura = $usuario->faturas()->where('id',$faturaId)->with('parcelas')->first();
        return view('modals.fatura',compact('fatura','faturaId','clienteId'));
    }

    public function pegaCreditos($clienteId) {
        $usuario = Usuario::find($clienteId);
        $quick = DataTables::of($usuario->parcelamentos);
        return $quick->make(true);
    }

    public function carregaCreditos($clienteId) {
        return view('modals.creditos',compact('clienteId'));
    }

    public function carregaCobranca($clienteId,$faturaId) {
        return view('forms.cobranca',compact('clienteId','faturaId'));
    }

    public function registraCobranca(Request $request) {
        
    }

    public function pegaDocumentos($clienteId) {
        $arrdocs = [];

        $usuario = Usuario::find($clienteId);
        $docs = $usuario->documentos()->with('tipo_documento')->orderBy('created_at','desc')->get();
        foreach($docs as $doc) {
            if ($doc->id_tipo_documento == 6) {
                $arrdocs[$doc['nome']][$doc['id_tipo_documento']][] = [
                    'id' => $doc->id,
                    'titulo' => $doc->titulo,
                    'tipo' => $doc->tipo,
                    'tipo_documento' => $doc->tipo_documento->descricao,
                    'aceite'=>$doc->aceite,
                ];
            } else {
                $arrdocs[$doc['nome']][$doc['id_tipo_documento']] = [
                    'id' => $doc->id,
                    'titulo' => $doc->titulo,
                    'tipo' => $doc->tipo,
                    'tipo_documento' => $doc->tipo_documento->descricao,
                    'aceite'=>$doc->aceite,
                ];
            }
        }
        
        $records = [];
        foreach($arrdocs as $grupo) {
            foreach($grupo as $tdoc=>$doc) {
                if ($tdoc == 6) {
                    $arquivos = '';

                    foreach($doc as $ksub=>$sub) {
                        if ($ksub > 0) {
                            $arquivos .= '<br>';
                        }
                        $arquivos .= $sub['titulo'].' <a target="_blank" href="'.URL::to('/').'/cliente/'.$clienteId.'/documento/'.$sub['id'].'">ver <i class="fas fa-external-link-alt"></i></a>';
                    }

                    $records[] = ['id'=>$doc[0]['id'],'documento'=>$doc[0]['tipo_documento'],'arquivo'=>$arquivos,'formato'=>$doc[0]['tipo'],'aceite'=>$doc[0]['aceite']];
                } else {
                    $records[] = ['id'=>$doc['id'],'documento'=>$doc['tipo_documento'],'arquivo'=>$doc['titulo'].' <a target="_blank" href="'.URL::to('/').'/cliente/'.$clienteId.'/documento/'.$doc['id'].'">ver <i class="fas fa-external-link-alt"></i></a>','formato'=>$doc['tipo'],'aceite'=>$doc['aceite']];
                }
            }
        }

        $quick = DataTables::of($records)->escapeColumns('arquivo');
        return $quick->make(true);
    }

    public function pegaHistorico($clienteId) {
        $usuario = Usuario::find($clienteId);

        $arrhistorico = [];

        $h_geral = $usuario->historicos()->with('operador','usuario','acao')->get();
        foreach($h_geral as $hg) {
            $operador = null;
            if ($hg->id_operador) {
                $operador = $hg->operador->nome;
            } else if ($hg->id_usuario) {
                $operador = $hg->usuario->nome;
                //$operador = 'Tomador';
            }

            if (in_array($hg->id_acao_historico,[10,11,12])) {
                $arrhistorico[] = [
                    'id_acao_historico'=>$hg->id_acao_historico,
                    'datahora'=>$hg->datahora,
                    'operador'=>$operador,
                    'acao'=>$hg->acao->descricao,
                    'descricao'=>base64_encode($hg->descricao),
                    'valor'=>base64_encode($hg->valor),
                ];
            } else {
                $arrhistorico[] = [
                    'id_acao_historico'=>$hg->id_acao_historico,
                    'datahora'=>$hg->datahora,
                    'operador'=>$operador,
                    'acao'=>$hg->acao->descricao,
                    'descricao'=>$hg->descricao,
                    'valor'=>$hg->valor,
                ];
            }
        }

        foreach($usuario->revisionHistory as $hu) {
            $operador = null;

            if ($hu->user_type == 'App\Operador') {
                $operador = Operador::find($hu->user_id);
                if ($operador) {
                    $operador = $operador->nome;
                }
            } else if ($hu->user_type == 'App\Usuario') {
                $operador = Usuario::find($clienteId)->nome_completo;
                //$operador = 'Tomador';
            }

            if ($hu->oldValue() && !in_array($hu->fieldName(), ['status','cadastro_finalizado']) && ($hu->oldValue()!=$hu->newValue()) && $hu->id_usuario = $usuario->id) {

                // if (in_array($hu->fieldName(), ['nome_completo','cep','endereco','numero','complemento'])) {
                //     try{
                //         $newvalue = decrypt($hu->newValue());
                //     }catch(DecryptException $e){
                //         $newvalue = $hu->newValue();
                //     }
                // } else {
                //     $newvalue = $hu->newValue();
                // }

                $arrhistorico[] = [
                    'datahora'=>$hu->created_at->format('Y-m-d H:i:s'),
                    'operador'=>$operador,
                    'acao'=>'Alterou Cadastro',
                    'descricao'=>$hu->fieldName(),
                    'valor'=>'"'.$hu->oldValue().'" para "'.$hu->newValue().'"',
                ];
            }
        }

        $quick = DataTables::of($arrhistorico);
        return $quick->make(true);
    }

    public function aceiteDocumento(Request $request, $clienteId) {
        $usuario = Usuario::find($clienteId);
        DB::beginTransaction();
        if ($usuario) {
            $doc = $usuario->documentos()->where('id',$request->id_documento)->first();
            $doc->update(['aceite'=>$request->aceite]);
            
            if ($request->aceite && $doc->id < 8) {
                $usuario->{$doc->tipo_documento->slug} = $request->aceite;
            }

            $usuario->historicos()->create([
                'id_acao_historico'=>3,
                'descricao'=>$doc->tipo_documento->slug.' - '.($request->aceite)?'Sim':'Não',
                'datahora'=>date("Y-m-d H:i:s"),
                'id_operador'=>Auth::user()->id
            ]);
            
            // validar se todos os documentos foram enviados e aceitos
            if ($this->validaDocumentos($usuario)) {
                $usuario->cadastro_finalizado = 1;
                $usuario->status = 3;

                // e-mail de aprovação
                Notification::send($usuario, new Approved($usuario));
                $usuario->historicos()->create([
                    'id_acao_historico'=>7,
                    'datahora'=>date("Y-m-d H:i:s"),
                    'id_operador'=>Auth::user()->id
                ]);
            }

            $usuario->save();

            $btImportaDoc = $this->validaDocumentos($usuario);

            DB::commit();
            return response()->json(['success'=>true,'btImportaDoc'=>$btImportaDoc]);
        }
    }

    public function dwnDocumento($clienteId,$documentoId) {
        if (Documento::where('id_usuario',$clienteId)->where('id',$documentoId)->exists()) {
            $filename = Documento::where('id_usuario',$clienteId)->where('id',$documentoId)->first()->titulo;
            return Storage::download('documentos/'.$clienteId.'/'.$filename);
        }
        return false;
    }

    public function pegaDispoCreditos($clienteId) {
        $usuario = Usuario::find($clienteId);
        $quick = DataTables::of($usuario->historicos()->with('operador')->where('id_acao_historico',1)->get());
        return $quick->make(true);
    }

    public function postAcao($clienteId, Request $request) {
        $usuario = Usuario::find($clienteId);

        $acoes = [
            'CONV'=>'Liberar Convite',
            'LIB'=>'Liberar Cadastro',
            'RCAD'=>'Recusa/Bloqueio de Cadastro',
            'RCRE'=>'Recusa de Crédito',
            'BPAG'=>'Bloqueio por Falta de Pagamento',
            'DEL'=>'Excluir conta do cliente',
            'LCAD'=>'Liberar Cadastro',
            'DEL'=>'Excluir conta do cliente',
        ];

        if ($request->acao_motivo) {
            $usuario->id_motivo = $request->acao_motivo;
        }

        if ($request->acao_executar == 'LCAD') {
            if ($this->validaDocumentos($usuario)) {
                $usuario->status = 3;
                $usuario->cadastro_finalizado = 1;
                $usuario->save();
                Notification::send($usuario, new Approved($usuario));
            } else {
                $usuario->status = 2;
                $usuario->save();
            }
        } else if ($request->acao_executar == 'CONV') {
            $usuario->aceito = 1;
            $usuario->passo_cadastro = 1;
            $usuario->status = 1;
            $usuario->save();

            Notification::send($usuario, new Invited($usuario));
        } else if ($request->acao_executar == 'LIB') {
            $usuario->status = 3;
            $usuario->cadastro_finalizado = 1;
            $usuario->save();
        } else if ($request->acao_executar == 'RCAD') {
            $usuario->status = 4;
            $usuario->cadastro_finalizado = null;
            $usuario->save();
            Notification::send($usuario, new Unregister($usuario));
        } else if ($request->acao_executar == 'RCRE') {
            $usuario->status = 5;
            $usuario->save();
            Notification::send($usuario, new Rejected($usuario));
        } else if ($request->acao_executar == 'BPAG') {
            $usuario->status = 6;
            $usuario->save();
            Notification::send($usuario, new Blocked($usuario));
        } else if ($request->acao_executar == 'DEL') {
            $randomHash = md5(rand(0,9999));
            $usuario->nome_completo = $randomHash;
            $usuario->whatsapp = $randomHash;
            $usuario->cpf = $randomHash;
            $usuario->rg = $randomHash;
            $usuario->cep = $randomHash;
            $usuario->endereco = $randomHash;
            $usuario->numero = $randomHash;
            $usuario->complemento = $randomHash;
            $usuario->email = $randomHash;
            $usuario->password = $randomHash;
            $usuario->banco = 0;
            $usuario->agencia = 0;
            $usuario->numero_conta = 0;
            $usuario->dv_conta = 0;
            $usuario->status = 7;
            $usuario->save();

            // apagar documentos do usuário

            foreach ($usuario->documentos()->get() as $doc) {
                unlink('../storage/app/documentos/'.$usuario->id.'/'.$doc->titulo);
                $doc->delete();
            }

        }

        // historico
        $usuario->historicos()->create([
            'id_acao_historico'=>8,
            'valor'=>$acoes[$request->acao_executar],
            'datahora'=>date("Y-m-d H:i:s"),
            'id_operador'=>Auth::user()->id
        ]);

        return response()->json(['success'=>true]);
    }

    public function cliente($clienteId) {
        
        

    	// timeline cadastro
        $usuario = Usuario::where('id',$clienteId)->where('status','!=',7)->first();

        if (!$usuario) {
            return redirect('/admin/clientes');
        }

        $acoes = [];

        if ($usuario->status <= 3) {
            $acoes = [
                'LIB'=>'Liberar Cadastro',
                'CONV'=>'Liberar Convite',
                'RCAD'=>'Recusa/Bloqueio de Cadastro',
                'RCRE'=>'Recusa de Crédito',
                'BPAG'=>'Bloqueio por Falta de Pagamento',
                'DEL'=>'Excluir conta do cliente',
            ];
        } else if ($usuario->status >= 4){
            $acoes = [
                'LCAD'=>'Liberar Cadastro',
                'DEL'=>'Excluir conta do cliente',
            ];
        }

    	$timeline = [];
        $timeline[] = [
            'link'=>'cadastro',
            'action'=>'Cadastro',
            'datetime'=>$usuario->created_at->format('Y-m-d H:i:s'),
            'bg'=>'bg-success',
            'color'=>'green',
            'icon'=>'fas fa-pencil-alt'
        ];
        if (count($usuario->documentos) > 0) {
            $timeline[] = [
                'link'=>'documentos',
                'action'=>'Envio Documentos',
                'datetime'=>$usuario->documentos->first()->created_at->format('Y-m-d H:i:s'),
                'bg'=>'bg-warning',
                'color'=>'orange',
                'icon'=>'fas fa-file-alt'
            ];
        }
        if (count($usuario->parcelamentos) > 0) {
            foreach($usuario->parcelamentos as $parcelamento) {
                $timeline[] = [
                    'link'=>'creditos',
                    'action'=>'Solicitação de Crédito',
                    'datetime'=>$parcelamento->created_at->format('Y-m-d H:i:s'),
                    'bg'=>'bg-primary',
                    'color'=>'blue',
                    'icon'=>'fas fa-dollar-sign'
                ];
            }
        }
        if (count($usuario->faturas) > 0) {
            foreach($usuario->faturas as $fatura) {
                $timeline[] = [
                    'link'=>'faturas',
                    'action'=>'Geração Fatura',
                    'datetime'=>$fatura->created_at->format('Y-m-d H:i:s'),
                    'bg'=>'bg-purple',
                    'color'=>'purple',
                    'icon'=>'fas fa-file-invoice'
                ];
            }
        }

        usort($timeline, function($a, $b) {
            return $a['datetime'] <=> $b['datetime'];
        });

        $motivos = Motivo::all();

        $btImportaDoc = $this->validaDocumentos($usuario);

        $tipoDocumento = TipoDocumento::all();

    	$nomeTela = 'Cliente';
    	return view('cliente',compact('nomeTela','timeline','clienteId','acoes','motivos','btImportaDoc','tipoDocumento'));
    }

    public function bloqueioCliente(Request $request) {
        $cliente = Usuario::find($request->id_usuario);
        if ($cliente->status == 4) {
            $cliente->status = ($cliente->cadastro_finalizado == 1) ? 3 : 2;
        } else {
            $cliente->status = 4;
        }
        $cliente->save();

        return response()->json(['success'=>true]);
    }

    // forms

    public function formCredito(Request $request) {
        if (Auth()->user()->temPermissao('creditos','leitura')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        if ($request->id_usuario) {
            $usuario = Usuario::find($request->id_usuario);
            $request->credito_aprovado = $this->fNum($request->credito_aprovado);
            if ($usuario->credito_aprovado != $request->credito_aprovado) {
                DB::beginTransaction();
                $usuario->update([
                    'credito_aprovado' => $request->credito_aprovado,
                    // 'limite_total' => $request->credito_aprovado,
                    // 'limite_disponivel' => $request->credito_aprovado - $usuario->limite_utilizado
                ]);
                $usuario->historicos()->create([
                    'id_acao_historico'=>1,
                    'valor'=>$request->credito_aprovado,
                    'datahora'=>date("Y-m-d H:i:s"),
                    'id_operador'=>Auth::user()->id
                ]);
            }
            //$this->recalculaLimites($usuario);
            $request->limite_utilizado = $this->fNum($request->limite_utilizado);
            $request->limite_utilizado_orig = $this->fNum($request->limite_utilizado_orig);
            if ($request->limite_utilizado != $request->limite_utilizado_orig) {
                $usuario->limite_utilizado = $request->limite_utilizado=="" ?'0':$request->limite_utilizado;
                //$usuario->limite_disponivel = $usuario->limite_disponivel - $usuario->limite_utilizado;
            }
            $request->limite_disponivel = $this->fNum($request->limite_disponivel);
            $request->limite_disponivel_orig = $this->fNum($request->limite_disponivel_orig);
            if ($request->limite_disponivel != $request->limite_disponivel_orig) {
                $usuario->limite_disponivel = $request->limite_disponivel==''?'0':$request->limite_disponivel;
            }
            $usuario->limite_total = $this->fNum($request->limite_total);
            $usuario->save();
            $usuario->historicos()->create([
                'id_acao_historico'=>15,
                'valor'=>$request->limite_disponivel,
                'datahora'=>date("Y-m-d H:i:s"),
                'id_operador'=>Auth::user()->id
            ]);
            if ($request->taxa_juros) {
                $juros = $this->fNum($request->taxa_juros);
                if ($juros <= 15) {
                    $usuario->taxa_juros = $juros/100;
                    $usuario->save();
                }
            }
            //$this->recalculaLimites($usuario);
            DB::commit();
            return response()->json(['success'=>true]);
        }
    }

    public function formConvite(Request $request) {
        if (Auth()->user()->temPermissao('cadastro','leitura')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        DB::beginTransaction();
        $usuario = Usuario::create([
            'nome_completo'=>$request->nome_completo,
            'email'=>trim($request->email),
            'password'=>$request->password,
            'whatsapp'=>trim($request->whatsapp),
            'cpf'=>trim($request->cpf),
            'aceito'=>1,
            'passo_cadastro'=>1,
            'status'=>1,
        ]);
        DB::commit();
        Notification::send($usuario, new Invited($usuario));
        return response()->json(['success'=>true]);
    }

    public function formPessoais(Request $request) {
        if (Auth()->user()->temPermissao('cadastro','leitura')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        DB::beginTransaction();
        if ($request->id_usuario) {
            $usuario = Usuario::find($request->id_usuario);
            if (isset($request->password)) {
                if ($request->password == $request->rep_password) {
                    $dados = [
                        'nome_completo'=>$request->nome_completo,
                        'nome_completo_pai'=>$request->nome_completo_pai,
                        'nome_completo_mae'=>$request->nome_completo_mae,
                        'email'=>trim($request->email),
                        'password'=>$request->password,
                        'whatsapp'=>trim($request->whatsapp),
                        'aceito'=>$request->aceito,
                        'cpf'=>trim($request->cpf),
                        'rg'=>$request->rg,
                        'rg_orgao'=>$request->rg_orgao,
                        'rg_uf'=>$request->rg_uf,
                        'rg_dtemissao'=>$request->rg_dtemissao,
                        'sexo'=>$request->sexo,
                        'data_nascimento'=>$request->data_nascimento,
                        'estado_civil'=>$request->estado_civil,
                        'escolaridade'=>$request->escolaridade,
                        'nacionalidade'=>$request->nacionalidade,
                        'habita_estado'=>$request->habita_estado,
                        'habita_cidade'=>$request->habita_cidade,
                    ];
                } else {
                    return response()->json(['success'=>false,'message'=>'Senhas digitadas não batem!']);
                }
            } else {
                $dados = [
                    'nome_completo'=>$request->nome_completo,
                    'nome_completo_pai'=>$request->nome_completo_pai,
                    'nome_completo_mae'=>$request->nome_completo_mae,
                    'email'=>trim($request->email),
                    'whatsapp'=>trim($request->whatsapp),
                    'aceito'=>$request->aceito,
                    'cpf'=>trim($request->cpf),
                    'rg'=>$request->rg,
                    'rg_orgao'=>$request->rg_orgao,
                    'rg_uf'=>$request->rg_uf,
                    'rg_dtemissao'=>$request->rg_dtemissao,
                    'sexo'=>$request->sexo,
                    'data_nascimento'=>$request->data_nascimento,
                    'estado_civil'=>$request->estado_civil,
                    'escolaridade'=>$request->escolaridade,
                    'nacionalidade'=>$request->nacionalidade,
                    'habita_estado'=>$request->habita_estado,
                    'habita_cidade'=>$request->habita_cidade,
                ];
            }
            $usuario->update($dados);
            // $usuario->historicos()->create([
            //     'id_acao_historico'=>6,
            //     'descricao'=>'Dados Pessoais',
            //     'datahora'=>date("Y-m-d H:i:s"),
            //     'id_operador'=>Auth::user()->id
            // ]);
            DB::commit();
            return response()->json(['success'=>true]);
        }
    }

    public function formEndereco(Request $request) {
        if (Auth()->user()->temPermissao('cadastro','leitura')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        DB::beginTransaction();
        if ($request->id_usuario) {
            $usuario = Usuario::find($request->id_usuario);
            $dados = [
                'cep'=>$request->cep,
                'endereco'=>$request->endereco,
                'numero'=>$request->numero,
                'complemento'=>$request->complemento,
                'bairro'=>$request->bairro,
                'cidade'=>$request->cidade,
                'estado'=>$request->estado,
                'moradia'=>$request->moradia,
                'morapais'=>$request->morapais,
            ];
            $usuario->update($dados);
            $usuario->historicos()->create([
                'id_acao_historico'=>6,
                'descricao'=>'Endereço',
                'datahora'=>date("Y-m-d H:i:s"),
                'id_operador'=>Auth::user()->id
            ]);
            DB::commit();
            return response()->json(['success'=>true]);
        }
    }

    public function formFinanceiro(Request $request) {
        if (Auth()->user()->temPermissao('cadastro','leitura')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        DB::beginTransaction();
        if ($request->id_usuario) {
            $usuario = Usuario::find($request->id_usuario);
            $request->renda_comprovada = $this->fNum($request->renda_comprovada);
            $dados = [
                'vence_fatura'=>$request->vence_fatura,
                'profissao'=>$request->profissao,
                'ocupacao'=>$request->ocupacao,
                'renda_comprovada'=>$request->renda_comprovada,
                'credito_aprovado'=>$request->renda_comprovada*0.3,
                'restritivo'=>$request->restritivo,
                'banco'=>$request->banco,
                'agencia'=>$request->agencia,
                'dv_agencia'=>$request->dv_agencia,
                'numero_conta'=>$request->numero_conta,
                'dv_conta'=>$request->dv_conta,
            ];
            $usuario->update($dados);
            $usuario->historicos()->create([
                'id_acao_historico'=>6,
                'descricao'=>'Dados Financeiro',
                'datahora'=>date("Y-m-d H:i:s"),
                'id_operador'=>Auth::user()->id
            ]);
            DB::commit();
            return response()->json(['success'=>true]);
        }
    }

    public function enviodoc(Request $request) {
        DB::beginTransaction();
        $tdoc = TipoDocumento::find($request->tipo);
        if ($request->file('arquivo')) {
            $ftime = date("U");
            $ext = $request->file('arquivo')->getClientOriginalExtension();
            $fname = $tdoc->slug."_".$ftime.".".$ext;
            $path = Storage::putFileAs("documentos/".$request->cliente_id, $request->file('arquivo'),$fname);

            if (in_array($tdoc->id,[1,2])) {
                Usuario::find($request->cliente_id)->update(['opcao_documento'=>'rg']);
            } else if ($tdoc->id == 7) {
                Usuario::find($request->cliente_id)->update(['opcao_documento'=>'cnh']);
            }

            $doc = Documento::create([
                'nome'=>$ftime,
                'id_usuario'=>$request->cliente_id,
                'titulo' => $fname,
                'tipo' => $ext,
                'id_tipo_documento'=>$tdoc->id,
                'aceite'=>0,
            ]);
        }
        DB::commit();
        return response()->json(['success'=>true,'documento'=>$doc]);
    }

    public function formFatura(Request $request) {
        if (Auth()->user()->temPermissao('faturas','leitura')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        DB::beginTransaction();
        if ($request->id_usuario) {
            $usuario = Usuario::find($request->id_usuario);
            $fatura = $usuario->faturas()->where('id',$request->id_fatura)->first();
            $updateFatura = [];
            if ($request->fatura_digitos && $request->file('fatura_file')) {
                if ($request->file('fatura_file')) {
                    $path = Storage::putFile('/public/faturas/'.$request->id_usuario, $request->file('fatura_file'));
                    $path = str_replace('public','storage/app/public',$path);
                    $fatura->update([
                        'url'=>$path,
                        'digitos'=>$request->fatura_digitos,
                        'status'=>1,
                        'fechado'=>1,
                    ]);
                    
                    $usuario->historicos()->create([
                        'id_acao_historico'=>9,
                        'datahora'=>date("Y-m-d H:i:s"),
                        'id_operador'=>Auth::user()->id
                    ]);

                    //Notification::send($usuario, new ClosedInvoice($usuario,$fatura));
                    Mail::to($usuario->email)->send(new ClosedInvoice($usuario,$fatura));
                    DB::commit();
                    return response()->json(['success'=>true]);
                } else if (!$fatura->url && !$fatura->digitos) {
                    return response()->json(['success'=>false,'message'=>'É necessário o PDF/Digitos do Boleto para alteração de status']);
                }
            }
            if ($request->status) {
                if (in_array($request->status, [1,2]) && !$fatura->digitos && !$fatura->url) {
                    return response()->json(['success'=>false,'message'=>'Status Emitido ou Pago apenas com o PDF e digitos da fatura cadastrada.']);
                }
                
                $updateFatura['status'] = $request->status;
                if ($request->status == 1) {
                    $updateFatura['fechado'] = 1;
                }
                if ($request->status == 2) {
                    $fatura->update([
                        'dtpagamento'=>date("Y-m-d"),
                        'status'=>$request->status,
                        'pago'=>1,
                        'fechado'=>1,
                    ]);

                    // pagar todas as parcelas de crédito
                    foreach($fatura->parcelas as $prc) {
                        if($prc->parcela_type == 'App\SolicitacaoParcelamento') {
                            $prc->pago = 1;
                            $prc->save();
                        }
                    }

                    $this->recalculaLimites($usuario);
                    /*$usuario->limite_disponivel = $usuario->limite_disponivel + $fatura->valor_total;
                    $usuario->limite_utilizado = $usuario->limite_utilizado - $fatura->valor_total;*/
                    $usuario->save();
                    Notification::send($usuario, new Receipt($usuario,$fatura));
                }
                $fatura->update($updateFatura);
                DB::commit();
                
                return response()->json(['success'=>true,'type'=>'status']);
            }
        }
    }

    public function formCobranca(Request $request) {
        if (Auth()->user()->temPermissao('faturas','leitura')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        DB::beginTransaction();
        if ($request->id_usuario) {
            $usuario = Usuario::find($request->id_usuario);
            $fatura = $usuario->faturas()->where('id',$request->id_fatura)->first();
            
            $valorcobranca = $this->fNum($request->valor);

            if ($request->tipo == 'C') {
                $usuario->limite_disponivel = $usuario->limite_disponivel + $valorcobranca;
                $valorcobranca = $valorcobranca * -1;
                $usuario->save();
            }

            $cobranca = Cobranca::create([
                'descricao'=>$request->descricao,
                'tipo'=>$request->tipo,
                'valor'=>$valorcobranca,
                'reg_date'=>$this->fData($request->reg_date),
            ]);

            $cobranca->parcela()->create([
                'id_usuario'=>$usuario->id,
                'id_fatura'=>$fatura->id,
            ]);

            $this->recalculaFatura($fatura->id);
            $fatura->save();

            $usuario->historicos()->create([
                'id_acao_historico'=>9,
                'descricao'=>'Acr./Dec. Fatura',
                'valor'=>$valorcobranca,
                'datahora'=>date("Y-m-d H:i:s"),
                'id_operador'=>Auth::user()->id
            ]);

            DB::commit();

            return response()->json(['success'=>true]);
        }
    }

    public function apagarCobranca(Request $request) {

        DB::beginTransaction();

        $parcela = ParcelasFatura::with('fatura','usuario','cobranca')->find($request->id_parcela);
        if ($parcela->parcela_type == 'App\Cobranca') {
            $fatura = $parcela->fatura;
            $this->recalculaFatura($fatura->id);
            $parcela->cobranca()->delete();
            $parcela->delete();
            DB::commit();

            if ($parcela->cobranca->tipo == 'C') {
                $usuario = $parcela->usuario;
                $usuario->limite_disponivel = $usuario->limite_disponivel + $parcela->cobranca->valor;
                $usuario->save();
            }

            return response()->json(['success'=>true]);
        }

        return response()->json(['success'=>false]);
    }

    public function motivos() {
        if (!Auth()->user()->temPermissao('motivos','acesso')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        $nomeTela = 'Motivos';
        $columns = [
            'nome'=>'Motivo',
        ];
        return view('motivos',compact('nomeTela','columns'));
    }

    public function formMotivo(Request $request) {
        if (Auth()->user()->temPermissao('motivos','leitura')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        DB::beginTransaction();
        if ($request->id) {
            $motivo = Motivo::find($request->id);
            $motivo->update([
                'nome'=>$request->nome,
                'mensagem'=>$request->mensagem,
            ]);
        } else {
            Motivo::create([
                'nome'=>$request->nome,
                'mensagem'=>$request->mensagem,
            ]);
        }
        DB::commit();
        return response()->json(['success'=>true]);
    }

    public function pegaMotivos() {
        $items = Motivo::all();
        $quick = DataTables::of($items);
        return $quick->make(true);
    }

    public function pegaMotivo($id) {
        $item = Motivo::find($id);
        return response()->json(['status'=>'success','item'=>$item]);
    }

    public function operadores() {
        if (!Auth()->user()->temPermissao('operadores','acesso')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        $nomeTela = 'Operadores';
        $columns = [
            'nome'=>'Nome',
            'email'=>'E-mail',
        ];
        $telas = [
            'clientes'=>'Clientes',
            'cadastro'=>' <i class="fas fa-caret-right"></i> Cadastro',
            'documentos'=>' <i class="fas fa-caret-right"></i> Documentos',
            'creditos'=>' <i class="fas fa-caret-right"></i> Créditos',
            'faturas'=>' <i class="fas fa-caret-right"></i> Faturas',
            'acoes'=>' <i class="fas fa-caret-right"></i> Ações',
            'taxas'=>'Taxas e Tarifas',
            'faq'=>'FAQ Aplicativo',
            'operadores'=>'Operadores',
            'motivos'=>'Motivos',
        ];
        return view('operadores',compact('nomeTela','columns','telas'));
    }

    public function formOperador(Request $request) {
        if (Auth()->user()->temPermissao('operadores','leitura')) {
            return response()->json(['success'=>false,'message'=>'sem permissão para o usuário']);
        }
        $acessos = $request->acesso;
        $leituras = $request->leitura;
        unset($request->acesso);
        unset($request->leitura);

        //cancelar senha em branco
        if ($request->password==null) {
            unset($request['password']);
        } else {
            if ($request->password != $request->rep_password) {
                return response()->json(['success'=>false,'message'=>'Senhas digitadas não batem']);
            }
        }

        if (isset($request->id)) {
            $request->data_nascimento = $this->fData($request->data_nascimento);
            $operador = Operador::find($request->id);
            $operador->update($request->toArray());
        } else {
            $operador = new Operador;
            $operador->create($request->toArray());
        }
        if (isset($operador->id)) {
            OperadorPermissao::where('id_operador',$operador->id)->delete();
            if ($acessos) {
                foreach($acessos as $ka=>$va) {
                    $perm = OperadorPermissao::firstOrNew([
                        'id_operador'=>$operador->id,
                        'tela'=>$ka
                    ]);
                    $perm->acesso = 1;
                    $perm->save();
                }
            }
            if ($leituras) {
                foreach($leituras as $ka=>$va) {
                    $perm = OperadorPermissao::firstOrNew([
                        'id_operador'=>$operador->id,
                        'tela'=>$ka
                    ]);
                    $perm->leitura = 1;
                    $perm->save();
                }
            }
        }
        return response()->json(['status'=>'success']);
    }

    public function pegaOperadores() {
        $items = Operador::all();
        $quick = DataTables::of($items);
        return $quick->make(true);
    }

    public function pegaOperador($id) {
        $item = Operador::with('permissoes')->find($id);
        return response()->json(['status'=>'success','item'=>$item]);
    }

    public function apagarOperador($id) {
        $item = Operador::with('permissoes')->find($id);
        $item->delete();
        return response()->json(['status'=>'success']);
    }

    /*public function pAuthorizeUser(Request $request, $clienteId, $step=null) {
        $user = Usuario::find($clienteId);
        if ($request->accept == 'S') {
            //$user->cadastro_finalizado = 1;
            $user->status = 2;
            if ($step == 'inicio') {
                Notification::send($user, new Approved($user));
                $user->historicos()->create([
                    'id_acao_historico'=>7,
                    'datahora'=>date("Y-m-d H:i:s"),
                    'id_operador'=>Auth::user()->id
                ]);
            }
        } else {
            $user->status = 4;
            if ($step == 'inicio') {
                Notification::send($user, new Rejected($user));
            }
            $user->historicos()->create([
                'id_acao_historico'=>8,
                'datahora'=>date("Y-m-d H:i:s"),
                'id_operador'=>Auth::user()->id
            ]);
        }
        $user->save();
        return response()->json(['success'=>true]);
    }*/

    public function apagarCliente(Request $request) {

    }

    /*public function pActions(Request $request, $clienteId) {
        $user = Usuario::find($clienteId);
        if ($request->option == 1) {
            $user->status = 4;
            $user->save();
            Notification::send($user, new Blocked($user));
        }
        return response()->json(['success'=>true]);
    }*/

    public function cancelarEmissaoFatura(Request $request) {
        DB::beginTransaction();
        if ($request->id_usuario) {
            $usuario = Usuario::find($request->id_usuario);
            $usuario->faturas()->where('id',$request->id_fatura)->first()->update([
                'url'=>null,
                'digitos'=>null,
                'status'=>0
            ]);
            DB::commit();
            return response()->json(['success'=>true]);
        }
    }

    public function recalculaFatura($faturaId) {
        $fatura = Fatura::find($faturaId);
        $valor_fatura = 0;
        foreach($fatura->parcelas as $prc) {
            if($prc->parcela_type == 'App\SolicitacaoParcelamento') {
                if (!$prc->pago && isset($prc->parcela->valor_parcela)) {
                    $valor_fatura += $prc->parcela->valor_parcela;
                }
            } else if($prc->parcela_type == 'App\Cobranca') {
                $valor_fatura += $prc->parcela->valor;
            }
        }
        $fatura->valor_total = $valor_fatura;
        $fatura->save();
    }

    public function recalculaLimites($usuario) {
        $limiteInicial = $usuario->limite_total;
        $limiteSolicitado = 0;
        $limiteRecuperado = 0;
        
        // limite utilizado
        $creditos = $usuario->parcelamentos()->get();
        foreach($creditos as $credito) {
            $limiteSolicitado += $credito->valor_solicitado;
        }

        // limite recuperado
        $faturas = $usuario->faturas()->get();
        foreach($faturas as $fatura) {
            foreach($fatura->parcelas as $parcela) {
                if ($parcela->parcela_type == 'App\SolicitacaoParcelamento') {
                    if ($parcela->pago) {
                        $limiteRecuperado += ($parcela->parcela->valor_solicitado/$parcela->parcela->parcelas);
                    }
                }
            }
        }
        $usuario->limite_utilizado = $limiteSolicitado - $limiteRecuperado;
        $usuario->limite_disponivel = $limiteInicial - $usuario->limite_utilizado;
        $usuario->save();
    }
    public function marcarFatura(Request $request) {
        DB::beginTransaction();
        if ($request->id_usuario) {
            $usuario = Usuario::find($request->id_usuario);
            $fatura = $usuario->faturas()->where('id',$request->id_fatura)->first();
            foreach($request->parcelas as $idf) {
                $parcela = $fatura->parcelas()->with('parcela')->where('id',$idf)->first();
                $parcela->pago = (isset($parcela->pago) && $parcela->pago == 1) ? 0 : 1;
                $parcela->save();
                if ($parcela->pago == 1) {
                    if ($parcela->parcela_type == 'App\SolicitacaoParcelamento') {
                        $this->recalculaLimites($usuario);
                        $usuario->limite_disponivel = $usuario->limite_disponivel + $parcela->parcela->valor_parcela;
                        $usuario->save();
                    }
                } else {
                    if ($parcela->parcela_type == 'App\SolicitacaoParcelamento') {
                        $this->recalculaLimites($usuario);
                        $usuario->limite_disponivel = $usuario->limite_disponivel - $parcela->parcela->valor_parcela;
                        $usuario->save();
                    }
                }
            }
            if ($fatura->antecipa == 1) {
                $fatura->antecipa = 0;
                $fatura->save();
            }
            $this->recalculaFatura($fatura->id);
            DB::commit();
            return response()->json(['success'=>true]);
        }
    }

    public function cronFatura(Request $request) {
        // fechar faturas com fechamento hoje
        $faturasFechar = Fatura::with('usuario')->where('fechamento',date("Y-m-d"))->where('fechado',0)->where('pago',0)->get();
        foreach($faturasFechar as $fatura) {
            $fatura->fechado = 1;
            $fatura->save();
            $user = $fatura->usuario;
        }

        // procurar faturas em atraso de 3 dias pós vencimento
        $faturasAtraso = Fatura::with('usuario')->where('vencimento',date("Y-m-d",strtotime('-3 days')))->where('fechado',1)->where('pago',0)->get();
        foreach($faturasAtraso as $fatura) {
            $user = $fatura->usuario;
            //Notification::send($user, new DelayedInvoice($user,$fatura));
            Mail::to($user->email)->send(new DelayedInvoice($user,$fatura));
        }

        return response()->json(['success'=>true]);
    }
}
