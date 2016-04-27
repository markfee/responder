<?php namespace Markfee\Responder\Responder;

trait MessageSetTrait {
	private $message_set = null;

	protected function getMessageSet($key = null)
	{
		return $this->MessageSet()->get($key);
	}

	protected function MessageSet($message_sets = [])
	{
		return $this->message_set ?: $this->message_set = new MessageSet($message_sets);
	}

}
