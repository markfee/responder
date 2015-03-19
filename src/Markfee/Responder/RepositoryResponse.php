<?php namespace Markfee\Responder;

use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;

class RepositoryResponse implements RepositoryResponseInterface {

    function __construct(TransformerInterface $transformer) {
        $this->transformer = $transformer;
    }

    use TransformableTrait;

    private $status_code = ResponseCodes::HTTP_OK;
    private $messages;
    private $errors = null;
    private $data;
    private $paginator;
    private $deletedFlag = false;
    private $updatedFlag = false;

    protected function reset() {
        $this->status_code = ResponseCodes::HTTP_OK;
        $this->messages = null;
        $this->errors = null;
        $this->data = null;
        $this->paginator = null;
        $this->deletedFlag = false;
        $this->updatedFlag = false;
    }

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
        $this->reset();
        $this->raiseError($msg, $statusCode);
        return $this;
    }

    public function InternalError($msg = "Internal Server Error") {
        $this->reset();
        $this->raiseError($msg, ResponseCodes::HTTP_INTERNAL_SERVER_ERROR);
        return $this;
    }

    protected function Created($data = null, $msg = "Successfully created a new record.") {
        $this->reset();
        $this->setData($data);
        return $this->Success($msg, ResponseCodes::HTTP_CREATED);
    }

    /**
     * @return bool
     */
    public function isCreated() {
        return $this->getStatusCode() == ResponseCodes::HTTP_CREATED;
    }

    protected function Found($data = null, $msg = "record found.") {
        $this->reset();
        $this->setData($data);
        return $this->Success($msg, ResponseCodes::HTTP_FOUND);
    }

    protected function Deleted($msg = "successful delete") {
        $this->reset();
        $this->deletedFlag = true;
        return $this->Success($msg, ResponseCodes::HTTP_OK);
    }

    /**
     * @return bool
     */
    public function isDeleted() {
        return (($this->deletedFlag === true) && $this->getStatusCode() == ResponseCodes::HTTP_OK);
    }

    /**
     * @return bool
     */
    public function isUpdated() {
        return (($this->updatedFlag === true) && $this->getStatusCode() == ResponseCodes::HTTP_OK);
    }

    /**
     * @return bool
     */
    public function isConflicted() {
        return $this->getStatusCode() == ResponseCodes::HTTP_CONFLICT;
    }

    /**
     * @return bool
     */
    public function isFound() {
        return ($this->getStatusCode() == ResponseCodes::HTTP_FOUND);
    }

    /**
     * @return bool
     */
    public function isFoundOrCreated() {
        return ($this->getStatusCode() == ResponseCodes::HTTP_FOUND) || $this->isCreated();
    }

    public function Updated($data = null, $msg = "Updated record successfully.") {
        $this->reset();
        $this->updatedFlag = true;
        $this->setData($data);
        return $this->Success($msg, ResponseCodes::HTTP_OK);
    }

    private function Success($msg = "", $status_code = ResponseCodes::HTTP_OK) {
        $this->setStatusCode($status_code);
        $this->addMessage($msg);
        return $this;
    }

    /**
     * @deprecated required for backwards compatibility
     * @return $this
     */
    public function respond() {
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
        return $this;
    }

    public function Paginated(Paginator $paginator) {
        $this->setPaginator($paginator);
        $this->setData($this->transformCollection($paginator->all()));
        return $this;
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

    public function getMessages() {
        return $this->messages()->toArray();
    }

    public function getPaginator() {
        return $this->paginator;
    }
}