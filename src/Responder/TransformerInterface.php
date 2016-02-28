<?php namespace Markfee\Responder\Responder;

interface TransformerInterface {

  public function transform($record);

  public function transformCollection(array $records);

  public function transformInput($record);

}