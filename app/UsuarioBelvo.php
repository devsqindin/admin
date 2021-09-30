<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Usuario;

class UsuarioBelvo extends BReADResource
{
    
    protected $table='usuario_belvo';

    public $atr = [
        'json'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'type'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
    ];

    public $foreign = [
        'id_usuario'=>[
            'class'=>Usuario::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
    ];

    public function usuario() {
        return $this->belongsTo('App\Usuario','id_usuario','id');
    }

    public function setJsonAttribute($value){
        $this->attributes['json'] = encrypt($value);
    }
    public function getJsonAttribute($value){
        try{
            $hold = decrypt($value);
        }catch(DecryptException $e){
            $hold = $value;
        }
        return $hold;
    }
}
