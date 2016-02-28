<?php namespace Markfee\Responder\Responder;

class Transformer implements TransformerInterface {

  	public function transform($record) 
  	{
  		return $record;
  	}

  	public function transformInput($record) 
  	{    
  		return $record;  
  	}

	public function transformCollection(array $records) {
    	$arr = [];
	    foreach($records as $record) {
    		$arr[] = $this->transform($record);
    	}
    	return $arr;
  	}
}