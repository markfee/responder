<?php namespace Markfee\Responder;
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 25/01/15
 * Time: 12:06
 */

interface TransformerInterface {

  public function transformCollection(array $records, $with = []);

  public static function transform($record);

  public static function transformInput($record);

  public function transformWith($record, $with = []);

  public static function nest($arrayToTransform);

}