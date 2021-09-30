<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuario;
use App\Fatura;
use Route;
use Illuminate\Support\Facades\Auth;
use Hash;
use Mail;
use App\Mail\ResetPassword;

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

    public function autentica(Request $request) {
        if (Auth::user()->validateForPassportPasswordGrant($request->password)) {
            $success = true;
        } else {
            $success = false;
        }
        return response()->json(['success'=>$success]);
    }

    public function logInProtected(Request $request)
    {

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
            return response()->json($json);
        } else {
            return response()->json(['success'=>false,'message'=>'E-mail ou senha incorretos']);
        }
    }

    public function userEdit(Request $request, $etapa) {
        $logged = Auth::user();
        if ($etapa == 1) {
            if (isset($request->password)) {
                if ($request->password == $request->rep_password) {
                    $dados = [
                        'nome_completo'=>$request->nome_completo,
                        'email'=>$request->email,
                        'password'=>$request->password,
                        'whatsapp'=>$request->whatsapp,
                        'cpf'=>$request->cpf,
                        'rg'=>$request->rg,
                        'data_nascimento'=>$request->data_nascimento,
                        'estado_civil'=>$request->estado_civil,
                        'escolaridade'=>$request->escolaridade,
                    ];
                } else {
                    return response()->json(['success'=>false,'message'=>'Senhas digitadas não batem!']);
                }
            } else {
                $dados = [
                    'nome_completo'=>$request->nome_completo,
                    'email'=>$request->email,
                    'whatsapp'=>$request->whatsapp,
                    'cpf'=>$request->cpf,
                    'rg'=>$request->rg,
                    'data_nascimento'=>$request->data_nascimento,
                    'estado_civil'=>$request->estado_civil,
                    'escolaridade'=>$request->escolaridade,
                ];
            }
        } else if ($etapa == 2) {
            $dados = [
                'cpf'=>$request->cpf,
                'rg'=>$request->rg,
                'data_nascimento'=>$request->data_nascimento,
                'estado_civil'=>$request->estado_civil,
                'escolaridade'=>$request->escolaridade,
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
                'moradia'=>$request->moradia,
            ];
        } else if ($etapa == 4) {
            $request->renda_comprovada = $this->fNum($request->renda_comprovada);
            $dados = [
                'vence_fatura'=>$request->vence_fatura,
                'profissao'=>$request->profissao,
                'ocupacao'=>$request->ocupacao,
                'renda_comprovada'=>$request->renda_comprovada,
                'credito_aprovado'=>$request->renda_comprovada*0.3,
                'restritivo'=>$request->restritivo,
            ];

            // calculo pre aprovado

        }
        $user = Usuario::find($logged->id);
        $user->update($dados);
        return response()->json(['success'=>true,'user'=>$user]);
    }

    public function userStart(Request $request) {
        if (Usuario::where('email',$request->username)->exists()) {
            return response()->json(['success'=>false,'message'=>'E-mail já cadastrado, se não terminou seu cadastro, faça seu login e continue o processo de cadastramento.']);
        } else if (Usuario::where('whatsapp',$request->whatsapp)->exists()) {
            return response()->json(['success'=>false,'message'=>'Esse número de WhatsApp já está cadastrado, faça seu login e continue o processo de cadastramento.']);
        } else {
            if ($request->password == $request->rep_password) {
                $user = new Usuario;
                $user->create([
                    'nome_completo'=>$request->nome_completo,
                    'email'=>$request->username,
                    'password'=>$request->password,
                    'whatsapp'=>$request->whatsapp,
                    'passo_cadastro'=>1,
                ]);

                $tokenRequest = $request->create('/oauth/token', 'POST', $request->all());
                $response = Route::dispatch($tokenRequest);
                $json = (array) json_decode($response->getContent());

                $json['success'] = true;

                return response()->json($json);
            } else {
                return response()->json(['success'=>false,'message'=>'Senhas digitadas não conferem']);
            }
        }
    }

    public function userComplete(Request $request, $etapa) {
        $userId = Auth::user()->id;
        $user = Usuario::find($userId);
        if ($etapa == 1) {
            $dados = [
                'email'=>$request->username,
                'password'=>$request->password,
                'whatsapp'=>$request->whatsapp,
                'passo_cadastro'=>$etapa,
            ];
        } else if ($etapa == 2) {
            $dados = [
                'cpf'=>$request->cpf,
                'rg'=>$request->rg,
                'data_nascimento'=>$request->data_nascimento,
                'estado_civil'=>$request->estado_civil,
                'escolaridade'=>$request->escolaridade,
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
                'moradia'=>$request->moradia,
                'passo_cadastro'=>$etapa,
            ];
        } else if ($etapa == 4) {
            $request->renda_comprovada = $this->fNum($request->renda_comprovada);
            $dados = [
                'vence_fatura'=>$request->vence_fatura,
                'profissao'=>$request->profissao,
                'ocupacao'=>$request->ocupacao,
                'renda_comprovada'=>$request->renda_comprovada,
                'credito_aprovado'=>$request->renda_comprovada*0.3,
                'limite_utilizado'=>0,
                'limite_disponivel'=>$request->renda_comprovada*0.3,
                'limite_total'=>$request->renda_comprovada*0.3,
                'restritivo'=>$request->restritivo,
                'passo_cadastro'=>$etapa,
            ];
        }
        $user->update($dados);
        return response()->json(['success'=>true]);
    }

    public function userHome() {
        $user = Auth::user();
        $fatura = ($user->faturas) ? $user->faturas->toArray() : [];
        if ($fatura) {
            $fatura['nparcela'] = (Fatura::where('pago',1)->count() + 1);
            $fatura['valor_fatura'] = number_format($fatura['valor_total'],2,',','.');
            $fatura['dtvencimento'] = date("d/m/Y",strtotime($fatura['vencimento']));
        }
        $parcelas = ($user->parcelamentos) ? $user->parcelamentos->toArray() : [];
        if ($parcelas) {
            foreach($parcelas as $pk=>$pv) {
                $parcelas[$pk]['valor_solicitado'] = number_format($pv['valor_solicitado'],2,',','.');
                $parcelas[$pk]['valor_parcela'] = number_format($pv['valor_parcela'],2,',','.');
                $parcelas[$pk]['reg_date'] = date("d/m/Y",strtotime($pv['reg_date']));

                $parcelas[$pk]['parcelas_pagar'] = ($pv['parcelas_pagas']) ? $pv['parcelas_pagas']+1 : 1;

                unset($parcelas[$pk]['parcelas_pagas']);

                $parcelas[$pk]['reg_date_texto'] = date("d",strtotime($pv['reg_date'])).' de '.$this->mMes(date("m",strtotime($pv['reg_date'])));

                $parcelas[$pk]['taxa_juros'] = ($pv['taxa_juros']) ? number_format($pv['taxa_juros'],2,',','.') : '0,0049';
            }
        }
        $user = $user->toArray();
        $pnome = explode(" ",$user['nome_completo']);
        $user['nome_app'] = strtolower($pnome[0]);
        return response()->json(['success'=>true,'user'=>$user,'fatura'=>$fatura,'parcelas'=>$parcelas]);
    }

    public function userData() {
        $user = Auth::user();
        return response()->json(['success'=>true,'user'=>$user]);
    }

    public function forgotMyPass(Request $request){
        $email = $request->email;
        $usuario = Usuario::all()->where('email',$email)->first();
        if(is_null($usuario)){
            return response()->json(['success'=>false,'message'=>'Usuário não cadastrado']);
        }
        if(is_null($usuario->email)){
            return response()->json(['success'=>false,'message'=>'Email não cadastrado']);
        }
        $token = $usuario->generateReset();
        try{
            Mail::to($usuario->email)->send(new ResetPassword($token));
        }catch(Throwable $error){
            return response()->json(['success'=>false,'message'=>'Erro ao enviar o email']);
        }
        return response()->json(['success'=>true]);
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

}
