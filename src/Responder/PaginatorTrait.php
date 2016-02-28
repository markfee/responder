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
trait PaginatorTrait {
    protected $records_per_page = 100;
    protected $fields = ["*"];

    /**
     * @return Integer
     */
    public function RecordsPerPage()
    {
        return $this->records_per_page;
    }

    /**
     * @return Integer
     */
    public function Fields()
    {
        return $this->fields;
    }

    /**
     * @return PaginatorInterface
     */
    public function withRecordsPerPage($records_per_page)
    {
        $this->records_per_page = $records_per_page;
        return $this;
    }

    /**
     * @return PaginatorInterface
     */
    public function withFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /** 
     *  @return Response
     */
    public function Paginated($model, $not_found_message = "") 
    {
        $paginator = 
              ($model instanceof Paginator ) ? $model
            : $model->paginate($this->RecordsPerPage(), $this->Fields());

        if (count($paginator) === 0) {
            return $this->NotFound($not_found_message);
        }

        return (new Response())
            ->withPaginator($this->PaginatorToArray($paginator))
            ->withData($this->transformCollection($paginator->all()))
            ->withStatusCode(ResponseCodes::HTTP_OK);
    }    

    /** 
     *  @return Array
     */
    private function PaginatorToArray(Paginator $paginator) 
    {
        // TODO - Check if we agree to return no paginator if the data count is smaller than the page size
        if (count($paginator) >= $paginator->total()) {
            return [];
        }

        $next       = $paginator->lastPage() > $paginator->currentPage() ? $paginator->currentPage() + 1 : null;
        $previous   = $paginator->currentPage() > 1 ? $paginator->currentPage() - 1 : null;
        return ["paginator" => [
                    'total'         => $paginator->total(),
                    'per_page'      => $paginator->perPage(),
                    'current_page'  => $paginator->currentPage(),
                    'last_page'     => $paginator->lastPage(),
                    'from'          => $paginator->firstItem(),
                    'to'            => $paginator->lastItem(),
                    'next'          => $next,
                    'previous'      => $previous,
                ]];
    }

}
