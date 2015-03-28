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
        return $this->errors->all();
    }

    public function setErrors(MessageBag $messageBag, $key = "error")
    {
        return $this->addErrors($messageBag, $key);
    }

    public function addErrors(MessageBag $messageBag)
    {
        foreach($messageBag->getMessages() as $key => $messages) {
            foreach($messages as $key => $message){
                $this->addError($message);
            }
        }
        return $this;
    }

    public function getJsonErrors() {
        return json_encode($this->errors);//->get("error");
    }


    public function addError($msg, $key="error")
    {
        $this->errors()->add($key, $msg);
    }

    public function hasErrors() {
        return (!empty($this->errors)) && ($this->errors->count() > 0);
    }
}