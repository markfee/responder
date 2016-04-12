<?php namespace Markfee\Responder\Responder;

use Illuminate\Database\QueryException;
use Illuminate\Pagination\AbstractPaginator as Paginator;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
/*
 * ResponderInterface is the interface for version 2 of the responder.
 * it replaces the need for inheritance with the requirement to implement an interface instead.
 * the functionality of the interface is defined with the ResponderTrait
 */
trait ResponderTrait {
    use TransformTrait;
    use PaginatorTrait;    

    protected function FoundOr404($data, $msg = null) 
    {
        return count($data) 
            ? $this->Found($data) 
            : $this->NotFound($msg);
    }

    /**
     * @return Response
     */
    protected function Found($data = null, $msg = null) 
    {
        return (new Response())
            ->withMessage($msg)
            ->withData($this->transform($data))
            ->withStatusCode(ResponseCodes::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function NotFound($msg = null) 
    {
        return (new Response())
            ->withError($msg)
            ->withStatusCode(ResponseCodes::HTTP_NOT_FOUND);
    }

    /**
     * @return Response
     */
    protected function Created($data = null, $msg = null) 
    {
        return (new Response())
            ->withMessage($msg)
            ->withData($this->transform($data))
            ->withStatusCode(ResponseCodes::HTTP_CREATED);
    }

    /**
     * @return Response
     */
    protected function Deleted($msg = "successful delete") {
        return $this->Found(null, $msg, ResponseCodes::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function Updated($data = null, $msg = "Updated record successfully.") {
        return $this->Found($data, $msg, ResponseCodes::HTTP_OK);
    }



    /**
     * @return Response
     */
    protected function WithError($msg, $statusCode) {
        return (new Response())
            ->withError($msg)
            ->withStatusCode($statusCode);
    }

    /**
     * @return Response
     */
    public function ReferentialIntegrityError($msg = null) {
        return $this->WithError($msg, ResponseCodes::HTTP_CONFLICT);
    }

    /**
     * @return Response
     */
    public function ValidationFailed($msg = null) 
    {
        return (new Response())
            ->withError($msg)
            ->withStatusCode(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return Response
     */
    public function NotAuthorised($msg = "Forbidden request") {
        return $this->WithError($msg, ResponseCodes::HTTP_FORBIDDEN);
    }

    /**
     * @return Response
     */
    public function NotLoggedIn($msg = "You must be logged in ") {
        return $this->WithError($msg, ResponseCodes::HTTP_UNAUTHORIZED);
    }


    public function TransformAndValidate($data, $rules, $success_callback, $fail_callback = null)
    {
        $transformed_data = $this->transformInput($data);
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = \Validator::make($transformed_data, $rules);
        if ($validator->fails()) {
            return $fail_callback ? $fail_callback($validator)
                : $this->ValidationFailed($validator->getMessageBag()->all());
        }
        try {
            return $success_callback($transformed_data);    
        } catch (\Exception $ex) {
            return (new Response())
                ->withError("The record failed to save")
                ->withStatusCode(ResponseCodes::HTTP_INTERNAL_SERVER_ERROR);
            // TODO inject a Logger to Log This.                
        }
    }

}
