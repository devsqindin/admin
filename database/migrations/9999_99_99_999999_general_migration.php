<?php

use App\BReAD\BReADMigration;

class GeneralMigration extends BReADMigration
{

    protected static function getClasses(){
        return [
            //Basic
            'App\CartaoCredito',
            'App\Documento',
            'App\SolicitacaoParcelamento',
            'App\Usuario',
            'App\Fatura',
            'App\ParcelasFatura',
            'App\TipoDocumento',
            'App\UsuarioHistorico',
            'App\AcaoHistorico',
            'App\Operador',
            'App\OperadorPermissao',
            'App\Taxa',
            'App\Cobranca',
        ];
    }
}
