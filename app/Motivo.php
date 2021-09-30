<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;

class Motivo extends BReADResource
{
    protected $table='motivo';

    public $atr = [
        'nome'=>[
            'type'=>'text',
            'nullable'=>true
        ],
        'mensagem'=>[
            'type'=>'text',
            'nullable'=>true
        ],
    ];
}
