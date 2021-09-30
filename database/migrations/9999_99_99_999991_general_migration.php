<?php

use App\BReAD\BReADMigration;

class GeneralMigration extends BReADMigration
{

    protected static function getClasses(){
        return [
            //Basic
            'App\Motivo',
        ];
    }
}
