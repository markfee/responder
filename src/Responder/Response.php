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

    public function getErrors() {
        return $this->errors;
    }

    public function getMessages() {
        return $this->messages;
    }

    public function getStatusCode() {
        return $this->status_code;
    }

    public function getPaginator() {
        return $this->paginator;
    }

    /**
     * @param null $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     * returns a Response object for restful requests
     */
    public function jsonResponse($headers = []) 
    {
        $response = \Response::json([
             "data"         => $this->getData()
            , "errors"      => $this->getErrors()
            , "messages"    => $this->getMessages()
            , "status_code" => $this->getStatusCode()
            , "paginator"   => $this->getPaginator()
        ],  $this->getStatusCode(), $headers);
        return $response;
    }

}
