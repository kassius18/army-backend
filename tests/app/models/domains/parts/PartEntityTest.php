<?php

use app\models\domains\part\PartEntity;
use PHPUnit\Framework\TestCase;

class PartEntityTest extends TestCase
{
  private PartEntity $part;
  private PartEntity $partWithId;

  private string $dateRecieved = "someDate";
  private string $pieNumber = "someNumber";
  private int $amountRecieved = 3;
  private string $tabUsed = "someTab";
  private string $dateUsed = "someDate";
  private int $amountUsed = 1;
  private int $id = 1;


  public function setUp(): void
  {
    $this->part = new PartEntity(
      $this->dateRecieved,
      $this->pieNumber,
      $this->amountRecieved,
      $this->tabUsed,
      $this->dateUsed,
      $this->amountUsed
    );

    $this->partWithId = new PartEntity(
      $this->dateRecieved,
      $this->pieNumber,
      $this->amountRecieved,
      $this->tabUsed,
      $this->dateUsed,
      $this->amountUsed,
      $this->id
    );
  }

  public function testEntityStructure()
  {
    $this->assertEquals($this->dateRecieved, $this->part->getDateRecieved());
    $this->assertEquals($this->pieNumber, $this->part->getPieNumber());
    $this->assertEquals($this->amountRecieved, $this->part->getAmountRecieved());
    $this->assertEquals($this->tabUsed, $this->part->getTabUsed());
    $this->assertEquals($this->dateUsed, $this->part->getDateUsed());
    $this->assertEquals($this->amountUsed, $this->part->getAmountUsed());
    $this->assertEquals(null, $this->part->getId());
    $this->assertEquals($this->id, $this->partWithId->getId());
  }

  public function testSerializingToJsonWithIdSet()
  {
    $expected = json_encode([
      "dateRecieved" => $this->dateRecieved,
      "pieNumber" => $this->pieNumber,
      "amountRecieved" => $this->amountRecieved,
      "tabUsed" => $this->tabUsed,
      "dateUsed" => $this->dateUsed,
      "amountUsed" => $this->amountUsed,
      "id" => $this->id
    ]);


    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($this->partWithId)
    );
  }

  public function testSerializingToJsonWithIdNotSet()
  {
    $expected = json_encode([
      "dateRecieved" => $this->dateRecieved,
      "pieNumber" => $this->pieNumber,
      "amountRecieved" => $this->amountRecieved,
      "tabUsed" => $this->tabUsed,
      "dateUsed" => $this->dateUsed,
      "amountUsed" => $this->amountUsed,
    ]);


    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($this->part)
    );
  }

  public function testSerializingToJsonWithNullInputs()
  {
    $expected = json_encode([
      "dateRecieved" => "",
      "pieNumber" => "",
      "amountRecieved" => "",
      "tabUsed" => "",
      "dateUsed" => "",
      "amountUsed" => "",
    ]);

    $actual = new PartEntity(null, null, null, null, null, null);
    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($actual)
    );
  }

  public function testSerializingToJsonWithNullInputsButIdSet()
  {
    $expected = json_encode([
      "dateRecieved" => "",
      "pieNumber" => "",
      "amountRecieved" => "",
      "tabUsed" => "",
      "dateUsed" => "",
      "amountUsed" => "",
      "id" => 3,
    ]);

    $actual = new PartEntity(null, null, null, null, null, null, 3);
    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($actual)
    );
  }
}
