<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Contracts\Encryption\DecryptException;


class Usuario extends BReADResource
{
    
    use Notifiable, HasApiTokens, Authenticatable, RevisionableTrait;

    protected $dontKeepRevisionOf = ['reset_password','reset_email','password','passo_cadastro'];

    protected $hidden = [
        'password', 'reset_password',
    ];

    protected $table='usuario';

    public $atr = [
        'email'=>[
            'type'=>'text',
        ],
        'new_email'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'reset_email'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'password'=>[
            'type'=>'text',
        ],
        'reset_password'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'aceito'=>[
            'type'=>'string',
            'nullable'=>true,
        ],
        'whatsapp'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'random_number'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'reg_date'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'nome_completo'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'nome_completo_pai'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'nome_completo_mae'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'nacionalidade'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'habita_estado'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'habita_cidade'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'cpf'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'rg'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'data_nascimento'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'estado_civil'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'escolaridade'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'cep'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'endereco'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'numero'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'complemento'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'bairro'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'cidade'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'estado'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'morapais'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'moradia'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'profissao'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'ocupacao'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'renda_comprovada'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'restritivo'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'opcao_documento'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'rg_frente'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
        'rg_verso'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
        'cpf_frente'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
        'cnh_frente'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
        'foto_doc'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
        'comprovante_residencia'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
        'extrato_bancario'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
        'serasa'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'quod'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'limite_total'=>[
            'type'=>'float',
            'nullable'=>true,
        ],
        'limite_utilizado'=>[
            'type'=>'float',
            'nullable'=>true,
        ],
        'limite_disponivel'=>[
            'type'=>'float',
            'nullable'=>true,
        ],
        'passo_cadastro'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'cadastro_finalizado'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
        'status'=>[
            'type'=>'integer',
            'nullable'=>true,
            'default'=>0
        ],
        'taxa_juros'=>[
            'type'=>'float:8:5',
            'nullable'=>true,
        ],
        'credito_aprovado'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'tipo_cadastro'=>[
            'type'=>'string',
            'nullable'=>true,
        ],
        'vence_fatura'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'limite_password'=>[
            'type'=>'datetime',
            'nullable'=>true,
        ],
        'limite_renda'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'limite_vence'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'banco'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'agencia'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'numero_conta'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'dv_conta'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
    ];

    public function findForPassport($email)
    {
        return $this->where('email', $email)->first();
    }
    
    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }
    
    public function setPasswordAttribute($value){
        $this->attributes['password'] = Hash::make($value);
    }

    public static function checkReset($token){
        $usuario = Usuario::where('reset_password',$token)->first();
        if(is_null($usuario)){
            return false;
        }
        if (date("U") > date("U",strtotime($usuario->limite_password." +12 hours"))) {
            return false;
        }
        [$time,$checker] = decrypt($token);
        [$time2,$checker2] = decrypt($usuario->reset_password);
        if($checker!==$checker2 || $time !==$time2){
            return false;
        }
        $date = new Carbon($time);
        $now = new Carbon;
        if($date->diffInHours($now) > 24){
            return false;
        }
        return true;
    }

    public function generateReset(){
        $now = Carbon::now()->toDateString();
        $small = Hash::make($this->cpf.$this->_RandomString(3));
        $token = encrypt($now.'<>'.$small);

        $this->reset_password = $token;
        $this->save();

        return $token;
    }

    protected static function _RandomString($num){
        // Variable that store final string 
        $final_string = ""; 
        
        //Range of values used for generating string
        $range = "_!@#$^~abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890"; 
        
        // Find the length of created string 
        $length = strlen($range); 
        
        // Loop to create random string 
        for ($i = 0; $i < $num; $i++) 
        { 
            // Generate a random index to pick 
            // characters 
            $index = rand(0, $length - 1); 
            
            // Concatenating the character 
            // in resultant string 
            $final_string.=$range[$index]; 
        } 
        
        // Return the random generated string 
        return $final_string; 
    }

    /** encrypt **/

    public function setNomeCompletoAttribute($value){
        $this->attributes['nome_completo'] = encrypt($value);
    }
    public function getNomeCompletoAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    public function setNomeCompletoPaiAttribute($value){
        $this->attributes['nome_completo_pai'] = encrypt($value);
    }
    public function getNomeCompletoPaiAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    public function setNomeCompletoMaeAttribute($value){
        $this->attributes['nome_completo_mae'] = encrypt($value);
    }
    public function getNomeCompletoMaeAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    public function setCpfAttribute($value){
        $this->attributes['cpf'] = encrypt($value);
    }
    public function getCpfAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    public function setRgAttribute($value){
        $this->attributes['rg'] = encrypt($value);
    }
    public function getRgAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    public function setCepAttribute($value){
        $this->attributes['cep'] = encrypt($value);
    }
    public function getCepAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    public function setEnderecoAttribute($value){
        $this->attributes['endereco'] = encrypt($value);
    }
    public function getEnderecoAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    public function setNumeroAttribute($value){
        $this->attributes['numero'] = encrypt($value);
    }
    public function getNumeroAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    public function setComplementoAttribute($value){
        $this->attributes['complemento'] = encrypt($value);
    }
    public function getComplementoAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    /*public function setEmailAttribute($value){
        $this->attributes['email'] = encrypt($value);
    }
    public function getEmailAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }*/
    /*public function setNewEmailAttribute($value){
        $this->attributes['new_email'] = encrypt($value);
    }
    public function getNewEmailAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }*/
    public function setWhatsappAttribute($value){
        $this->attributes['whatsapp'] = encrypt($value);
    }
    public function getWhatsappAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
    /**/    

    public function motivo() {
        return $this->belongsTo('App\Motivo','id_motivo','id');
    }

    public function faturas() {
        return $this->hasMany('App\Fatura','id_usuario','id');
    }

    public function fatura(){
        return $this->hasOne('App\Fatura','id_usuario','id')->latest();
    }

    public function documentos() {
        return $this->hasMany('App\Documento','id_usuario','id');
    }

    public function parcelamentos() {
        return $this->hasMany('App\SolicitacaoParcelamento','id_usuario','id');   
    }

    public function historicos() {
        return $this->hasMany('App\UsuarioHistorico','id_usuario','id');   
    }
}
