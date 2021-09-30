<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\AcaoHistorico;
use App\Usuario;
use App\Operador;

class UsuarioHistorico extends BReADResource
{
    
    protected $table='usuario_historico';

    public $atr = [
        'descricao'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'valor'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'datahora'=>[
            'type'=>'datetime',
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
        'id_operador'=>[
            'class'=>Operador::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
        'id_acao_historico'=>[
            'class'=>AcaoHistorico::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
    ];

    public function usuario() {
        return $this->belongsTo('App\Usuario','id_usuario','id');
    }

    public function operador() {
        return $this->belongsTo('App\Operador','id_operador','id');
    }

    public function acao() {
        return $this->belongsTo('App\AcaoHistorico','id_acao_historico','id');
    }

}
