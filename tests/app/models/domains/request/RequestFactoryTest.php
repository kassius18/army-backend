<?php

use app\models\domains\request\RequestEntity;
use app\models\domains\request\RequestFactory;
use PHPUnit\Framework\TestCase;

class RequestFactoryTest extends TestCase
{
  private static array $dbRecord;
  private static array $userInput;
  private RequestEntity $request;
  private RequestEntity $otherRequest;
  private RequestEntity $requestWithNullValues;
  private RequestEntity $requestWithoutId;
  private RequestEntity $requestWithEmptyValues;


  public static function setUpBeforeClass(): void
  {
    self::$dbRecord = [
      [
        "phi_first_part" => 1,
        "phi_second_part" => 2,
        "year" => 3,
        "month" => 4,
        "day" => 5,
        "request_id" => 1,
      ], [
        "phi_first_part" => 6,
        "phi_second_part" => 7,
        "year" => 8,
        "month" => 9,
        "day" => 10,
        "request_id" => 2,
      ], [
        "phi_first_part" => null,
        "phi_second_part" => null,
        "year" => null,
        "month" => null,
        "day" => null,
        "request_id" => 3,
      ]
    ];

    self::$userInput = [
      [
        "firstPartOfPhi" => 1,
        "secondPartOfPhi" => 2,
        "year" => 3,
        "month" => 4,
        "day" => 5,
      ], [
        "firstPartOfPhi" => "",
        "secondPartOfPhi" => "",
        "year" => "",
        "month" => "",
        "day" => "",
      ]
    ];
  }

  protected function setUp(): void
  {
    $this->request = new RequestEntity(
      self::$dbRecord[0]["phi_first_part"],
      self::$dbRecord[0]["phi_second_part"],
      self::$dbRecord[0]["year"],
      self::$dbRecord[0]["month"],
      self::$dbRecord[0]["day"],
      self::$dbRecord[0]["request_id"]
    );

    $this->otherRequest = new RequestEntity(
      self::$dbRecord[1]["phi_first_part"],
      self::$dbRecord[1]["phi_second_part"],
      self::$dbRecord[1]["year"],
      self::$dbRecord[1]["month"],
      self::$dbRecord[1]["day"],
      self::$dbRecord[1]["request_id"]
    );

    $this->requestWithNullValues = new RequestEntity(
      self::$dbRecord[2]["phi_first_part"],
      self::$dbRecord[2]["phi_second_part"],
      self::$dbRecord[2]["year"],
      self::$dbRecord[2]["month"],
      self::$dbRecord[2]["day"],
      self::$dbRecord[2]["request_id"]
    );

    $this->requestWithoutId = new RequestEntity(
      self::$userInput[0]["firstPartOfPhi"],
      self::$userInput[0]["secondPartOfPhi"],
      self::$userInput[0]["year"],
      self::$userInput[0]["month"],
      self::$userInput[0]["day"],
    );

    $this->requestWithEmptyValues = new RequestEntity(null, null, null, null, null);
  }

  public function testCreatingRequestFromJOIN()
  {
    [$expected, $recordsFromJOIN] = include(TEST_DIR . "/fixtures/RequestFactoryFixture.php");
    $actual = RequestFactory::createRequestsFromJOINRecord($recordsFromJOIN);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testCreatingRequestFromDatabaseRecord()
  {
    $requestCreateFromFactory = RequestFactory::createRequestFromRecord(self::$dbRecord[0]);
    $this->assertEquals($this->request, $requestCreateFromFactory);
  }

  public function testCreatingRequestFromUserInput()
  {
    $requestFromInput = RequestFactory::createRequestFromUserInput(self::$userInput[0]);
    $this->assertEquals(json_encode($this->requestWithoutId), json_encode($requestFromInput));
  }

  public function testCreatingArrayFromUserInputWithEmptyValues()
  {
    $expected = $this->requestWithEmptyValues;
    $requestFromRecordWithEmptyValues = RequestFactory::createRequestFromUserInput(self::$userInput[1]);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($requestFromRecordWithEmptyValues));
  }

  public function testCreatingManyRequestsFromUserInput()
  {
    $expected = [$this->requestWithoutId, $this->requestWithEmptyValues];
    $requestsFromUserInput = RequestFactory::createManyRequestsFromUserInput(self::$userInput);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($requestsFromUserInput));
  }

  public function testCreatingManyRequestsFromDatabaseRecord()
  {
    $expected = [$this->request, $this->otherRequest, $this->requestWithNullValues];
    $requestsFromRecord = RequestFactory::createManyRequestsFromRecord(self::$dbRecord);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($requestsFromRecord));
  }

  public function testCreatingArrayFromRecordWithEmptyValues()
  {
    $expected = $this->requestWithNullValues;
    $requestFromRecordWithEmptyValues = RequestFactory::createRequestFromRecord(self::$dbRecord[2]);
    $this->assertJsonStringEqualsJsonString(
      json_encode($expected),
      json_encode($requestFromRecordWithEmptyValues)
    );
  }
}
