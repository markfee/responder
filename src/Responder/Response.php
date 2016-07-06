<?php namespace Markfee\Responder\Responder;

/*
 * Response is the actual data of the response returned to the controller
 */

class Response {

    use MessageSetTrait;

    private $status_code = 0;
    private $data;
    private $paginator;

    private $multipleFlag = false;
    private $validatedFlag = false;
    private $deletedFlag = false;
    private $updatedFlag = false;

    public function __construct() {
        $this->status_code      = 0;
        $this->data             = null;
        $this->paginator        = [];
        
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

    /** @return Response */
    public function withError($msg) 
    {
        $this->MessageSet()->add("errors", $msg);
        return $this;
    }    

    /** @return Response */
    public function withMessage($msg) 
    {
        $this->MessageSet()->add("messages", $msg);
        return $this;
    } 

    /** @return Response */
    public function withPaginator($paginatorArray) 
    {
        $this->paginator = $paginatorArray;
        return $this;
    } 

    /** @return Array */
    public function getData() {
        return $this->data;
    }

    public function getStatusCode() {
        return $this->status_code;
    }

    public function getPaginator() {
        return $this->paginator;
    }

    public function getMessages() 
    {
        return $this->getMessageSet("messages");
    }

    public function getErrors() 
    {
        return $this->getMessageSet("errors");
    }

    private function toArray()
    {
        return array_merge(
            [ "data"        => $this->getData() ]
            , $this->getMessageSet()
            , $this->getPaginator()
        );
    }

    /**
     * @param null $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     * returns a Response object for restful requests
     */
    public function jsonResponse($headers = []) 
    {
        return \Response::json($this->toArray()
            , $this->getStatusCode()
            , $headers);
    }
}
