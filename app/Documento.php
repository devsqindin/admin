<?php

namespace App;

use App\BReAD\BReADResource;
use Illuminate\Database\Eloquent\Model;
use App\Usuario;
use App\TipoDocumento;

class Documento extends BReADResource
{
    
    protected $table='documento';

    public $atr = [
        'nome'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'titulo'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'tipo'=>[
            'type'=>'text',
            'nullable'=>true,
        ],
        'url'=>[
            'type'=>'text',
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
        'id_tipo_documento'=>[
            'class'=>TipoDocumento::class,
            'relation'=>['belongsTo','hasMany'],
            'nullable'=>true,
            'onDelete'=>'SET NULL',
            'onCascade'=>'SET NULL',
        ],
    ];
    
    public function usuario() {
        return $this->belongsTo('App\Usuario');
    }

    public function documento_fiducia() {
        return $this->hasOne('App\DocumentoFiducia','id_documento');
    }

    public function tipo_documento() {
        return $this->belongsTo('App\TipoDocumento','id_tipo_documento','id');
    }
}
