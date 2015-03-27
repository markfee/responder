<?php namespace Markfee\Responder;

use Illuminate\Support\MessageBag;

trait ErrorBagTrait {

    /**
     * @var MessageBag
     */
    private $errors = null;

    /**
     * @return MessageBag
     */
    private function errors()
    {
        if (empty($this->errors)) {
            $this->errors = new MessageBag;
        }
        return $this->errors;
    }

    public function resetErrors()
    {
        $this->errors           = null;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getErrorArray() {
        return $this->errors->get("error");
    }

    public function setErrors(MessageBag $messageBag)
    {
        $this->errors = $messageBag;
        return $this;
    }

    public function addErrors(MessageBag $messageBag)
    {
        foreach($messageBag->get("error") as $msg) {
            $this->addError($msg);
        }
    }

    public function getJsonErrors() {
        return $this->errors->get("error");
    }


    public function addError($msg)
    {
        $this->errors()->add("error", $msg);
    }

    public function hasErrors() {
        return (!empty($this->errors)) && ($this->errors->count() > 0);
    }
}