<?php namespace Markfee\Responder\Responder;

interface TransformerInterface {

  public function transform($record);

  public function transformCollection($records);

  public function transformInput($record);

}