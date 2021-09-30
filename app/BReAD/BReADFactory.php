<?php

namespace App\BReAD;

use \Illuminate\Database\Eloquent\Factory;
use Faker\Generator as Faker;

class BReADFactory {
    private static $matches = [
        'text'=>[
            'faker'=>'text'
        ],
        'string'=>[
            'faker'=>'word'
        ],
        'boolean'=>[
            'faker'=>'randomElement',
            'param'=>[[1,0]],
        ],
        'integer'=>[
            'faker'=>'randomNumber',
            'param'=>true,
        ],
        'float'=>[
            'faker'=>'randomFloat',
            'param'=>true,
        ],
        'char'=>[
            'faker'=>'randomLetter'
        ],
        'date'=>[
            'faker'=>'date',
            'param'=>true,
        ],
        'datetime'=>[
            'faker'=>'dateTime',
            'param'=>true
        ],
        'decimal'=>[
            'faker'=>'randomFloat',
            'param'=>true,
        ],
        'longText'=>[
            'faker'=>'text'
        ],
        'time'=>[
            'faker'=>'time',
            'param'=>true,
        ],
        'year'=>[
            'faker'=>'year',
            'param'=>true
        ]
    ];

    public static function define(array $classes, Factory $factory)
    {
        echo "Defining factories\n";
        foreach($classes as $class){
            $obj = new $class;
            $factory->define($class,function (Faker $faker) use ($obj){
                $result = [];
                if(isset($obj->atr)){
                    foreach($obj->atr as $name=>$atr){
                        if(!isset($atr['nullable']) || isset($atr['factory'])){
                            if(isset($atr['factory'])){
                                $result[$name] = self::sliptCheck($atr['factory'],$faker,$result);
                            }else{
                                $mode = self::$matches[$atr['type']];
                                $result[$name] = self::sliptCheck($mode,$faker,$result);
                            }
                        }
                    }
                }
                if(isset($obj->foreign)){
                    foreach($obj->foreign as $name=>$props){
                        if(is_array($props['class'])){
                            $position = array_rand($props['class']);
                            $class = $props['class'][$position];
                        }else{
                            $class = $props['class'];
                        }
                        if(isset($props['factory'])){
                            [$selectedID,$selectedClass] = self::foreignKeyCheck($props['factory'],$class,$props['class']);
                        }else{
                            $selectedClass = $class;
                            $selectedID = $class::inRandomOrder()->first()->id;
                        }
                        if(is_array($props['class'])){
                            $result[$name.'_type'] = $selectedClass;
                            $result[$name.'_id'] = $selectedID;
                        }else{
                            $result[$name] = $selectedID;
                        }
                    }
                }
                return $result;
            });
        }
    }

    public static function foreignKeyCheck($factory=false,$class,$classes){
        $selectedClass = $class;
        $mode = $factory;
        if($mode['mode'] == 'create'){
            $parentHold = factory($class,1)->create();
            $selectedID = $parentHold->first()->id;
        }elseif($mode['mode']=='restricted'){
            $obj = new $class;
            $query = $mode['query'];
            $params = isset($mode['param']) && !is_null($mode['param']) ? $mode['param'] : [];
            $relation = $obj->$query(...$params)->inRandomOrder()->first();
            if(is_null($relation)){
                if(is_array($classes)){
                    foreach($classes as $newClass){
                        $newObj = new $newClass;
                        $relation = $newObj->$query(...$params)->inRandomOrder()->first();
                        if(!is_null($relation)){
                            $selectedClass = $newClass;
                            break;
                        }
                    }
                    if(is_null($relation)){
                        $relation = $obj->inRandomOrder()->first();
                    }
                }else{
                    $relation = $obj->inRandomOrder()->first();
                }
            }
            $selectedID = $relation->id;
        }else{
            $selectedID = self::sliptCheck($mode,$faker,$result);
        }
        return [$selectedID,$selectedClass];
    }

    public static function sliptCheck(array $mode, Faker $faker, array $currentState)
    {
        if(isset($mode['faker'])){
            $choice = $mode['faker'];
            if(isset($mode['param'])){
                $param = $mode['param'];
                if(is_bool($param)){
                    $final = $faker->$choice();
                }else{
                    $final = $faker->$choice(...$param);
                }
            }else{
                $final = $faker->$choice;
            }
        }elseif(isset($mode['default'])){
            $final = $mode['default'];
        }elseif(isset($mode['function'])){
            $final = $mode['function']($faker,$currentState);
        }else{
            $final = 0;
        }

        return $final;
    }
}