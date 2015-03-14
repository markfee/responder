<?php namespace Markfee\Responder;

use Markfee\Responder\Transformer;

interface TransformableInterface {

  /**
   * @return Transformer
   */
  function getTransformer();


}