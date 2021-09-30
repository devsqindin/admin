<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends BReADResource
{
    
    protected $table='tipo_documento';

    public $atr = [
        'descricao'=>[
            'type'=>'string',
            'nullable'=>true,
        ],
        'slug'=>[
            'type'=>'string',
            'nullable'=>true,
        ],
    ];

    public static function getBySlug($interno){
        return self::where('slug',$interno)->first();
    }
}
