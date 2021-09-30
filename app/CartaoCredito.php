<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\Usuario;

class CartaoCredito extends BReADResource
{
    
    protected $table='cartao_credito';

    public $atr = [
        'numeros'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'bandeira'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'data_vencimento'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'cod_seguranca'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'banco'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'reg_date'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'valor'=>[
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
        return $this->belongsTo('App\Usuario');
    }
}
