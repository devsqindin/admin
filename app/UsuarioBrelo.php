<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Usuario;

class UsuarioBrelo extends BReADResource
{
    
    protected $table='usuario_brelo';

    public $atr = [
        'token'=>[
            'type'=>'text',
            'nullable'=>false,
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
}
