<?php namespace Markfee\Responder\Responder;

/*
 * Response is the actual data of the response returned to the controller
 */

class Response {
    private $status_code = 0;
    private $messages;
    private $data;
    private $paginator;
    private $errors;
    private $multipleFlag = false;
    private $validatedFlag = false;
    private $deletedFlag = false;
    private $updatedFlag = false;

    public function __construct() {
        $this->status_code      = 0;
        $this->messages         = null;
        $this->errors           = null;
        $this->data             = null;
        $this->paginator        = null;
        $this->multipleFlag     = false;
        $this->validatedFlag    = false;
        $this->deletedFlag      = false;
        $this->updatedFlag      = false;
    }

    /** @return Response */
    public function withData($data) 
    {
        $this->data = $data;
        return $this;
    }

    /** @return Response */
    public function withStatusCode($code) 
    {
        $this->status_code = $code;
        return $this;
    }

    /** @return Array */
    public function getData() {
        return $this->data;
    }

}
