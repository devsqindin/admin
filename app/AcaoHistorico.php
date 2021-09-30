<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;

class AcaoHistorico extends BReADResource
{
    
    protected $table='acao_historico';

    public $atr = [
        'descricao'=>[
            'type'=>'string',
            'nullable'=>true,
        ],
    ];
}
