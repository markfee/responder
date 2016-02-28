<?php namespace Markfee\Responder\Responder;

class MessageSet {
	private $message_sets = [];

    public function add($key, $message)
    {
    	if (!empty($message)) {
    		$this->message_sets[$key][] = $message;	
    	}
    }

    public function get($key = null)
    {
    	return $key ? $this->message_sets[$key] : $this->message_sets;
    }
}