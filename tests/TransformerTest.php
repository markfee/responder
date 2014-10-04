<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 04/10/14
 * Time: 07:07
 */
use Markfee\Responder\Transformer;
class TransformerTest extends TestCase {


  public function test_tester() {
    $this->assertTrue(true);
  }

  public function test_transformBy()
  {
    $top_level = [
        [
          "a"   => "UNKNOWN Category",
          "category_id"   => "UNKNOWN"
        ],
        [
          "a"   => "Category 1",
          "category_id"   => 1
        ],
        [
          "a"   => "Category 2",
          "category_id"   => 2
        ]
      ];
    $months = [
      [
        "a"   => "January",
        "category_id"   => "UNKNOWN",
        "month"   => "01"
      ],
      [
        "a"   => "february",
        "category_id"   => "UNKNOWN",
        "month"   => "02"
      ],
        [
          "a"   => "february",
          "category_id"   => 1,
          "month"   => "02"
        ],
        [
          "a"   => "category 2 - march totals",
          "category_id"   => 2,
          "month"   => "03"
        ]
      ];
    $response = Transformer::transformBy( [
        [$top_level, "category", "category_id"]
      , [$months,   "months", "month"]

    ] );
    print_r($response);
    $this->assertTrue(is_array($response));
    $this->assertTrue(is_array($response["category"]));
    $this->assertTrue(is_array($response["category"]["UNKNOWN"]));
    $this->assertTrue(is_array($response["category"][1]));
    $this->assertTrue(is_array($response["category"][2]));
    $this->assertEquals($response["category"][2]["months"]["03"]["a"], "category 2 - march totals");

  }
}