<?php namespace Markfee\Responder;

use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;

class RepositoryResponse {
  private $status_code = ResponseCodes::HTTP_OK;
  private $messages;
  private $errors = null;
  private $data;
  private $paginator;

  /**
   * @return MessageBag
   */
  private function errors() {
    if (empty($this->errors)) {
      $this->errors = new MessageBag;
    }
    return $this->errors;
  }

  /**
   * @return MessageBag
   */
  private function messages() {
    if (empty($this->messages)) {
      $this->messages = new MessageBag;
    }
    return $this->messages;
  }

  public function NotFound($msg = "Record not found") {
    return $this->WithError($msg, ResponseCodes::HTTP_NOT_FOUND);
  }

  public function ReferentialIntegrityError($msg = "Record not found") {
    return $this->WithError($msg, ResponseCodes::HTTP_CONFLICT);
  }

  public function QueryException(QueryException $e) {
    switch ($e->getCode()) {
      case 23000:
        $this->raiseError("This record can't be deleted as it is referenced by another record");
        return $this->ReferentialIntegrityError($e->getMessage());
    }
    return $this->InternalError($e->getMessage());
  }

  public function ValidationFailed($msg = "Validation failed - required fields missing.") {
    return $this->WithError($msg, ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
  }

  public function NotAuthorised($msg = "Forbidden request") {
    return $this->WithError($msg, ResponseCodes::HTTP_UNAUTHORIZED);
  }

  public function NotLoggedIn($msg = "You must be logged in ") {
    return $this->WithError($msg, ResponseCodes::HTTP_UNAUTHORIZED);
  }

  public function WithError($msg, $statusCode) {
    $this->raiseError($msg, $statusCode);
    return $this->respond();
  }

  public function InternalError($msg = "Internal Server Error") {
    $this->raiseError($msg, ResponseCodes::HTTP_INTERNAL_SERVER_ERROR);
    return $this->respond();
  }

  protected function Created($data = null, $msg = "Successfully created a new record.") {
    $this->setData($data);
    return $this->Success($msg, ResponseCodes::HTTP_CREATED);
  }

  /**
   * @return bool
   */
  public function getCreatedStatus() {
    return $this->getStatusCode() == ResponseCodes::HTTP_CREATED;
  }

  protected function Found($data = null, $msg = "record found.") {
    $this->setData($data);
    return $this->Success($msg, ResponseCodes::HTTP_FOUND);
  }

  /**
   * @return bool
   */
  public function getFoundStatus() {
    return $this->getStatusCode() == ResponseCodes::HTTP_FOUND;
  }


  public function Updated($data = null, $msg = "Updated record successfully.") {
    $this->setData($data);
    return $this->Success($msg, ResponseCodes::HTTP_OK);
  }

  public function Success($msg = "", $status_code = ResponseCodes::HTTP_OK) {
    $this->setStatusCode($status_code);
    $this->addMessage($msg);
    return $this->respond();
  }

  public function respond($headers = []) {
    return $this;
  }

  /**
   * @param null $data
   * @param array $headers
   * @return \Illuminate\Http\JsonResponse
   */
  public function Raw($data = null, $headers = []) {
    $this->setData($data);
    return \Response::json($this->data, $this->getStatusCode(), $headers);
  }

  public function setData($data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }

  public function getErrors() {
    return $this->errors;
  }

  public function WithErrors(MessageBag $messageBag) {
    $this->errors = $messageBag;
    return $this->respond();
  }

  public function Paginated($paginator, array $records) {
    $this->setPaginator($paginator);
    $this->setData($records);
    return $this->respond();
  }

  /**
   * @param Paginator $paginator
   */
  public function setPaginator(Paginator $paginator) {
    $next = $paginator->getLastPage() > $paginator->getCurrentPage() ? $paginator->getCurrentPage() + 1 : null;
    $previous = $paginator->getCurrentPage() > 1 ? $paginator->getCurrentPage() - 1 : null;
    $this->paginator = [
      'total' => $paginator->getTotal(),
      'per_page' => $paginator->getPerPage(),
      'current_page' => $paginator->getCurrentPage(),
      'last_page' => $paginator->getLastPage(),
      'from' => $paginator->getFrom(),
      'to' => $paginator->getTo(),
      'next' => $next,
      'previous' => $previous,
    ];
  }

  public function raiseError($msg, $statusCode = 0) {
    $this->errors()->add("error", $msg);
    if ($statusCode > 0) {
      $this->setStatusCode($statusCode);
    }
  }

  /**
   * @param $msg
   */
  public function addMessage($msg) {
    if (!empty($msg)) {
      $this->messages()->add("message", $msg);
    }
  }

  public function setStatusCode($code) {
    $this->status_code = $code;
  }

  public function getStatusCode() {
    return $this->status_code;
  }

  public function hasErred() {
    return $this->status_code >= 400;
  }
} 