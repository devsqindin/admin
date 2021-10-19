<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Usuario;
use App\Fatura;
use App\Mail\ResetPassword;
use App\Mail\ResetEmail;
use App\Mail\LateBill;
use App\Notifications\WelcomeUser;
use App\Notifications\Rejected;
use Route;
use Hash;
use Mail;
use Notification;
use DataTables;
use DB;
use Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
//use Illuminate\Log\LogManager;

class UsuarioController extends Controller
{

    public function fNum ($num) {
        $num = str_replace(".","",$num);
        return str_replace(",",".",$num);
    }

    public function mMes($mes) {
        switch ($mes) {
            case "01":    $mes = "Janeiro";     break;
            case "02":    $mes = "Fevereiro";   break;
            case "03":    $mes = "Março";       break;
            case "04":    $mes = "Abril";       break;
            case "05":    $mes = "Maio";        break;
            case "06":    $mes = "Junho";       break;
            case "07":    $mes = "Julho";       break;
            case "08":    $mes = "Agosto";      break;
            case "09":    $mes = "Setembro";    break;
            case "10":    $mes = "Outubro";     break;
            case "11":    $mes = "Novembro";    break;
            case "12":    $mes = "Dezembro";    break; 
        }
     
     return $mes;
    }

    public function confirmMati(Request $request) {

        $user = Auth::user();
        //if (!$user->opcao_documento || $user->opcao_documento != 'b') {
            $user->opcao_documento = 'm';
            $user->save();
        //}
        return response()->json(['success'=>true]);
    }

    public function belvoLink(Request $request) {
       
        $user = Auth::user();
        $userId = $user->id;

        $user->belvo_link = $request->link;
 
        $user->opcao_documento = 'b';
 
        $user->save();

        $institution = $request->institution;

        if($institution === 'bradesco_br_retail') {

            // TODO -> New mechanism to process possible data failed.
            $this->saveBradescoDataUserBelvo('accounts', $userId);
            $this->saveBradescoDataUserBelvo('owners', $userId);
            $this->saveBradescoDataUserBelvo('balances', $userId);
            $this->saveBradescoDataUserBelvo('incomes', $userId);
            $this->saveBradescoDataUserBelvo('transactions', $userId);
        }

        return response()->json(['success'=>true]);
    }

    public function belvoAccessToken() {

        $postfield = '{

            "id": "'.env('BELVO_ID').'",
            "password": "'.env('BELVO_PASS').'",
            "scopes": "read_institutions,write_links,read_links",
            "widget": {
                "branding": {
                    "company_name": "Qindin"
                }
            }
          }';

        $curlHandler = curl_init();

        curl_setopt_array($curlHandler, [

            CURLOPT_URL => "https://".env('BELVO_URL')."/api/token/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,

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
    

        return response($response);
    }

    /*
    public function autentica(Request $request) {
        if (Auth::user()->validateForPassportPasswordGrant($request->password)) {
            $success = true;
        } else {
            $success = false;
        }
        return response()->json(['success'=>$success]);
    }
    
    public function sendNotification()
    {
        $user = Usuario::find(62);
        Notification::send($user, new WelcomeUser($user));
        dd('done');
    }
    */

    public function saveBradescoDataUserBelvo($type, $userId) {

        if (!in_array($type, ['accounts','owners','balances','incomes','transactions'])) {
            return;
        }

        $user = Usuario::find($userId);

        if (!$user || !$user->belvo_link) {
            return;
        }

        $postfieldJson = [
            'link'=>$user->belvo_link
        ];

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

            return;
        } else if ($http_code === 500) {

            $i = 0;
            
            while($i++ < $this->curlExecRetryMax) {
                
                sleep($this->interval);
                $response = curl_exec($curlHandler);
                $http_code = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
                
                if($http_code === 200 || $http_code === 201) {

                    UsuarioBelvo::create([
                        'json'=>$response,
                        'type'=>$type,
                        'id_usuario'=>$user->id,
                    ]);
        
                    return;
                } else {

                    if($i < $this->curlExecRetryMax) {

                        // Belvo rule five tries / base interval of 3 seconds with a factor of two.
                        // 1-> 3, 2-> 2*3, 3-> 2*6, 4->2*12, 5-> 2*24
                        $this->interval = 2 * $this->interval;
                    }
                }
            }
            
        } else if ($http_code === 400 && (str_contains($header, "too_many_sessions" || str_contains($body, "too_many_sessions")))) {

            curl_close($curlHandler);
            return;
        } else {
            
            curl_close($curlHandler);
            return;
        }
    }

    public function logInProtected(Request $request)
    {

        Log::debug("POST: QINDIN-API/login");
        Log::debug('Usuário tentando fazer login: ', ['email' => $request->username]);

        $tokenRequest = $request->create('/oauth/token', 'POST', $request->all());
        $response = Route::dispatch($tokenRequest);
        $json = (array) json_decode($response->getContent());
        $user = Usuario::get()->where('email',$request->username)->first();
        
        if ($user && isset($json['access_token'])){
            $pnome = explode(" ",$user['nome_completo']);
            $json['user']['nome_completo'] = strtolower($pnome[0]);
            $json['user']['email'] = $user['email'];
            $json['user']['limite_total'] = $user['limite_total'];
            $json['user']['limite_utilizado'] = $user['limite_utilizado'];
            $json['success'] = true;

            Log::debug('Usuário logou com sucesso: ', ['id' => $user->id, 'email' => $user->email]);
            return response()->json($json);
        } else {
            Log::debug('Usuário falhou no login: ', ['id' => $user->id, 'email' => $user->email]);
            return response()->json(['success'=>false,'message'=>'E-mail ou senha incorretos']);
        }
    }

    public function bancos() {

        Log::debug("GET: QINDIN-API/bancos");
        
        $bancos = DB::select("SELECT codigo, UPPER(nome) AS nome FROM bancos ORDER BY nome");

        Log::debug('Lista de bancos retornada com sucesso: ', ['bancos' => $bancos->bancos]);

        return response()->json(['success'=>true,'bancos'=>$bancos]);
    }

    public function userStartConvite(Request $request) {

        Log::debug("POST: QINDIN-API/user/startconvite");
        Log::debug('Usuário tentando fazer o cadastro: ', ['email' => $request->email]);


        if (!isset($request->termos) && $request->termos != 'true') {

            Log::debug('Usuário não aceitou os termos: ', ['email' => $request->email]);
            return response()->json(['success'=>false,'message'=>'É necessário aceitar o Termo de Uso e a Política de Privacidade para criar seu cadastro no Desbankei']);
        }

        DB::beginTransaction();

        if (Usuario::get()->where('cpf',$request->cpf)->first()) {

            Log::debug('Usuário já tem CPF cadastrado no sistema: ', ['email' => $request->email]);
            return response()->json(['success'=>false,'message'=>'Este CPF já está cadastrado para um usuário Desbankei, faça o acesso utilizando os dados do mesmo, em caso de dúvidas nos contacte.']);
        }

        $date = date_create('NOW');
        $created_at = date_format($date, 'Y-m-d H:i:s');
        $updated_at = $created_at;
        $var_cpf_negativado = $request->cpf_negativado == 'Não' ? 0 : 1;
        $var_banco_credenciado = $request->banco_credenciado == 'Não' ? 0 : 1;
        $regiao = '';
        $uf = '';
        $var_whatsapp = $request->whatsapp;

        //$ddd = substr($var_whatsapp, 1, 2); /* (99) 99999-9999 -> 99 */
        $ddd = substr($var_whatsapp, 0, 2); /* 9999999-9999 -> 99 */

        $uf_regiao = array (
        
            array(11, "SP", "São Paulo"),
            array(12, "SP", "São José dos Campos"),
            array(13, "SP", "Santos"),
            array(14, "SP", "Bauru"),
            array(15, "SP", "Sorocaba"),
            array(16, "SP", "Ribeirão Preto"),
            array(17, "SP", "São José do Rio Preto"),
            array(18, "SP", "Presidente Prudente"),
            array(19, "SP", "Campinas"),
            array(21, "RJ", "Rio de Janeiro"),
            array(22, "RJ", "Campos dos Goytacazes"),
            array(24, "RJ", "Volta Redonda"),
            array(27, "ES", "Vitória / Vila Velha"),
            array(28, "ES", "Cachoeiro de Itapemirim"),
            array(31, "MG", "Belo Horizonte"),
            array(32, "MG", "Juiz de Fora"),
            array(33, "MG", "Governador Valadares"),
            array(34, "MG", "Uberlândia"),
            array(35, "MG", "Poços de Caldas"),
            array(37, "MG", "Divinópolis"),
            array(38, "MG", "Montes Claros"),
            array(41, "PR", "Curitiba"),
            array(42, "PR", "Ponta Grossa"),
            array(43, "PR", "Londrina"),
            array(44, "PR", "Maringá"),
            array(45, "PR", "Foz do Iguaçú"),
            array(46, "PR", "Pato Branco / Francisco Beltrão"),
            array(47, "SC", "Joinville"),
            array(48, "SC", "Florianópolis"),
            array(49, "SC", "Chapecó"),
            array(51, "RS", "Porto Alegre"),
            array(53, "RS", "Pelotas"),
            array(54, "RS", "Caxias do Sul"),
            array(55, "RS", "Santa Maria"),
            array(61, "DF", "Brasília"),
            array(62, "GO", "Goiânia"),
            array(63, "TO", "Palmas"),
            array(64, "GO", "Rio Verde"),
            array(65, "MT", "Cuiabá"),
            array(66, "MT", "Rondonópolis"),
            array(67, "MS", "Campo Grande"),
            array(68, "AC", "Rio Branco"),
            array(69, "RO", "Porto Velho"),
            array(71, "BA", "Salvador"),
            array(73, "BA", "Ilhéus"),
            array(74, "BA", "Juazeiro"),
            array(75, "BA", "Feira de Santana"),
            array(77, "BA", "Barreiras"),
            array(79, "SE", "Aracaju"),
            array(81, "PE", "Recife"),
            array(82, "AL", "Maceió"),
            array(83, "PB", "João Pessoa"),
            array(84, "RN", "Natal"),
            array(85, "CE", "Fortaleza"),
            array(86, "PI", "Teresina"),
            array(87, "PE", "Petrolina"),
            array(88, "CE", "Juazeiro do Norte"),
            array(89, "PI", "Picos"),
            array(91, "PA", "Belém"),
            array(92, "AM", "Manaus"),
            array(93, "PA", "Santarém"),
            array(94, "PA", "Marabá"),
            array(95, "RR", "Boa Vista"),
            array(96, "AP", "Macapá"),
            array(97, "AM", "Coari"),
            array(98, "MA", "São Luís"),
            array(99, "MA", "Imperatriz")
        );

        for ($row = 0; $row < 67; $row++) {

            if ($uf_regiao[$row][0] == $ddd) {

                $uf = $uf_regiao[$row][1];
                $regiao = $uf_regiao[$row][2];

                break;
            }
        }

        //{{ condition ? 'yes' : 'no' }}

        $user = Usuario::create([

            'nome_completo'=>$request->nome_completo,
            'whatsapp'=>$var_whatsapp,
            'email'=>trim($request->email),
            'cpf'=>trim($request->cpf),
            'perfil'=>trim($request->perfil),   
            
            'cpf_negativado'=>$var_cpf_negativado,  
            'banco_credenciado'=>$var_banco_credenciado,
            
            'banco_nome'=>trim($request->banco_nome),
            'aceito'=>$request->termos,
            'password'=>$request->password,
            // API Calc
            'passo_cadastro'=>0,
            'created_at'=>$created_at,
            'updated_at'=>$updated_at,
            'regiao'=>$regiao,
            'uf'=>$uf,
        ]);

        $tokenRequest = $request->create('/oauth/token', 'POST', $request->all());
        $response = Route::dispatch($tokenRequest);
        $json = (array) json_decode($response->getContent());

        //falta calcular regiao e uf, e deployar
     
        DB::commit();

        $json['success'] = true;

        Log::debug('Usuário criado com sucesso: ', ['email' => $request->email]);

        return response()->json($json);
    }

    // iniciar cadastro usuário
    public function userStart(Request $request) {

        if (Usuario::get()->where('email',$request->username)->first()) {
            return response()->json(['success'=>false,'message'=>'E-mail já cadastrado, se não terminou seu cadastro, faça seu login e continue o processo de cadastramento.']);
        } else if (Usuario::get()->where('whatsapp',$request->whatsapp)->first()) {
            return response()->json(['success'=>false,'message'=>'Esse número de WhatsApp já está cadastrado, faça seu login e continue o processo de cadastramento.']);
        } else {
            if ($request->password != $request->rep_password) {
                return response()->json(['success'=>false,'message'=>'Senhas digitadas não conferem']);
            }
            if (!isset($request->aceito) && $request->aceito != 'S') {
                return response()->json(['success'=>false,'message'=>'É necessário aceitar o Termo de Uso e a Política de Privacidade para criar seu cadastro no Desbankei']);
            }

            DB::beginTransaction();

            if (Usuario::get()->where('cpf',$request->cpf)->first()) {
                return response()->json(['success'=>false,'message'=>'Este CPF já está cadastrado para um usuário Desbankei, faça o acesso utilizando os dados do mesmo, em caso de dúvidas nos contacte.']);
            }
            $user = Usuario::create([
                'nome_completo'=>$request->nome_completo,
                'cpf'=>trim($request->cpf),
                'email'=>trim($request->username),
                'password'=>$request->password,
                'whatsapp'=>trim($request->whatsapp),
                'aceito'=>$request->aceito,
                'passo_cadastro'=>1,
            ]);

            $tokenRequest = $request->create('/oauth/token', 'POST', $request->all());
            $response = Route::dispatch($tokenRequest);
            $json = (array) json_decode($response->getContent());

            if ($user->email) {
                Notification::send($user, new WelcomeUser($user));
                DB::commit();
            } else {
                return response()->json(['success'=>false,'user'=>$user]);
            }
            
            $json['success'] = true;

            return response()->json($json);
        }
    }

    // prosseguir cadastro usuário
    public function userComplete(Request $request, $etapa) {

        $userId = Auth::user()->id;
        $user = Usuario::find($userId);
        if ($etapa == 1) {
            if (Usuario::get()->where('cpf',$request->cpf)->first()) {
                return response()->json(['success'=>false,'message'=>'Este CPF já está cadastrado para um usuário Desbankei, faça o acesso utilizando os dados do mesmo, em caso de dúvidas nos contacte.']);
            }
            $dados = [
                'cpf'=>trim($request->cpf),
                'email'=>trim($request->username),
                'password'=>$request->password,
                'whatsapp'=>trim($request->whatsapp),
                'aceito'=>($request->aceito=='S')?$request->aceito:null,
                'passo_cadastro'=>$etapa,
            ];
        } else if ($etapa == 2) {
            $dados = [
                'rg'=>trim($request->rg),
                'rg_orgao'=>$request->rg_orgao,
                'rg_uf'=>$request->rg_uf,
                'rg_dtemissao'=>$request->rg_dtemissao,
                'sexo'=>$request->sexo,
                'data_nascimento'=>$request->data_nascimento,
                'estado_civil'=>$request->estado_civil,
                'escolaridade'=>$request->escolaridade,
                'nome_completo_pai'=>$request->nome_completo_pai,
                'nome_completo_mae'=>$request->nome_completo_mae,
                'nacionalidade'=>$request->nacionalidade,
                'habita_estado'=>$request->habita_estado,
                'habita_cidade'=>$request->habita_cidade,
                'passo_cadastro'=>$etapa,
            ];
        } else if ($etapa == 3) {
            $dados = [
                'cep'=>$request->cep,
                'endereco'=>$request->endereco,
                'numero'=>$request->numero,
                'complemento'=>$request->complemento,
                'bairro'=>$request->bairro,
                'cidade'=>$request->cidade,
                'estado'=>$request->estado,
                'morapais'=>$request->morapais,
                'moradia'=>$request->moradia,
                'passo_cadastro'=>$etapa,
            ];
        } else if ($etapa == 4) {
            $request->renda_comprovada = $this->fNum($request->renda_comprovada);

            if ($request->restritivo == 'S') {
                $dados = [
                    'profissao'=>$request->profissao,
                    'ocupacao'=>$request->ocupacao,
                    'restritivo'=>$request->restritivo,
                    'renda_comprovada'=>$request->renda_comprovada,
                    'vence_fatura'=>$request->vence_fatura,
                    'credito_aprovado'=>0,
                    'limite_disponivel'=>0,
                    'limite_total'=>0,
                    'limite_utilizado'=>0,
                    'limite_renda'=>date("Y-m-d"),
                    'limite_vence'=>date("Y-m-d"),
                    'status'=>5,
                    'passo_cadastro'=>$etapa,
                ];
            } else {
                $dados = [
                    'profissao'=>$request->profissao,
                    'ocupacao'=>$request->ocupacao,
                    'restritivo'=>$request->restritivo,
                    'renda_comprovada'=>$request->renda_comprovada,
                    'vence_fatura'=>$request->vence_fatura, 
                    'credito_aprovado'=>0,
                    'limite_disponivel'=>0,
                    'limite_total'=>0,
                    'limite_utilizado'=>0,
                    'limite_renda'=>date("Y-m-d"),
                    'limite_vence'=>date("Y-m-d"),
                    'status'=>1,
                    'passo_cadastro'=>$etapa,
                ];
            }
        } else if ($etapa == 5) {
            $dados = [
                'passo_cadastro'=>$etapa
            ];

            $user->historicos()->create([
                'id_acao_historico'=>2,
                'datahora'=>date("Y-m-d H:i:s")
            ]);
        }
        $user->update($dados);

        // dispara e-mail se restritivo
        if ($request->restritivo == 'S') {
            Notification::send($user, new Rejected($user));
        }

        return response()->json(['success'=>true]);
    }

    // editar dados do usuário logado
    public function userEdit(Request $request, $etapa) {

        $logged = Auth::user();
        if (!$logged->validateForPassportPasswordGrant($request->password)) {
            return response()->json(['success'=>false,'message'=>'Senha inválida! Digite novamente.']);
        }
        DB::beginTransaction();
        $msg = '';
        if ($etapa == 1) {
                $dados = [
                    //'nome_completo'=>$request->nome_completo,
                    //'email'=>$request->email,
                    'whatsapp'=>trim($request->whatsapp),
                    //'cpf'=>$request->cpf,
                    //'rg'=>$request->rg,
                    //'sexo'=>$request->sexo,
                    //'data_nascimento'=>$request->data_nascimento,
                    'estado_civil'=>$request->estado_civil,
                    'escolaridade'=>$request->escolaridade,
                    // 'nome_completo_pai'=>$request->nome_completo_pai,
                    // 'nome_completo_mae'=>$request->nome_completo_mae,
                    // 'nacionalidade'=>$request->nacionalidade,
                    // 'habita_estado'=>$request->habita_estado,
                    // 'habita_cidade'=>$request->habita_cidade,
                ];

                // novo e-mail para análise

                if ($request->email != $logged->email) {

                    if (Usuario::get()->where('email',$request->username)->first()) {
                        return response()->json(['success'=>false,'message'=>'E-mail já cadastrado, se não terminou seu cadastro, faça seu login e continue o processo de cadastramento.']);
                    }

                    $dados['new_email'] = $request->email;
                    $dados['reset_email'] = Hash::make($logged->id.$request->email.rand(11,99));
                }
        } else if ($etapa == 2) {
            /*$dados = [
                'cpf'=>$request->cpf,
                'rg'=>$request->rg,
                'data_nascimento'=>$request->data_nascimento,
                'estado_civil'=>$request->estado_civil,
                'escolaridade'=>$request->escolaridade,
            ];*/
        } else if ($etapa == 3) {
            $dados = [
                'cep'=>$request->cep,
                'endereco'=>$request->endereco,
                'numero'=>$request->numero,
                'complemento'=>$request->complemento,
                'bairro'=>$request->bairro,
                'cidade'=>$request->cidade,
                'estado'=>$request->estado,
                'morapais'=>$request->morapais,
                'moradia'=>$request->moradia,
            ];
        } else if ($etapa == 4) {
            $request->renda_comprovada = $this->fNum($request->renda_comprovada);

            // validar alteração de renda ou vencimento fatura
            if (isset($logged->renda_comprovada) && $logged->renda_comprovada > 0) { 
                if (isset($request->renda_comprovada) && $logged->renda_comprovada != $request->renda_comprovada) {
                    if (date("U") < date("U",strtotime($logged->limite_renda." +90 days"))) {
                        return response()->json(['success'=>false,'message'=>'Sua renda só pode ser alterada 90 dias depois do cadastro ou última alteração.']);
                    }
                }

                if (isset($request->vence_fatura) && $logged->vence_fatura != $request->vence_fatura) {
                    if (date("U") < date("U",strtotime($logged->limite_vence." +90 days"))) {
                        return response()->json(['success'=>false,'message'=>'O vencimento da fatura só pode ser alterada 90 dias depois do cadastro ou última alteração.']);
                    }
                }
            }
            
            $dados = [
                'profissao'=>$request->profissao,
                'ocupacao'=>$request->ocupacao,
                'restritivo'=>$request->restritivo,
                'renda_comprovada'=>$request->renda_comprovada,
                //'credito_aprovado'=>$request->renda_comprovada*0.3,
                'credito_aprovado'=>0,
            ];

            if ($request->vence_fatura && $logged->faturas()->where('pago',0)->count() < 1) {
                $dados['vence_fatura'] = $request->vence_fatura;
            }

            // calculo pre aprovado
        } else if ($etapa == 5) {
            // TODO: Nao precisamos mais editar dados bancários.
            $dados = [
                'banco'=>$request->banco,
                'agencia'=>$request->agencia,
                'dv_agencia'=>$request->dv_agencia,
                'numero_conta'=>$request->numero_conta,
                'dv_conta'=>$request->dv_conta,
            ];
        }
        $logged->update($dados);
        DB::commit();

        if (isset($logged->banco)) {
            $logged = $this->pegaBanco($logged);
        }

        $arrsexo = [
            'M'=>'Masculino',
            'F'=>'Feminino',
        ];

        if ($logged->sexo) {
            $logged->sexo_txt = $arrsexo[$logged->sexo];
        }

        if (isset($request->email) && $request->email != $logged->email) {
            Mail::to($logged->new_email)->send(new ResetEmail($logged));
        }

        return response()->json(['success'=>true,'user'=>$logged,'message'=>$msg]);
    }

    // altera a senha
    public function userPassword(Request $request) {

        $user = Auth::user();
        if (isset($request->password)) {
            if (!Hash::check($request->atual_password, $user->password)) {
                return response()->json(['success'=>false,'message'=>'Senha atual está incorreta']);
            }
            if ($request->password == $request->rep_password) {
                $dados = [
                    'password'=>$request->password,
                ];
                $msg = 'Senha alterada com sucesso';
            } else {
                return response()->json(['success'=>false,'message'=>'Senhas nova e repetir senha são diferentes']);
            }
        }
        $user->update($dados);
        return response()->json(['success'=>true]);
    }

    // dashboard app - carregando dados usuário
    public function userHome() {

        Log::debug("GET: QINDIN-API/user/home");
        $user = Auth::user();
        Log::debug("Carregando dados do seguinte usuário: ", ['id' => $user->id, 'email' => $user->email, 'status' => $user->status]);
        /*if ($user->status == 1) {
            return response()->json(['success'=>false,'message'=>"Olá! No momento estamos com altas demandas em nossos apps.\n\nPor isso, pedimos desculpas pela inconveniência no envio de seus comprovantes por aqui. Contudo, já providenciamos o crescimento de nosso time e todos nós estamos à postos para tudo voltar ao normal.\n\nEstamos felizes em saber que o número esperado de acessos ultrapassou nossas estimativas.\n\nPara finalizar o seu cadastro, por gentileza, envie os seguintes documentos por e-mail colocando seu CPF e NOME COMPLETO no ASSUNTO:\n\n• Cópia do CPF (frente) + RG (frente e verso) OU CNH (aberta);\n• Comprovante de residência atualizado;\n• Selfie segurando seu documento com foto ao lado do rosto;\n• Extrato bancário dos últimos 12 meses (é por um bom motivo);\n\nDevido à alta demanda, nosso prazo para resposta de análise de crédito é de 3 dias úteis.\n\nAgradecemos pela compreensão. Estamos aqui por você!\n\nNosso e-mail é: contato@desbankei.com.br\n\nConte com a gente!"]);
        }*/

        if ($user->status == 5) {
            $motivo = (isset($user->motivo->mensagem)) ? ' - '.$user->motivo->mensagem : '';

            Log::debug("Credito recusado. Usuário: ", ['id' => $user->id, 'email' => $user->email, 'status' => $user->status]);

            return response()->json(['success'=>false,'message'=>'Seu crédito foi recusado - '.$motivo,'status'=>$user->status]);
        } else if ($user->status == 4 || $user->status == 7) {
            $motivo = (isset($user->motivo->mensagem)) ? ' - '.$user->motivo->mensagem : '';

            Log::debug("Cadastro bloqueado. Usuário: ", ['id' => $user->id, 'email' => $user->email, 'status' => $user->status]);

            return response()->json(['success'=>false,'message'=>'Cadastro bloqueado'.$motivo,'status'=>$user->status]);
        }

        $user->historicos()->create([
            'descricao'=>'LOGIN',
            'valor'=>'LOGIN',
            'id_acao_historico'=>14,
            'datahora'=>date("Y-m-d H:i:s")
        ]);

        //$user = Usuario::find(59);
        $faturas = ($user->faturas()->with('parcelas')->exists()) ? $user->faturas()->with('parcelas','parcelas.parcela')->orderBy('vencimento')->get()->toArray() : [];
        /*
        ->whereHas('parcelas', function($q){
            $q->where('parcela_type','App\SolicitacaoParcelamento');
        })
        */
        //})->where('fechado',0)->first()->toArray() : [];
        $parcelas = [];
        if ($faturas) {
            $mostrar = -1;

            foreach ($faturas as $kf=>$fatura) {

                // mostrar fatura

                if (($mostrar == -1 && $fatura['anomes'] == date("Ym")) || $mostrar == $kf) {
                    if ($fatura['pago'] == 0) {
                        $faturas[$kf]['mostrar'] = 1;
                        $mostrar = $kf;
                    } else {
                        $mostrar = $kf+1;
                    }
                }

                $faturas[$kf]['valor_fatura'] = number_format($fatura['valor_total'],2,',','.');
                $faturas[$kf]['dtvencimento'] = date("d/m/Y",strtotime($fatura['vencimento']));
                $faturas[$kf]['diasvenc'] = Carbon::parse($fatura['vencimento'])->diffInDays(Carbon::now()) * -1;
                $faturas[$kf]['melhor_data'] = date("d/m/Y",strtotime($fatura['vencimento']." -5 days"));
                $faturas[$kf]['mes_fatura'] = $this->mMes(date("m",strtotime($fatura['vencimento'])));
                $faturas[$kf]['ano_fatura'] = date("Y",strtotime($fatura['vencimento']));
                $faturas[$kf]['pos_fatura'] = 324*$kf;

                if (isset($fatura['parcelas'])) {
                    $parcelas = $fatura['parcelas'];
                    foreach($parcelas as $pk=>$pv) {
                        if ($pv['parcela_type'] == 'App\Cobranca' && isset($pv['parcela'])) {
                            $faturas[$kf]['parcelas'][$pk]['valor_parcela'] = number_format($pv['parcela']['valor'],2,',','.');
                            $faturas[$kf]['parcelas'][$pk]['reg_date'] = date("d/m/Y",strtotime($pv['parcela']['reg_date']));
                            $faturas[$kf]['parcelas'][$pk]['reg_date_texto'] = date("d",strtotime($pv['parcela']['reg_date'])).' de '.$this->mMes(date("m",strtotime($pv['parcela']['reg_date'])));
                            $faturas[$kf]['parcelas'][$pk]['descricao'] = $pv['parcela']['descricao'];
                        } else {
                            $faturas[$kf]['parcelas'][$pk]['valor_solicitado'] = number_format($pv['parcela']['valor_solicitado'],2,',','.');
                            $faturas[$kf]['parcelas'][$pk]['valor_parcela'] = number_format($pv['parcela']['valor_parcela'],2,',','.');
                            $faturas[$kf]['parcelas'][$pk]['reg_date'] = date("d/m/Y",strtotime($pv['parcela']['reg_date']));
                            $faturas[$kf]['parcelas'][$pk]['parcelas_pagar'] = ($pv['parcela']['parcelas_pagas']) ? $pv['parcela']['parcelas_pagas']+1 : 1;
                            $faturas[$kf]['parcelas'][$pk]['numparcela'] = $pv['numparcela'];
                            $faturas[$kf]['parcelas'][$pk]['reg_date_texto'] = date("d",strtotime($pv['parcela']['reg_date'])).' de '.$this->mMes(date("m",strtotime($pv['parcela']['reg_date'])));
                            $faturas[$kf]['parcelas'][$pk]['taxa_juros'] = number_format(($pv['parcela']['taxa_juros']*100),2,',','.');
                            unset($faturas[$kf]['parcelas'][$pk]['parcelas_pagas']);
                        }
                    }
                }
            }
        }

        $user = $user->toArray();
        $pnome = explode(" ",$user['nome_completo']);
        $user['nome_app'] = strtolower($pnome[0]);

        Log::debug("Dados de fatura/parcelas do usuário recuperados com sucesso:", ['id' => $user->id, 'faturas' => $faturas, 'parcelas' => $parcelas]);

        return response()->json(['success'=>true,'user'=>$user,'faturas'=>$faturas,'parcelas'=>$parcelas]);
    }

    private function pegaBanco($user) {
        $bancos = DB::select("SELECT codigo, UPPER(nome) AS nome FROM bancos WHERE codigo = ? ORDER BY nome",[$user->banco]);
        $user->nome_banco = $bancos[0]->nome;
        return $user;
    }

    // dados rapido usuário
    public function userData(Request $request) {

        Log::debug("GET: QINDIN-API/user");

        $arrsexo = [
            'M'=>'Masculino',
            'F'=>'Feminino'
        ];
        
        $user = Auth::user();

        Log::debug('Usuário encontrado: ', ['id' => $user->id, 'email' => $user->email, 'status' => $user->status]);

        if ($user->status == 4) {
            return response()->json(['success'=>false,'message'=>'Cadastro bloqueado']);
        }
        $user->faturas_abertas = $user->faturas()->where('pago',0)->count();
        if ($user->sexo) {
            $user->sexo_txt = $arrsexo[$user->sexo];
        }
        $user->renda_comprovada_text = number_format($user->renda_comprovada,2,',','.');
        if (isset($user->banco)) {
            $user = $this->pegaBanco($user);
        }
        if ($request->query('tela') == 'bancario') {
            Log::debug("GET: QINDIN-API/user -> query('tela')");
            $bancos = DB::select("SELECT codigo, UPPER(nome) AS nome FROM bancos ORDER BY nome");
            $user['bancos'] = $bancos;
            $json = ['success'=>true,'user'=>$user,'bancos'=>$bancos];    
        }
        
        $json = ['success'=>true,'user'=>$user];

        Log::debug("Usuário encontrado e verificado com sucesso: ", ['id' => $user->id]);

        return response()->json($json);
    }

    public function userDocumentos() {
        $user = Auth::user();
        if ($user->status == 4) {
            return response()->json(['success'=>false,'message'=>'Cadastro bloqueado']);
        }
        $pendentes = [];
        $telas = [];

        if ($user->opcao_documento == 'rg') {
            if (isset($user->rg_frente) && $user->rg_frente == 0 || $user->rg_frente == 2) {
                $pendentes[] = ['field'=>'rg_frente','nome'=>'RG Frente'];
                if (!in_array(2, $telas)) {
                    $telas[] = 2;
                }
            }
            if (isset($user->rg_verso) && $user->rg_verso == 0 || $user->rg_verso == 2) {
                $pendentes[] = ['field'=>'rg_verso','nome'=>'RG Verso'];
                if (!in_array(2, $telas)) {
                    $telas[] = 2;
                }
            }
            if (isset($user->cpf_frente) && $user->cpf_frente == 0 || $user->cpf_frente == 2) {
                $pendentes[] = ['field'=>'cpf_frente','nome'=>'CPF Frente'];
                if (!in_array(2, $telas)) {
                    $telas[] = 2;
                }
            }
        } else if ($user->opcao_documento == 'cnh') {
            if (isset($user->cnh_frente) && $user->cnh_frente == 0 || $user->cnh_frente == 2) {
                $pendentes[] = ['field'=>'cnh_frente','nome'=>'CNH Aberta'];
                if (!in_array(2, $telas)) {
                    $telas[] = 2;
                }
            }
        }

        if (isset($user->foto_doc) && $user->foto_doc == 0 || $user->foto_doc == 2) {
            $pendentes[] = ['field'=>'foto_doc','nome'=>'Foto com Documento'];
            $telas[] = 3;
        }
        if (isset($user->comprovante_residencia) && $user->comprovante_residencia == 0 || $user->comprovante_residencia == 2) {
            $pendentes[] = ['field'=>'comprovante_residencia','nome'=>'Comprovante de Residência'];
            $telas[] = 4;
        }
        if (isset($user->extrato_bancario) && $user->extrato_bancario == 0 || $user->extrato_bancario == 2) {
            $pendentes[] = ['field'=>'extrato_bancario','nome'=>'Extrato Bancário'];
            $telas[] = 5;
        }

        return response()->json(['success'=>true,'documento'=>$user->opcao_documento,'pendentes'=>$pendentes,'telas'=>$telas]);
    }

    public function userAntecipa(Request $request) {
        $user = Auth::user();
        if ($user->status == 4) {
            return response()->json(['success'=>false,'message'=>'Cadastro bloqueado']);
        }
        if (!$user->validateForPassportPasswordGrant($request->password)) {
            return response()->json(['success'=>false,'message'=>'Senha inválida! Digite novamente.']);
        }

        $fatura = $user->faturas()->find($request->id_fatura);

        if ($this->fNum($request->valor_antecipa) > $fatura->valor_total) {
            return response()->json(['success'=>false,'message'=>'Não é permitido um valor superior ao da fatura']);
        }

        $dados = [
            'valor_antecipa'=>$request->valor_antecipa,
            'fatura'=>$fatura,
        ];

        $fatura->antecipa = 1;
        $fatura->save();

        Mail::to('adiantamento@desbankei.com.br')->send(new LateBill($user,$dados));
        //Mail::to('suporte@f5webnet.com.br')->send(new LateBill($user,$dados));

        return response()->json(['success'=>true]);
    }

    public function forgotMyPass(Request $request){

        Log::debug("POST: QINDIN-API/senha/redefinir");

        try{
            $email = $request->email;
            $usuario = Usuario::all()->where('email',$email)->first();

            if(is_null($usuario)){
                Log::debug("Usuário não cadastrado tentou pedir email de 'Esqueci minha senha': ", ['email' => $request->email]);
                return response()->json(['success'=>false,'message'=>'Usuário não cadastrado']);
            }
            if(is_null($usuario->email)){
                Log::debug("Email não cadastrado tentou pedir email de 'Esqueci minha senha': ", ['email' => $request->email]);
                return response()->json(['success'=>false,'message'=>'Email não cadastrado']);
            }
            $usuario->limite_password = date("Y-m-d H:i:s");
            $token = $usuario->generateReset();
            $usuario->save();
            Mail::to($usuario->email)->send(new ResetPassword($token,$usuario));
        }catch(Throwable $error){
            Log::error("Erro/Exception ao pedir email de 'Esqueci minha senha no usuário: ", ['email' => $request->email]);
            Log::error("Exception: ", ['exception' => $error]);
            return response()->json(['success'=>false,'message'=>'Erro ao enviar o email']);
        }

        Log::debug("Usuário solicitou o email de 'Esqueci minha senha' com sucesso: ", ['email' => $request->email]);
        return response()->json(['success'=>true,'token'=>$token]);
    }

    public function recoverPass(Request $request){
        $resetToken = $request->query('resettoken');
        if(Usuario::checkReset($resetToken)){
            return view('resetpassword',compact('resetToken'));
        }else{
            return view('invalidtoken');
        }
    }

    public function changePass(Request $request){
        $resetToken = $request->_userToken;
        if(!Usuario::checkReset($resetToken)){
            return view('invalidtoken');
        }
        $usuario = Usuario::where('reset_password',$resetToken)->first();
        if($request->newPass === $request->confirmPass){
            $usuario->password = $request->newPass;
            $usuario->reset_password = null;
            $usuario->save();
        }else{
            return view('resetpassword',compact('resetToken'));
        }
        return view('passwordsuccess');
    }

    public function checkNumber() {
        return response()->json(['success'=>true]);
    }

    public function validateNumber() {
        return response()->json(['success'=>true]);
    }

    public function pDocumentType(Request $request) {
        $user = Auth::user();
        //$user->opcao_documento = $request->tipo;
        $user->status = 2;
        $user->save();
        return response()->json(['success'=>true]);
    }

    public function perguntas() {
        $perguntas = DB::select("SELECT * FROM perguntas WHERE resposta != 'x' OR resposta IS NULL ORDER BY parent");
        // transforma em array
        $perguntas = array_map(function ($value) {
            return (array)$value;
        }, $perguntas);

        $arrperg = [];
        foreach($perguntas as $perg) {
            if ($perg['parent'] == null) {
                $arrperg[$perg['id']] = ['pergunta'=>$perg['pergunta'],'sub'=>[]];
            } else {
                $arrperg[$perg['parent']]['sub'][] = ['pergunta'=>$perg['pergunta'],'resposta'=>$perg['resposta'],'parent'=>$perg['parent']];
                
                $sub = DB::select("SELECT * FROM perguntas WHERE resposta = 'x' AND parent = ?",[$perg['id']]);
                if ($sub) {
                    $arrperg[$perg['parent']]['sub'][] = ['pergunta'=>$sub[0]->pergunta];
                }
            }
        }
        //dd($arrperg);
        return view('faq',compact('arrperg'));
    }
        
    public function resetEmail(Request $request) {
        $alteraemail = false;

        if (Usuario::where('reset_email',$request->query('token'))->exists()) {
            $user = Usuario::where('reset_email',$request->query('token'))->first();
            $user->email = $user->new_email;
            $user->reset_email = null;
            $user->new_email = null;
            $user->save();
            $alteraemail = true;
        }

        return view('resetemail',compact('alteraemail'));
    }

}
