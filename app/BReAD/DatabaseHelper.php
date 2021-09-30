<?php

namespace App\BReAD;

use DB;

class DatabaseHelper
{
    public static function changeDatabase($new,$type='mysql')
    {
        if($new==""){
            return DB::connection($type);
        }
        if(is_null(config('database.connections.'.$new))){
            config(['database.connections.'.$new => [
                'driver' => $type,
                'host' => env('DB_HOST', '127.0.0.1'),
                'port'=>env('DB_PORT', '3306'),
                'database' => $new,
                'username' => env('DB_USERNAME', 'forge'),
                'password' => env('DB_PASSWORD', '')
            ]]);
        }
        return DB::connection($new);
    }
}