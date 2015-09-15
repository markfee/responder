<?php namespace Markfee\Responder;

use \Illuminate\Support\MessageBag;


interface ResponseInterface {

  public function addError($msg, $statusCode);
  public function setData($data);
  /** @param MessageBag $paginator   */
  public function addMessageBag(MessageBag $messageBag);
  /** @param Paginator $paginator   */
  public function setPaginator(Paginator $paginator);
public function raiseError($msg, $statusCode = 0);

  /** @param $msg   */
  public function addMessage($msg);
  public function setStatusCode($code);
  public function getStatusCode();
  public function hasErred();
}