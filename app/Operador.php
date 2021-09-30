<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class Operador extends BReADResource implements AuthenticatableContract
{
    
    use Notifiable, Authenticatable;

    protected $hidden = [
        'password', 'reset_password',
    ];

    protected $table='operador';

    public $atr = [
        'email'=>[
            'type'=>'text',
        ],
        'password'=>[
            'type'=>'text',
        ],
        'reset_password'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'nome'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'celular'=>[
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
    ];


    public function setPasswordAttribute($value){
        $this->attributes['password'] = Hash::make($value);
    }

    public static function checkReset($token){
        $usuario = Usuario::where('reset_password',$token)->first();
        if(is_null($usuario)){
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
    
    public function permissoes() {
        return $this->hasMany('App\OperadorPermissao','id_operador','id');
    }

    public function temPermissao($tela,$tipo) {
        return $this->permissoes()->where('tela',$tela)->where($tipo,1)->exists();
    }
}
