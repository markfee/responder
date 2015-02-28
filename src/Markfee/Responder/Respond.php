<?php namespace Markfee\Responder;

use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;

class Respond {

  private static $responce;

  private static function responce()
  {
    if (empty(static::$responce)) {
      static::$responce = new Response();
    }
    return static::$responce;
  }

  private static $status_code  = ResponseCodes::HTTP_OK;
  private static $messages     ;
  private static $errors       ;
  private static $data         ;
  private static $paginator    ;

  public static function Reset() {
    static::$responce = new Response();
  }

  /**
   * @return MessageBag
   */
  private static function errors() {
    if (empty(Respond::$errors)) {
      Respond::$errors = new MessageBag;
    }
    return Respond::$errors;
  }

  /**
   * @return MessageBag
   */
  private static function messages() {
    if (empty(Respond::$messages)) {
      Respond::$messages = new MessageBag;
    }
    return Respond::$messages;
  }
  
  
  public static function NotFound($msg = "Record not found") {
    return Respond::WithError($msg, ResponseCodes::HTTP_NOT_FOUND);
  }

  public static function ReferentialIntegrityError($msg = "Record not found") {
    return Respond::WithError($msg, ResponseCodes::HTTP_CONFLICT);
  }

  public static function QueryException(QueryException $e) {
      switch($e->getCode()) {
        case 23000:
              Respond::raiseError("This record can't be deleted as it is referenced by another record");
              return Respond::ReferentialIntegrityError($e->getMessage());
    }
    return Respond::InternalError($e->getMessage());
  }

  public static function ValidationFailed($msg = "Validation failed - required fields missing.") {
    return Respond::WithError($msg, ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
  }

  public static function NotAuthorised($msg = "Forbidden request") {
    return Respond::WithError($msg, ResponseCodes::HTTP_UNAUTHORIZED);
  }

  public static function NotLoggedIn($msg = "You must be logged in ") {
    return Respond::WithError($msg, ResponseCodes::HTTP_UNAUTHORIZED);
  }

  public static function WithError($msg, $statusCode) {
    Respond::raiseError($msg, $statusCode);
    return Respond::respond();
  }

  public static function InternalError($msg = "Internal Server Error") {
    Respond::raiseError($msg, ResponseCodes::HTTP_INTERNAL_SERVER_ERROR);
    return Respond::respond();
  }


  public static function Created($data = null, $msg = "Successfully created a new record.") {
    Respond::setData($data);
    return Respond::Success($msg, ResponseCodes::HTTP_CREATED);
  }

  public static function Updated($data = null, $msg = "Updated record successfully.") {
    Respond::setData($data);
    return Respond::Success($msg, ResponseCodes::HTTP_OK);
  }

  public static function Success($msg = "", $status_code = ResponseCodes::HTTP_OK) {
    Respond::setStatusCode($status_code);
    Respond::addMessage($msg);
    return Respond::respond();
  }

  public static function respond($headers = []) {
    $response = \Response::json([
        "data"        => Respond::$data
      , "errors"      => Respond::errors()->toArray()
      , "messages"    => Respond::messages()->toArray()
      , "status_code" => Respond::getStatusCode()
      , "paginator"   => Respond::$paginator
    ],  Respond::getStatusCode(), $headers);
    return $response;
  }

  /**
   * @param null $data
   * @param array $headers
   * @return \Illuminate\Http\JsonResponse
   */
  public static function Raw($data = null, $headers = []) {
    Respond::setData($data);
    return \Response::json(Respond::$data,  Respond::getStatusCode(), $headers);
  }

  public static function setData($data) {
    Respond::$data = $data;
  }

  public static function WithErrors(MessageBag $messageBag) {
    Respond::$errors = $messageBag;
    return Respond::respond();
  }

  public static function Paginated($paginator, array $records) {
    Respond::setPaginator($paginator);
    Respond::setData($records);
    return Respond::respond();
  }

  /**
   * @param Paginator $paginator
   */
  public static function setPaginator(Paginator $paginator) {
    $next     = $paginator->getLastPage() > $paginator->getCurrentPage() ? $paginator->getCurrentPage() + 1 : null;
    $previous = $paginator->getCurrentPage() > 1 ? $paginator->getCurrentPage() - 1 : null;
    Respond::$paginator = [
      'total' =>        $paginator->getTotal(),
      'per_page' =>     $paginator->getPerPage(),
      'current_page' => $paginator->getCurrentPage(),
      'last_page' =>    $paginator->getLastPage(),
      'from' =>         $paginator->getFrom(),
      'to' =>           $paginator->getTo(),
      'next' =>         $next,
      'previous' =>     $previous,
    ];
  }

  public static function raiseError($msg, $statusCode = 0) {
    Respond::errors()->add("error", $msg);
    if ($statusCode > 0) {
      Respond::setStatusCode($statusCode);
    }
  }

  /**
   * @param $msg
   */
  public static function addMessage($msg) {
    if (!empty($msg)) {
      Respond::messages()->add("message", $msg);
    }
  }

  public static function setStatusCode($code) {
    Respond::$status_code = $code;
  }

  public static function getStatusCode() {
    return Respond::$status_code;
  }

  public static function hasErred() {
    return Respond::$status_code >= 400;
  }
} 