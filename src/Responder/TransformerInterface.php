<?php namespace Markfee\Responder\Transformer;

interface TransformerInterface {

  public function transform($record);

  public function transformCollection(array $records);

  public function transformInput($record);

}