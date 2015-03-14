<?php namespace Markfee\Responder;

use Markfee\Responder\Transformer;
use \Illuminate\Pagination\Paginator;
trait TransformableTrait {

  /**
   * @var Transformer
   */
  protected $transformer = null;

  private function getTransformer()
  {
    return $this->transformer;
  }

  /**
   * @param $record
   * @return mixed
   */
  protected function transform($record) {
    $transformer = $this->getTransformer();
    if (empty($transformer)) {
      return $record;
    }
    if (is_array($record)) {
      $record["ISARRAY"] = true;
      dd($record);
    } if(is_object($record)) {
      if ($record instanceof Paginator) {
        return $transformer->transformCollection($record->all());
      } elseif ($record instanceof \Illuminate\Database\Eloquent\Collection) {
        return $transformer->transformCollection($record->all());
      }
    }
    return $transformer->transform($record);
  }

  private function transformPagination(Paginator $paginator)
  {
    $next     = $paginator->getLastPage() > $paginator->getCurrentPage() ? $paginator->getCurrentPage() + 1 : null;
    $previous = $paginator->getCurrentPage() > 1 ? $paginator->getCurrentPage() - 1 : null;
    return [
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

  /**
   * @param $record
   * @return mixed
   */
  protected function transformInput($record) {
    $transform = $this->getTransformer();
    return $transform ? $transform->transformInput($record) : $record;
  }

  /**
   * @param $record
   * @return mixed
   */
  protected function transformCollection($record) {
    $transform = $this->getTransformer();
    return $transform ? $transform->transformCollection($record) : $record;
  }

}