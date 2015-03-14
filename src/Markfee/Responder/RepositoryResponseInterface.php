<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 14/03/15
 * Time: 07:13
 */
namespace Markfee\Responder;

use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\MessageBag;

interface RepositoryResponseInterface {

//  function __construct(TransformerInterface $transformer);

    public function NotFound($msg = "Record not found");

    public function ReferentialIntegrityError($msg = "Record not found");

    public function QueryException(QueryException $e);

    public function ValidationFailed($msg = "Validation failed - required fields missing.");

    public function NotAuthorised($msg = "Forbidden request");

    public function NotLoggedIn($msg = "You must be logged in ");

    public function WithError($msg, $statusCode);

    public function InternalError($msg = "Internal Server Error");

    /**
     * @return bool
     */
    public function isCreated();

    /**
     * @return bool
     */
    public function isDeleted();

    /**
     * @return bool
     */
    public function isConflicted();

    /**
     * @return bool
     */
    public function isFound();

    /**
     * @return bool
     */
    public function isFoundOrCreated();

    public function Updated($data = null, $msg = "Updated record successfully.");

    public function respond($headers = []);

    /**
     * @param null $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function Raw($data = null, $headers = []);

    public function setData($data);

    public function getData();

    public function getErrors();

    public function getMessages();

    public function WithErrors(MessageBag $messageBag);

    public function Paginated(Paginator $paginator);

    /**
     * @param Paginator $paginator
     */
    public function setPaginator(Paginator $paginator);
    public function getPaginator();



    public function raiseError($msg, $statusCode = 0);

    /**
     * @param $msg
     */
    public function addMessage($msg);

    public function setStatusCode($code);

    public function getStatusCode();

    public function hasErred();
}