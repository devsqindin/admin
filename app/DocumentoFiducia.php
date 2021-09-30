<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\Documento;
use App\SolicitacaoParcelamento;

class DocumentoFiducia extends BReADResource
{
    
    protected $table='documento_fiducia';

    public $atr = [
        'enviado'=>[
            'type'=>'integer',
            'nullable'=>true,
            'default'=>0
        ],
    ];

    public $foreign = [
        'id_documento'=>[
            'class'=>Documento::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
        'id_solicitancao_parcelamento'=>[
            'class'=>SolicitacaoParcelamento::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
    ];

    public function documento() {
        return $this->belongsTo('App\Documento','id_documento','id');
    }
}
