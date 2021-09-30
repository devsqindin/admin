<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\Usuario;
use App\Fatura;
use App\SolicitacaoParcelamento;

class Taxa extends BReADResource
{
    
    protected $table='taxas';

    public $atr = [
        'slug'=>[
            'type'=>'string',
            'nullable'=>true,
        ],
        'type'=>[
            'type'=>'string',
            'nullable'=>true,
        ],
        'valor'=>[
            'type'=>'double',
            'nullable'=>true,
        ],
        'nparcela'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
    ];

    /*public $foreign = [
        'id_parcela'=>[
            'class'=>SolicitacaoParcelamento::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
        'id_fatura'=>[
            'class'=>Fatura::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
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
    }*/
}
