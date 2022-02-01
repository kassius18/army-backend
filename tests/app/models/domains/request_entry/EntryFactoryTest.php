<?php

use app\models\domains\request_entry\EntryEntity;
use app\models\domains\request_entry\EntryFactory;
use PHPUnit\Framework\TestCase;

class EntryFactoryTest extends TestCase
{
  private static array $dbRecord;
  private static array $userInput;
  private EntryEntity $entry;
  private EntryEntity $otherEntry;
  private EntryEntity $entryWithNullValues;
  private EntryEntity $entryWithoutId;
  private EntryEntity $entryWithEmptyValues;

  public static function setUpBeforeClass(): void
  {
    self::$dbRecord = [
      [
        "name_number" => "nameNumber",
        "name" => "name",
        "main_part" => "mainPart",
        "amount_of_order" => 4,
        "unit_of_order" => "unit",
        "reason_of_order" => 5,
        "priority_of_order" => 6,
        "observations" => "obs",
        "consumable_tab_id" => 1,
        "request_row_id" => 7
      ], [
        "name_number" => "otherNameNumber",
        "name" => "otherName",
        "main_part" => "otherMainPart",
        "amount_of_order" => 5,
        "unit_of_order" => "otherUnit",
        "reason_of_order" => 6,
        "priority_of_order" => 7,
        "observations" => "otherObs",
        "consumable_tab_id" => 2,
        "request_row_id" => 8
      ], [
        "name_number" => null,
        "name" => null,
        "main_part" => null,
        "amount_of_order" => null,
        "unit_of_order" => null,
        "reason_of_order" => null,
        "priority_of_order" => null,
        "observations" => null,
        "consumable_tab_id" => null,
        "request_row_id" => 9
      ]
    ];
    self::$userInput = [
      [
        "nameNumber" => "nameNumber",
        "name" => "name",
        "mainPart" => "mainPart",
        "amountOfOrder" => 4,
        "unitOfOrder" => "unit",
        "reasonOfOrder" => 5,
        "priorityOfOrder" => 6,
        "observations" => "obs",
        "consumable" => 3,
      ], [
        "nameNumber" => "",
        "name" => "",
        "mainPart" => "",
        "amountOfOrder" => "",
        "unitOfOrder" => "",
        "reasonOfOrder" => "",
        "priorityOfOrder" => "",
        "observations" => "",
        "consumable" => "",
      ]
    ];
  }

  protected function setUp(): void
  {
    $this->entry = new EntryEntity(
      self::$dbRecord[0]["name_number"],
      self::$dbRecord[0]["name"],
      self::$dbRecord[0]["main_part"],
      self::$dbRecord[0]["amount_of_order"],
      self::$dbRecord[0]["unit_of_order"],
      self::$dbRecord[0]["reason_of_order"],
      self::$dbRecord[0]["priority_of_order"],
      self::$dbRecord[0]["observations"],
      self::$dbRecord[0]["consumable_tab_id"],
      self::$dbRecord[0]["request_row_id"]
    );

    $this->otherEntry = new EntryEntity(
      self::$dbRecord[1]["name_number"],
      self::$dbRecord[1]["name"],
      self::$dbRecord[1]["main_part"],
      self::$dbRecord[1]["amount_of_order"],
      self::$dbRecord[1]["unit_of_order"],
      self::$dbRecord[1]["reason_of_order"],
      self::$dbRecord[1]["priority_of_order"],
      self::$dbRecord[1]["observations"],
      self::$dbRecord[1]["consumable_tab_id"],
      self::$dbRecord[1]["request_row_id"]
    );

    $this->entryWithNullValues = new EntryEntity(
      self::$dbRecord[2]["name_number"],
      self::$dbRecord[2]["name"],
      self::$dbRecord[2]["main_part"],
      self::$dbRecord[2]["amount_of_order"],
      self::$dbRecord[2]["unit_of_order"],
      self::$dbRecord[2]["reason_of_order"],
      self::$dbRecord[2]["priority_of_order"],
      self::$dbRecord[2]["observations"],
      self::$dbRecord[2]["consumable_tab_id"],
      self::$dbRecord[2]["request_row_id"]
    );

    $this->entryWithoutId = new EntryEntity(
      self::$userInput[0]["nameNumber"],
      self::$userInput[0]["name"],
      self::$userInput[0]["mainPart"],
      self::$userInput[0]["amountOfOrder"],
      self::$userInput[0]["unitOfOrder"],
      self::$userInput[0]["reasonOfOrder"],
      self::$userInput[0]["priorityOfOrder"],
      self::$userInput[0]["observations"],
      self::$userInput[0]["consumable"],
    );

    $this->entryWithEmptyValues = new EntryEntity(null, null, null, null, null, null, null, null, null);
  }

  public function testCreatingEntryFromDatabaseRecord()
  {
    $entryFromRecord = EntryFactory::createEntryFromRecord(self::$dbRecord[0]);
    $this->assertJsonStringEqualsJsonString(json_encode($this->entry), json_encode($entryFromRecord));
  }

  public function testCreatingEntryFromUserInput()
  {
    $entryFromInput = EntryFactory::createEntryFromUserInput(self::$userInput[0]);
    $this->assertEquals(json_encode($this->entryWithoutId), json_encode($entryFromInput));
  }

  public function testCreatingArrayFromUserInputWithEmptyValues()
  {
    $expected = $this->entryWithEmptyValues;
    $entryFromRecordWithEmptyValues = EntryFactory::createEntryFromUserInput(self::$userInput[1]);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($entryFromRecordWithEmptyValues));
  }

  public function testCreatingManyEntriesFromUserInput()
  {
    $expected = [$this->entryWithoutId, $this->entryWithEmptyValues];
    $entriesFromUserInput = EntryFactory::createManyEntriesFromUserInput(self::$userInput);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($entriesFromUserInput));
  }

  public function testCreatingManyEntriesFromDatabaseRecord()
  {
    $expected = [$this->entry, $this->otherEntry, $this->entryWithNullValues];
    $entriesFromRecord = EntryFactory::createManyEntriesFromRecord(self::$dbRecord);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($entriesFromRecord));
  }

  public function testCreatingArrayFromRecordWithEmptyValues()
  {
    $expected = $this->entryWithNullValues;
    $entryFromRecordWithEmptyValues = EntryFactory::createEntryFromRecord(self::$dbRecord[2]);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($entryFromRecordWithEmptyValues));
  }
}
