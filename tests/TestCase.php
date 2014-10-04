<?php
use Illuminate\Http\Response;

class TestCase extends Illuminate\Foundation\Testing\TestCase {
  protected $expected_status = Response::HTTP_OK;
  protected $expects_pagination = true;

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;
		$testEnvironment = 'testing';
		return require __DIR__.'/../../bootstrap/start.php';
	}

  public function assertExpectedStatus($status) {
    $this->expected_status = $status;
    return $this->assertResponseStatus($status);
  }

  public function setExpectedStatus($status) {
    $this->expected_status = $status;
    return $this;
  }


  public function seed($seederName) {
    Eloquent::unguard();
    $seeder = new DatabaseSeeder();
    $seeder->call("test".$seederName);
    Eloquent::reguard();
  }
  /**
   * Default preparation for each test
   */
  public function setUp()
  {
    parent::setUp();
    Markfee\Responder\Respond::Reset();
    Artisan::call('migrate');
    Eloquent::reguard();

  }


  protected function assertNoErrors($jsonResponse) {
    $this->assertEquals(true,  isset($jsonResponse->errors),    "Empty errors attribute expected: \n");
    if ( count($jsonResponse->errors) ) {
      $str = print_r($jsonResponse->errors, true);
      $this->assertEquals(0,  count($jsonResponse->errors),    "Unexpected errors: \n{$str}");
    }
  }

  protected function assertHasErrors($jsonResponse) {
    $this->assertEquals(true,  count($jsonResponse->errors) > 0,    "Is there an error message?");
  }

  protected function assertNewRecordHasId($jsonResponse) {
    $str = "";
    if (empty($jsonResponse->data[0]->id)){
      $str = print_r($jsonResponse->data, true);
    }

    $this->assertInternalType('integer', $jsonResponse->data[0]->id, "Data is expected to contain the inserted items id:\n{$str}");
  }

  protected function assertValidJsonError($response, $expectedResponse) {
    $this->assertEquals($expectedResponse, $response->getStatusCode(), "Expected response {$expectedResponse} got " . $response->getStatusCode());

    $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    $jsonResponse = $response->getData();
    $this->assertHasErrors($jsonResponse);
    $this->assertEquals(0,    count($jsonResponse->messages),   "Are there no messages?");
    $this->assertEquals(0,    count($jsonResponse->data) > 0,   "Is the data item empty?");
    return $jsonResponse;
  }

  protected function assertMultipleUpdate($response, $expectedCount) {
    $this->expects_pagination = false;
    $jsonResponse = $this->assertValidJsonResponse($response);
    $this->assertEquals($expectedCount, $jsonResponse->data,  "Are there exactly {$expectedCount} data items? Actual result: " . $jsonResponse->data);
    return $jsonResponse;
  }


  protected function assertNRecordsResponse($response, $expectedCount, Array $expectedFields = null, Array $unexpectedFields = null) {
    $this->expects_pagination = false;
    $jsonResponse = $this->assertValidJsonResponse($response, $expectedFields, $unexpectedFields);
    $this->assertEquals(true, count($jsonResponse->data) == $expectedCount,  "Are there exactly {$expectedCount} data items? Actual result: " . count($jsonResponse->data));
    return $jsonResponse;
  }

  protected function assertValidJsonResponse($response, Array $expectedFields = null, Array $unexpectedFields = null) {
    $jsonResponse = $response->getData();
    $this->assertNoErrors($jsonResponse);
    $this->assertEquals($this->expected_status, $response->getStatusCode(), "Expected response {$this->expected_status} got " . $response->getStatusCode());
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));
//    $this->assertEquals(0,  count($jsonResponse->messages),  "Are there no messages?");
    $this->assertEquals(true, count($jsonResponse->data) > 0,  "Is there at least one data item?");
    if ($this->expects_pagination)
      $this->assertEquals(1,  count($jsonResponse->paginator), "Is there pagination data?");
    $this->assertExpectedFields($jsonResponse->data[0], $expectedFields);
    $this->assertUnExpectedFields($jsonResponse->data[0], $unexpectedFields);
    return $jsonResponse;
  }

  protected function assertCallback($jsonResponse, $callback) {
    foreach($jsonResponse->data as $item) {
      $callback($item);
    }
  }

  protected function assertValidSingleRecordJsonResponse($response, Array $expectedFields = null, Array $unexpectedFields = null) {
    $jsonResponse = $response->getData();
    $this->assertEquals($this->expected_status, $response->getStatusCode(), "Expected response {$this->expected_status} got " . $response->getStatusCode());
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    $this->assertTrue(true, count($jsonResponse) == 1,  "Is there exactly one data item?");
    $this->assertExpectedFields($jsonResponse, $expectedFields);
    $this->assertUnExpectedFields($jsonResponse, $unexpectedFields);
    return $jsonResponse;
  }

  protected function assertExpectedFields($record, $expectedFields) {
    if (!empty($expectedFields)) {
      foreach($expectedFields as $field) {
        $this->assertEquals(true, array_key_exists($field, $record),  "Does the data have the field: '{$field}'?");
      }
    }
  }
  protected function assertUnExpectedFields($record, $unexpectedFields) {
    if (!empty($unexpectedFields)) {
      foreach($unexpectedFields as $field) {
        $this->assertEquals(false, array_key_exists($field, $record),  "Does the data NOT have the field: '{$field}'?");
      }
    }
  }

  protected function assertValidInsertJsonResponse($response) {
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));

    $jsonResponse = $response->getData();
    $this->assertNoErrors($jsonResponse);
    $this->assertEquals(1, count($jsonResponse->messages), "A single message is expected");
    $this->assertEquals(1, count($jsonResponse->data),     "Data is expected to be a singular array");
    $this->assertNewRecordHasId($jsonResponse);

    $this->assertResponseStatus(Response::HTTP_CREATED);
    return $jsonResponse;
  }

  protected function assertValidUpdateJsonResponse($response) {
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));

    $jsonResponse = $response->getData();
    $this->assertNoErrors($jsonResponse);
    $this->assertEquals(1, count($jsonResponse->messages), "A single message is expected");
    $this->assertEquals(0, count($jsonResponse->data),     "No Data is expected for an update");
    $this->assertResponseStatus(Response::HTTP_OK);
    return $jsonResponse;
  }

}
