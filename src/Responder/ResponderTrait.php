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

    /**
     * @return ResponderInterface
     */
    protected function Found($data = null, $msg = "record found.") {
    	return (new Response())
    		->withData($this->transform($data))
    		->withStatusCode(ResponseCodes::HTTP_OK);
    }

    /**
     * @return ResponderInterface
     */
    public function NotFound($msg = "Record not found") {
    	dd("Not Found");
    }

}
