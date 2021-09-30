<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\Usuario;

class Cobranca extends BReADResource
{
    
    protected $table='cobranca';

    public $atr = [
        'descricao'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'reg_date'=>[
            'type'=>'date',
            'nullable'=>true,
        ],
        'tipo'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'valor'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
    ];

    /*public $foreign = [
        'id_usuario'=>[
            'class'=>Usuario::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
    ];*/
    
    public function parcela() {
        return $this->morphMany('App\ParcelasFatura','parcela');
    }
}
