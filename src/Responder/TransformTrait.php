<?php namespace Markfee\Responder\Responder;

use Markfee\Responder\Transformer;
use \Illuminate\Pagination\Paginator;

trait TransformTrait {

    /**
     * @var Transformer
     */
    protected $transformer = null;

    /**
     * @returns Transformer
     */
    public function getTransformer() 
    {
        return $this->transformer;
    }

    /**
     * @param Transformer
     * @returns Transformer
     */
    public function setTransformer($transformer) 
    {
        $this->transformer = $transformer;
        return $this;
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
            return $transformer->transformCollection($record);
        }
        if (is_object($record)) {
            if ($record instanceof Paginator) {
                return $transformer->transformCollection($record->all());
            } elseif ($record instanceof \Illuminate\Database\Eloquent\Collection) {
                return $transformer->transformCollection($record->all());
            }
        }
        return $transformer->transform($record);
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