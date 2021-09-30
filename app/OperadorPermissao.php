<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\AcaoHistorico;
use App\Usuario;
use App\Operador;

class OperadorPermissao extends BReADResource
{
    
    protected $table='operador_permissao';

    public $atr = [
        'tela'=>[
            'type'=>'string',
            'nullable'=>true,
        ],
        'acesso'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
        'leitura'=>[
            'type'=>'boolean',
            'nullable'=>true,
        ],
    ];

    public $foreign = [
        'id_operador'=>[
            'class'=>Operador::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
    ];

    public function operador() {
        return $this->belongsTo('App\Operador','id_operador','id');
    }

}
