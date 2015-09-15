<?php namespace Markfee\Responder;

use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;

class Response implements ResponseInterface
{
    private $status_code = ResponseCodes::HTTP_OK;
    private $messages;
    private $errors;
    private $data;
    private $paginator;

    function __construct()
    {
        $this->status_code = ResponseCodes::HTTP_OK;
        $this->messages = null;
        $this->errors = null;
        $this->data = null;
        $this->paginator = null;
    }

    public function addError($msg, $statusCode)
    {
        // TODO: Implement addError() method.
    }

    public function setData($data)
    {
        // TODO: Implement setData() method.
    }

    /** @param MessageBag $paginator */
    public function addMessageBag(MessageBag $messageBag)
    {
        // TODO: Implement addMessageBag() method.
    }

    /** @param Paginator $paginator */
    public function setPaginator(Paginator $paginator)
    {
        // TODO: Implement setPaginator() method.
    }

    public function raiseError($msg, $statusCode = 0)
    {
        // TODO: Implement raiseError() method.
    }

    /** @param $msg */
    public function addMessage($msg)
    {
        // TODO: Implement addMessage() method.
    }

    public function setStatusCode($code)
    {
        $this->status_code = $code;
    }

    public function getStatusCode()
    {
        return $this->status_code;
    }

    public function hasErred()
    {
        // TODO: Implement hasErred() method.
    }
}