<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pergunta extends Model
{
    protected $table='faq';

    protected $guarded = ['id'];

    public $atr = [
        'pergunta'=>[
            'type'=>'text',
            'nullable'=>true
        ],
        'resposta'=>[
            'type'=>'text',
            'nullable'=>true
        ],
        'type'=>[
            'type'=>'char',
            'params'=>1,
            'nullable'=>true
        ],
        'order'=>[
            'type'=>'integer',
            'nullable'=>true
        ],
        'parent'=>[
            'type'=>'integer',
            'nullable'=>true
        ]
    ];
}
