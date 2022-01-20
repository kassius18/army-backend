<?php

use app\models\domains\part\PartEntity;
use app\models\domains\part\PartFactory;
use PHPUnit\Framework\TestCase;

class PartFactoryTest extends TestCase
{
  private static array $dbRecord;
  private static array $userInput;

  private PartEntity $part;
  private PartEntity $secondPart;
  private PartEntity $partWithoutId;
  private PartEntity $partWithNullValues;
  private PartEntity $partWithEmptyValues;

  public static function setUpBeforeClass(): void
  {
    self::$dbRecord = [
      [
        "date_recieved" => "someDate",
        "pie_number" => "someNumber",
        "amount_recieved" => 2,
        "tab_used" => "someTab",
        "date_used" => "someDate",
        "amount_used" => 1,
        "id" => 1
      ], [
        "date_recieved" => "someOtherDate",
        "pie_number" => "someOtherNumber",
        "amount_recieved" => 3,
        "tab_used" => "someOtherTab",
        "date_used" => "someOtherDate",
        "amount_used" => 2,
        "id" => 2
      ], [
        "date_recieved" => null,
        "pie_number" => null,
        "amount_recieved" => null,
        "tab_used" => null,
        "date_used" => null,
        "amount_used" => null,
        "id" => 3

      ]
    ];

    self::$userInput = [[
      "dateRecieved" => "someDate",
      "pieNumber" => "someNumber",
      "amountRecieved" => 2,
      "tabUsed" => "someTab",
      "dateUsed" => "someDate",
      "amountUsed" => 1,
    ], [
      "dateRecieved" => "",
      "pieNumber" => "",
      "amountRecieved" => "",
      "tabUsed" => "",
      "dateUsed" => "",
      "amountUsed" => "",
    ]];
  }

  protected function setUp(): void
  {
    $this->part = new PartEntity(
      self::$dbRecord[0]["date_recieved"],
      self::$dbRecord[0]["pie_number"],
      self::$dbRecord[0]["amount_recieved"],
      self::$dbRecord[0]["tab_used"],
      self::$dbRecord[0]["date_used"],
      self::$dbRecord[0]["amount_used"],
      self::$dbRecord[0]["id"],
    );

    $this->secondPart = new PartEntity(
      self::$dbRecord[1]["date_recieved"],
      self::$dbRecord[1]["pie_number"],
      self::$dbRecord[1]["amount_recieved"],
      self::$dbRecord[1]["tab_used"],
      self::$dbRecord[1]["date_used"],
      self::$dbRecord[1]["amount_used"],
      self::$dbRecord[1]["id"],
    );

    $this->partWithNullValues = new PartEntity(
      self::$dbRecord[2]["date_recieved"],
      self::$dbRecord[2]["pie_number"],
      self::$dbRecord[2]["amount_recieved"],
      self::$dbRecord[2]["tab_used"],
      self::$dbRecord[2]["date_used"],
      self::$dbRecord[2]["amount_used"],
      self::$dbRecord[2]["id"],
    );

    $this->partWithoutId = new PartEntity(
      self::$userInput[0]["dateRecieved"],
      self::$userInput[0]["pieNumber"],
      self::$userInput[0]["amountRecieved"],
      self::$userInput[0]["tabUsed"],
      self::$userInput[0]["dateUsed"],
      self::$userInput[0]["amountUsed"],
    );

    $this->partWithEmptyValues = new PartEntity(
      self::$userInput[1]["dateRecieved"] ?: null,
      self::$userInput[1]["pieNumber"] ?: null,
      self::$userInput[1]["amountRecieved"] ?: null,
      self::$userInput[1]["tabUsed"] ?: null,
      self::$userInput[1]["dateUsed"] ?: null,
      self::$userInput[1]["amountUsed"] ?: null,
    );
  }


  public function testCreatingPartFromUserInput()
  {
    $partFromInput = PartFactory::createPartFromUserInput(self::$userInput[0]);
    $this->assertEquals(json_encode($this->partWithoutId), json_encode($partFromInput));
  }

  public function testCreatingManyPartsFromUserInput()
  {
    $expected = [$this->partWithoutId, $this->partWithEmptyValues];
    $manyPartsFromUserInput = PartFactory::createManyPartsFromUserInput(self::$userInput);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($manyPartsFromUserInput));
  }

  public function testCreatingPartFromDatabaseRecord()
  {
    $partFromRecord = PartFactory::createPartFromRecord(self::$dbRecord[0]);
    $this->assertJsonStringEqualsJsonString(json_encode($this->part), json_encode($partFromRecord));
  }

  public function testCreatingManyVehiclesFromDatabaseRecord()
  {
    $expected = [$this->part, $this->secondPart, $this->partWithNullValues];
    $manyPartsFromRecord = PartFactory::createManyPartsFromRecord(self::$dbRecord);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($manyPartsFromRecord));
  }

  public function testCreatingArrayFromRecordWithEmptyValues()
  {
    $expected = new PartEntity(null, null, null, null, null, null, 3);
    $partFromRecordWithEmptyValues = PartFactory::createPartFromRecord(self::$dbRecord[2]);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($partFromRecordWithEmptyValues));
  }

  public function testCreatingArrayFromUserInputWithEmptyValues()
  {
    $expected = $this->partWithEmptyValues;
    $partFromRecordWithEmptyValues = PartFactory::createPartFromUserInput(self::$userInput[1]);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($partFromRecordWithEmptyValues));
  }
}
