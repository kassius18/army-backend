<?php

use app\models\domains\tab\TabEntity;
use app\models\domains\tab\TabFactory;
use PHPUnit\Framework\TestCase;

class TabFactoryTest extends TestCase
{

  private static array $dbRecord;
  private static array $userInput;

  private TabEntity $tab;
  private TabEntity $secondTab;
  private TabEntity $tabFromUserInput;
  private TabEntity $tabWithNullValues;
  private TabEntity $tabWithEmptyValues;

  public static function setUpBeforeClass(): void
  {
    self::$dbRecord = [
      [
        "id" => 1,
        "name" => "name1",
        "usage" => "tab1",
        "observations" => ""
      ], [
        "id" => 2,
        "name" => "name2",
        "usage" => "tab2",
        "observations" => "obs2"
      ], [
        "id" => 3,
        "name" => null,
        "usage" => null,
        "observations" => null
      ]
    ];

    self::$userInput = [
      [
        "name" => "name3",
        "usage" => "tab3",
        "observations" => "obs3",
        "id" => "4"
      ], [
        "id" => 5,
        "name" => "",
        "usage" => "",
        "observations" => ""
      ]
    ];
  }

  protected function setUp(): void
  {
    $this->tab = new TabEntity(
      self::$dbRecord[0]["name"],
      self::$dbRecord[0]["usage"],
      self::$dbRecord[0]["observations"],
      self::$dbRecord[0]["id"],
    );

    $this->secondTab = new TabEntity(
      self::$dbRecord[1]["name"],
      self::$dbRecord[1]["usage"],
      self::$dbRecord[1]["observations"],
      self::$dbRecord[1]["id"],
    );

    $this->tabWithNullValues = new TabEntity(
      self::$dbRecord[2]["name"],
      self::$dbRecord[2]["usage"],
      self::$dbRecord[2]["observations"],
      self::$dbRecord[2]["id"],
    );

    $this->tabFromUserInput = new TabEntity(
      self::$userInput[0]["name"],
      self::$userInput[0]["usage"],
      self::$userInput[0]["observations"],
      self::$userInput[0]["id"],
    );

    $this->tabWithEmptyValues = new TabEntity(
      self::$userInput[1]["name"],
      self::$userInput[1]["usage"],
      self::$userInput[1]["observations"],
      self::$userInput[1]["id"],
    );
  }

  public function testCreatingTabFromDatabaseRecord()
  {
    $tabFromRecord = TabFactory::createTabFromRecord(self::$dbRecord[0]);
    $this->assertJsonStringEqualsJsonString(json_encode($this->tab), json_encode($tabFromRecord));
  }

  public function testCreatingTabFromUserInput()
  {
    $tabFromUserInput = TabFactory::createTabFromInput(self::$userInput[0]);
    $this->assertJsonStringEqualsJsonString(
      json_encode($this->tabFromUserInput),
      json_encode($tabFromUserInput)
    );
  }

  public function testCreatingManyTabsFromDatabaseRecord()
  {
    $expected = [$this->tab, $this->secondTab, $this->tabWithNullValues];
    $tabsFromRecord = TabFactory::createManyTabsFromRecord(self::$dbRecord);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($tabsFromRecord));
  }

  public function testCreatingArrayFromRecordWithEmptyValues()
  {
    $expected = $this->tabWithNullValues;
    $tabFromRecordWithEmptyValues = TabFactory::createTabFromRecord(self::$dbRecord[2]);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($tabFromRecordWithEmptyValues));
  }

  public function testCreatingArrayFromUserInputWithEmptyValues()
  {
    $expected = $this->tabWithEmptyValues;
    $tabFromRecordWithEmptyValues = TabFactory::createTabFromInput(self::$userInput[1]);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($tabFromRecordWithEmptyValues));
  }
}
