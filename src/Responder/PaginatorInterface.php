<?php namespace Markfee\Responder\Responder;

use Illuminate\Pagination\AbstractPaginator as Paginator;

interface PaginatorInterface {

    /**
     * @return Integer
     */
    public function RecordsPerPage();

    /**
     * @return Integer
     */
    public function Fields();

	/** 
	 *	@return Response
	 */
    public function Paginated(Paginator $paginator);

}
