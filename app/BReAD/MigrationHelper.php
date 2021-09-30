<?php

namespace App\BReAD;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

abstract class MigrationHelper
{
    public static function manualForeign($array,$db='')
    {
        foreach($array as $index=>$content)
        {
            Schema::connection($db)->table($index,function (Blueprint $table) use($content,$array) {
                foreach($content as $key=>$item)
                {

                    $default = (!isset($item['default']) || is_null($item['default'])) ? 0 : $item['default'];
                    $onDelete = (!isset($item['onDelete']) || is_null($item['onDelete'])) ? 'cascade' : $item['onDelete'];
                    $onUpdate = (!isset($item['onUpdate']) || is_null($item['onUpdate'])) ? 'cascade' : $item['onUpdate'];
                    $customKey = (!isset($item['customKey']) || is_null($item['customKey'])) ? '' : $item['customKey'];
                    $database = (!isset($item['database']) || is_null($item['database'])) ? '' : $item['database'].'.';
                    $nullable = (!isset($item['nullable']) || is_null($item['nullable'])) ? false : $item['nullable'];
                    $table->unsignedBigInteger($key.'_id')->default($default)->nullable($nullable);

                    $table->foreign($key.'_id',$customKey)->references('id')->on($database.$key)->onDelete($onDelete)->onUpdate($onUpdate);
                }
            });
        }
    }

    public static function connectForeign($foreign,$db='')
    {
        foreach($foreign as $object)
        {
            $_table = $object->_tableName();
            $content = $object->foreign;

            Schema::connection($db)->table($_table,function (Blueprint $table) use($content) {
                $table = self::foreignFromArray($content,$table);
            });
        };
    }

    public static function addFromArray($array,$table)
    {
        $specialCases = 'morphs-';
	
        if(isset($array)){
            foreach($array as $index=>$item)
            {
                $type = (!isset($item['type']) || is_null($item['type'])) ? 'string' : $item['type'];
                $params = (!isset($item['params']) || is_null($item['params'])) ? [] : $item['params'];
                $default = (!isset($item['default']) || is_null($item['default'])) ? null : $item['default'];
                $nullable = (!isset($item['nullable']) || is_null($item['nullable'])) ? false : $item['nullable'];
                
                if(strpos($specialCases,$type)>-1){
                    $table->$type($index,...$params);
                }else{
                    $table->$type($index,...$params)->default($default)->nullable($nullable);
                }
            }

            return $table;
        }
        return $table;
    }   

    public static function foreignFromArray($content,$table){
        foreach($content as $name=>$item)
        {
            $def = (!isset($item['default']) || is_null($item['default'])) ? null : $item['default'];
            $onDelete = (!isset($item['onDelete']) || is_null($item['onDelete'])) ? 'cascade' : $item['onDelete'];
            $onUp = (!isset($item['onUpdate']) || is_null($item['onUpdate'])) ? 'cascade' : $item['onUpdate'];
            $cuK = (!isset($item['customKey']) || is_null($item['customKey'])) ? '' : $item['customKey'];
            $database = (!isset($item['database']) || is_null($item['database'])) ? '' : $item['database'].'.';
            $nullable = (!isset($item['nullable']) || is_null($item['nullable'])) ? false : $item['nullable'];
            
            

            if(gettype($item['class'])=='array'){
                if($nullable){
                    $table->nullableMorphs($name);
                }else{
                    $table->morphs($name);
                }
            }else{
                $hold = new $item['class'];
                $_connectTable = $hold->_tableName();

                $table->unsignedBigInteger($name)->default($def)->nullable($nullable);
                $table->foreign($name,$cuK)->references('id')->on("$database$_connectTable")->onDelete($onDelete)->onUpdate($onUp);
            }
        };
        return $table;
    }
}
