<?php

namespace App\BReAD;

use App\BReAD\MigrationHelper;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

abstract class BReADMigration extends Migration
{
    protected static abstract function getClasses();

    public function up()
    {
        $classes = static::getClasses();
        $foreign = [];

        foreach($classes as $index=>$class)
        {
            echo "\t Migrating: $class\r\n";
            $start = microtime(true);
            $hold = new $class;
            $_table = $hold->_tableName();

            Schema::create($_table, function (Blueprint $table) use($hold) {
                if($hold->_idMode){
                    if($hold->_idMode=='none'){
                        echo "\t \t No ID column will be added on this table\n";
                    }else{
                        $_idType = $hold->_idMode['type'];
                        $table->$_idType('id');
                        $table->primary('id');
                    }
                }else{
                    $table->bigIncrements('id');
                }
                
                $table = MigrationHelper::addFromArray($hold->atr,$table);

                foreach($hold->extraAtr as $income){
                    $table->$income();
                }
            });

            if(isset($hold->foreign)){
                array_push($foreign, $hold);
            }

            $end = number_format(microtime(true) - $start,2);
            echo "\t Migrated: $class ($end seconds)\r\n";
        }

        echo "\t Connecting Foreign Keys...\r\n";
        $start = microtime(true);
        MigrationHelper::connectForeign($foreign);
        $end = number_format(microtime(true) - $start,2);
        echo "\t Foreign Keys Connected ($end seconds)\r\n";
    }

    public function down()
    {
        $classes = static::getClasses();

        foreach($classes as $class)
        {
            $hold = new $class;
            $_table = $hold->_tableName();
            Schema::dropIfExists($_table);
        }
    }

    public function upnew(){
        $classes = collect(static::getClasses());

        echo "Analyzing Model Structures\n";
        $start = microtime(true);
        $bestOrder = self::sortByDepth($classes);
        $end = number_format(microtime(true) - $start,2);
        echo "Models analyzed ($end seconds)\n";

        $bestOrder->each(function($class){
            echo "\t Migrating: $class\r\n";
            $start = microtime(true);
            $hold = new $class;
            $_table = $hold->_tableName();

            Schema::create($_table, function (Blueprint $table) use($hold) {
                if($hold->_idMode){
                    if($hold->_idMode=='none'){
                        echo "\t \t No ID column will be added on this table\n";
                    }else{
                        $_idType = $hold->_idMode['type'];
                        $table->$_idType('id');
                        $table->primary('id');
                    }
                }else{
                    $table->bigIncrements('id');
                }
                
                $table = MigrationHelper::addFromArray($hold->atr,$table);
                if(isset($hold->foreign)){
                    $table = MigrationHelper::foreignFromArray($hold->foreign,$table);
                }

                foreach($hold->extraAtr as $income){
                    $table->$income();
                }
            });


            $end = number_format(microtime(true) - $start,2);
            echo "\t Migrated: $class ($end seconds)\r\n";
        });

    }

    public static function sortByDepth($array){
        $loops = 0;
        $done = true;
        $total = $array->count();
        $final = collect();
        while($final->count() < $total){
            if($loops > 30){
                echo "The depth of foreign keys is too high or there is an endless loop, try again with --nocheck ";
                $done = false;
                break;
            }
            $toremove = collect();
            $array->each(function($item,$pos)use(&$final,&$toremove){
                $obj = new $item;
                $istherekey = false;
                if(isset($obj->foreign)){
                    foreach($obj->foreign as $foreign){
                        if(!is_array($foreign['class'])){
                            if($final->search($foreign['class'])===false){
                                $istherekey = true;
                            }
                        }
                    }
                }
                if(!$istherekey){
                    $final->push($item);
                    $toremove->push($pos);
                }
            });
            $toremove->sort()->reverse()->each(function($removepos)use(&$array){
                $array->splice($removepos,1);
            });
            $loops++;
        }

        return $final;
    }

}
