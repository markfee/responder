<?php namespace Markfee\Responder\Responder;

use Illuminate\Database\QueryException;
use Illuminate\Pagination\AbstractPaginator as Paginator;
use Illuminate\Support\MessageBag;

/*
 * ResponderInterface is the interface for version 2 of the responder.
 * it replaces the need for inheritance with the requirement to implement an interface instead.
 * the functionality of the interface is defined with the ResponderTrait
 */
interface ResponderInterface {

    /**
     * @return ResponderInterface
     */
    public function Found($data = null, $msg = "Record found");

    /**
     * @return ResponderInterface
     */
    public function NotFound($msg = "Record not found");

}
