<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\Usuario;
use App\Fatura;
use App\Cobranca;
use App\SolicitacaoParcelamento;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParcelasFatura extends BReADResource
{
    
    use SoftDeletes;
    protected $table='parcelas_fatura';

    public $atr = [
        'numparcela'=>[
            'type'=>'integer',
            'nullable'=>true,
        ],
    ];

    public $foreign = [
        'parcela'=>[
            'class'=>[SolicitacaoParcelamento::class,Cobranca::class],
            'nullable'=>true,
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

    public function fatura() {
        return $this->belongsTo('App\Fatura','id_fatura');
    }

    public function usuario() {
        return $this->belongsTo('App\Usuario','id_usuario');
    }

    public function parcela() {
        return $this->morphTo();
    }

    /*public function credito() {
        return $this->belongsTo('App\SolicitacaoParcelamento','parcela_id');
    }

    public function cobranca() {
        return $this->belongsTo('App\Cobranca','parcela_id');
    }*/
}
