<?php

namespace App\BReAD;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BReADResource extends Model
{
	//Just a quick adjustment for mass assignment feel free to replace it with your own
	protected $guarded = ['id'];

	//Attributes that laravel manages (timestamp, softdelete, remembertoken) by default only timestamps is added
	public $extraAtr = ['timestamps'];
	/*
		Replacable functions

		You may replace this functions on your model to give the desired result

	*/
	public function displayName()
	{
		//Returns a string to be show as a display name for an item
		return $this->_tableName();
	}

	public function fileName($in)
	{
		/*
			$in is the name of the column that was set to be a file upload
			Returns the desired name to be saved
				(the name should be unique to avoid overwritting existing files)
		*/
		return 'file'.time();
	}

	public function folderPath($in)
	{
		/*
			$in is the name of the column that was set to be a file upload
			Returns the desired public folder path it should place the file
		*/
		return '';
	}

	//Inner functions (Shouldn't be replaced)
	public function _tableName() {
		return $this->table ?? Str::snake(Str::pluralStudly(class_basename($this)));
	}

	public function _callRelation($in){
		if(isset($this->foreign)){
			$hold = $this->foreign;
			foreach($hold as $key=>$info){
				if(class_basename($info['class'])==class_basename($in)){
					$final = $info['relation'][0];
					return $this->$final($in);
				}
			}
		}
		$hold2 = new $in;
		if(isset($hold2->foreign)){
			$hold3 = $hold2->foreign;
			foreach($hold3 as $key=>$info){
				if(class_basename($info['class'])==class_basename($this)){
					$final = $info['relation'][1];
					return $this->$final($in);
				}
			}
		}
		return $this->belongsTo($in);
	}

	public function getRelations(){
		$foreign = $this->foreign;
		if(isset($foreign)){
	        foreach ($foreign as $name=>$info){
				if(is_array($info['class'])){

				}else{
					$flop = new $info['class'];
					if(isset($this->$name)){
						$flop = $flop->find($this->$name)->attributes;
					}
					$hold[$name] = $flop;
				}
	        }
	        return $hold;
    	}
    	return false;
    }

    public function fillDetails($step,$foreign=false)
    {	
		$forms = new StyleForm(['']);

		if($foreign){
			if(isset($this->foreignDetails[$step])){
				$forms = new StyleForm($this->foreignDetails[$step]);
			}
		}else{
    		if(isset($this->atrDetails[$step])){
				$forms = new StyleForm($this->atrDetails[$step]);
			}
		}

        return $forms;
	}
}
