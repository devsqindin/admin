<?php

namespace App\BReAD;

use DB;
use Illuminate\Support\Collection;

class ControllerHelper 
{
    public static function store($model,$request)
    {
        $answer = DB::transaction(function() use($model,$request){
            $atr = $model->atr;
            foreach($atr as $name=>$info){
                $test[$name] = $info['laravelValidation'] ?? '';
            }

            $foreign = $model->foreign;

            if(isset($foreign)){
                foreach($foreign as $name=>$info){
                    $test[$name] = $info['laravelValidation'] ?? '';
                }
            }

            $request->validate($test);

            $new = $request->all();

            foreach($atr as $name=>$info)
            {
                $type = $info['prototype'] ?? '';

                if($type=='upload')
                {
                    $file = $request->file($name);
                    $filename = $model->fileName($name);
                    $folderpath = $model->folderPath($name);
                    $file->move($folderpath, $filename);
                    $new[$name] = $filename;
                }
            }

            try{
                $item = $model->create($new);
            }catch(Exception $err){
                return ['error'=>true,'message'=>$err];
            }
            return ['success'=>true,'item'=>$item];
        });
        return $answer;
    }

    public static function update($model,$item,$request)
    {
        $answer = DB::transaction(function() use ($model,$item,$request){
            $atr = $model->atr;
            foreach($atr as $name=>$info)
            {
                if(isset($request->$name)){
                    $test = $info['laravelValidation'] ?? '';
                    $type = $info['prototype'] ?? '';
                    $request->validate([$test]);
                    if($type=='upload')
                    {
                        $file = $request->file($name);
                        if($file!=null)
                        {
                            $filename = $model->fileName($name);
                            $folderpath = $model->folderPath($name);
                            $file->move($folderpath, $filename);
                            $item->$name = $filename;
                        }
                    }else{
                        $item->$name = $request->$name;
                    }
                }
            }

            $foreign =$model->foreign;

            if(isset($foreign))
            {
                foreach($foreign as $name=>$fkey)
                {
                    if(isset($request->$name)){
                        $test = $info['laravelValidation'] ?? '';
                        $request->validate([$test]);
                        $item->$name = $request->$name;
                    }
                }
            }
            try{
                $item->save();
            }catch(Exception $err){
                return ['error'=>true,'message'=>$err];
            }
            return ['success'=>true,'item'=>$item];
        });
        return $answer;
    }

    public static function canDig($model)
    {
        $willDig = false;
        if(isset($model->foreign)){
            foreach($model->foreign as $one){
                if(isset($one['apiPull'])){
                    $willDig = true;
                    break;
                }
            }
        }
        return $willDig;
    }

    public static function dig($items,$model,$single=false)
    {
        $hold = self::canDig($model);
        $result = $items;
        if($hold){
            if($single){
                $result = collect([$result]);
            }
            $result = $result->each(function($item){
                if(isset($item->foreign)){
                    foreach($item->foreign as $key=>$connect){
                        if($connect['relation'][0]=='belongsTo'){
                            if(!is_null($item->$key) && isset($connect['apiPull'])){
                                $hover = $connect['apiPull'];
                                $step = $connect['class']::find($item->$key);
                                if(!is_null($step) && $hover=='all'){
                                    $item->$key = $step;    
                                }else{
                                    $item->$key = $step->$hover;
                                }
                            }
                        }
                    }
                }
            });
            if($single && $result instanceof Collection){
                $result = $result->first();
            }
        }
        
        return $result;
    }
}