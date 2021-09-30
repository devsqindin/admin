<?php

namespace App\BReAD;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class BReADPivot extends Pivot
{
    //Attributes that laravel manages (timestamp, softdelete, remembertoken) by default only timestamps is added
    public $extraAtr = ['timestamps'];
    
    public function __construct(array $in=array()){
        parent::__construct($in);

        if($this->Resources){
            $hold = $this->Resources;

            $this->foreign = [
                Str::snake(class_basename($hold[0])).'_id' => [
			        'class'=>$hold[0],
                    'relation'=>['belongsToMany','belongsToMany']
		        ],
                Str::snake(class_basename($hold[1])).'_id' => [
                    'class'=>$hold[1],
                    'relation'=>['belongsToMany','belongsToMany']
		        ]
            ];
        }
    }

    public function btmPivoted($subject){
        if(class_basename($subject)==class_basename($this->Resources[0])){
            $target = $this->Resources[1];
        }else{
            $target = $this->Resources[0];
        }
        if(isset($this->atr)){
            return $subject->belongsToMany($target,$this->_tableName())->withPivot(array_merge(['id','created_at','updated_at'],array_keys($this->atr)));
        }else{
            return $subject->belongsToMany($target,$this->_tableName())->withPivot(['id','created_at','updated_at']);
        }
    }

    public function _tableName() {
		return $this->table ?? Str::snake(Str::singular(class_basename($this)));
	}
}