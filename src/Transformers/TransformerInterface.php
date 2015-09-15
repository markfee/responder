<?php namespace Markfee\Responder\Transformer;

interface TransformerInterface {

  public function transformCollection(array $records, $with = []);

  public static function transform($record);

  public static function transformInput($record);

  public function transformWith($record, $with = []);

  public static function nest($arrayToTransform);

}