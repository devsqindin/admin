<?php

namespace App\BReAD;

class StyleForm
{

	public $form = [
		'title'=>false,					//Name of variable

		//Visibility
		'OnCreate' => true,					//Visible on create?
		'OnEdit' => true,					//Visible on edit?
		'OnShow' => true,					//Visible on show?

		'DefaultCreate' =>'',

		//Validate variables
			'required'=>false,					//Adds required to input field
			'laravelValidation'=>'',			//Laravels validate stirng

		//Variables to control blade php 

			//General
			'placeholder' => '',				//Placeholder value shown on create (only on create)
			'stepper' => 0,						//If steps are being used, this sets which step the variable should show on
			'column'=>false,					//Make this variable part of a column
			'newcolumn'=>false,					//Will start a new row if set
			'divsize'=>'',						//Add Bulma column modifiers to class name

			//Helper text on the bottom of the input
			'helpsup'=>'',						//Bulma helper modifiers
			'help'=>'',							//Helper under the input
			//Boolean radio Select
			'b-choice'=>['True','False'],			//Text to display on boolean radio
			//Radio Select (Default of char variables)
			'c-option' =>['Option 1','Option 2'],	//Text to diplay on radio options
			'c-choice' =>['A','B'],					//Value to be saved on database (1:1 with c-option, in this case if you select option 1 it will save A on the database)
			//Select options
			'd-option' => ['Opt 1','Opt 2'],	//Text to display on select menu
			'd-choice' => ['OptA','OptB'],		//Values to be saved on database (1:1 with d-option, in this case if you select Opt 1 it will save OptA on the database)
			//For number inputs
			'step'=>null,						//Step of input
			'min'=>null,						//Minimun value of input
			'max'=>null,						//Maximum value of input
		

		//For foreign keys
		'foreignuse' => 'id',				//What atribute to use when on create/show/edit pages
		'foreignType' => 'string',			//What type should be shown
		'foreignAdd' => false,				//If a button to add should appear when on create/edit pages
		'foreignAddLink' => '',				//Where should the Add button lead
		'foreignEmpty' => 'Empty',			//Text when there are no connected objects

		//Others
		'extra' => '',						//For extra variables you want to pass down
		'override' => false,				//Use replacement instead of default component
		'replacement' => '',				//name of path to new blade component

		//Unused Variables (Food for thought)
		'maxLenght' => 0,					//Delimit max lenght
	];

	public function __construct($atr)
	{
		foreach($atr as $key => $value){
			if(isset($form[$key])){
				$this->form[$key] = $value;
			}
		}
	}
}