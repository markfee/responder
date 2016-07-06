<?php namespace Markfee\Responder\Responder;

class MessageSet {
	private $message_sets = [];

    public function __construct($message_sets = [])
    {
        $this->message_sets = $message_sets;
    }

    public function add($key, $message)
    {
    	if (!empty($message)) {
            $this->message_sets[$key] = empty($this->message_sets[$key]) ? [] : $this->message_sets[$key];
            if (is_array($message)) {
                $this->message_sets[$key] = array_merge($this->message_sets[$key], $message);
            } else {
                $this->message_sets[$key][] = $message;     
            }
    	}
    }

    public function get($key = null)
    {
        if(!is_null($key) AND !array_key_exists($key, $this->message_sets)){
            return [];
        }
    
    	return $key ? $this->message_sets[$key] : $this->message_sets;
    }
}