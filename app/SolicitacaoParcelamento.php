<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\Usuario;
use App\CartaoCredito;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitacaoParcelamento extends BReADResource
{
    
    use SoftDeletes;
    protected $table='solicitacao_parcelamento';

    public $atr = [
        'valor_solicitado'=>[
            'type'=>'float',
            'nullable'=>true,
        ],
        'parcelas'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'parcelas_pagas'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'valor_parcela'=>[
            'type'=>'float',
            'nullable'=>true,
        ],
        'ultima_parcela'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'solicitacao_cliente'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
        'taxa_juros'=>[
            'type'=>'float:8:5',
            'nullable'=>true,
        ],
        'reg_date'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'aceite'=>[
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
        'id_cardholder'=>[
            'class'=>CartaoCredito::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
    ];
    
    public function usuario() {
        return $this->belongsTo('App\Usuario');
    }

    public function parcelaFatura() {
        return $this->morphMany('App\ParcelasFatura','parcela');
    }
}
