<?php namespace Markfee\Responder;

use Illuminate\Support\MessageBag;

class Response implements ResponseInterface {
  private $status_code  = ResponseCodes::HTTP_OK;
  private $messages     ;
  private $errors       ;
  private $data         ;
  private $paginator    ;

  function __construct() {
    $this->status_code  = ResponseCodes::HTTP_OK;
    $this->messages     = null;
    $this->errors       = null;
    $this->data         = null;
    $this->paginator    = null;
  }

  public function addError($msg, $statusCode) {
    // TODO: Implement addError() method.
  }

  public function setData($data) {
    // TODO: Implement setData() method.
  }

  /** @param MessageBag $paginator */
  public function addMessageBag(MessageBag $messageBag) {
    // TODO: Implement addMessageBag() method.
  }

  /** @param Paginator $paginator */
  public function setPaginator(Paginator $paginator) {
    // TODO: Implement setPaginator() method.
  }

  public function raiseError($msg, $statusCode = 0) {
    // TODO: Implement raiseError() method.
  }

  /** @param $msg */
  public function addMessage($msg) {
    // TODO: Implement addMessage() method.
  }

  public function setStatusCode($code) {
    // TODO: Implement setStatusCode() method.
  }

  public function getStatusCode() {
    // TODO: Implement getStatusCode() method.
  }

  public function hasErred() {
    // TODO: Implement hasErred() method.
  }
}