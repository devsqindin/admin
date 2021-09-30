<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\Usuario;
use App\CartaoCredito;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fatura extends BReADResource
{
    
    use SoftDeletes;
    protected $table='fatura';

    public $atr = [
        'valor_total'=>[
            'type'=>'float',
            'nullable'=>true,
        ],
        'fechamento'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'vencimento'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'dtpagamento'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'anomes'=>[
            'type'=>'string',
            'nullable'=>true,
        ],
        'maxparcelas'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'reg_date'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'fechado'=>[
            'type'=>'boolean',
            'default'=>0,
            'nullable'=>true,
        ],
        'pago'=>[
            'type'=>'boolean',
            'default'=>0,
            'nullable'=>true,
        ],
        'url'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'digitos'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'status'=>[
            'type'=>'integer',
            'nullable'=>true,
            'default'=>0
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
        return $this->belongsTo('App\Usuario','id_usuario');
    }

    public function parcelas() {
        return $this->hasMany('App\ParcelasFatura','id_fatura');
    }
}
