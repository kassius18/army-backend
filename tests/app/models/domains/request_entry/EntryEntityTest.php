<?php

use app\models\domains\part\PartEntity;
use app\models\domains\request_entry\EntryEntity;
use PHPUnit\Framework\TestCase;

class EntryEntityTest extends TestCase
{
  private int $id =  1;
  private string $nameNumber =  "9S9972";
  private string $name =  "ΦΙΛΤΡΟ ΑΕΡΑ ΕΣΩΤ";
  private string $mainPart =  "Π/Θ";
  private int $amountOfOrder =  1;
  private string $unitOfOrder =  "τεμ.";
  private string $reasonOfOrder =  "04";
  private int $priorityOfOrder =  50;
  private string $observations =  "Π/Θ CAT";
  private int $consumableId = 22;
  private PartEntity $part;
  private PartEntity $secondPart;

  private EntryEntity $entryEntity;

  public function setUp(): void
  {
    $this->entryEntity = new EntryEntity(
      $this->nameNumber,
      $this->name,
      $this->mainPart,
      $this->amountOfOrder,
      $this->unitOfOrder,
      $this->reasonOfOrder,
      $this->priorityOfOrder,
      $this->observations,
      $this->consumableId,
    );

    $this->entryEntityWithIdSet = new EntryEntity(
      $this->nameNumber,
      $this->name,
      $this->mainPart,
      $this->amountOfOrder,
      $this->unitOfOrder,
      $this->reasonOfOrder,
      $this->priorityOfOrder,
      $this->observations,
      $this->consumableId,
      $this->id
    );

    $this->part = new PartEntity(
      uniqid(),
      uniqid(),
      rand(),
      uniqid(),
      uniqid(),
      rand(),
      1
    );
    $this->secondPart =  new PartEntity(
      uniqid(),
      uniqid(),
      rand(),
      uniqid(),
      uniqid(),
      rand(),
      2
    );
  }

  public function testEntityStructure()
  {
    $this->assertEquals($this->entryEntity->getNameNumber(), $this->nameNumber);
    $this->assertEquals($this->entryEntity->getName(), $this->name);
    $this->assertEquals($this->entryEntity->getMainPart(), $this->mainPart);
    $this->assertEquals($this->entryEntity->getAmountOfOrder(), $this->amountOfOrder);
    $this->assertEquals($this->entryEntity->getUnitOfOrder(), $this->unitOfOrder);
    $this->assertEquals($this->entryEntity->getReasonOfOrder(), $this->reasonOfOrder);
    $this->assertEquals($this->entryEntity->getPriorityOfOrder(), $this->priorityOfOrder);
    $this->assertEquals($this->entryEntity->getObservations(), $this->observations);
    $this->assertEquals($this->entryEntity->getConsumableId(), $this->consumableId);
    $this->assertEquals($this->entryEntityWithIdSet->getId(), $this->id);
  }

  public function testSettingParts()
  {
    $this->entryEntity->setParts([$this->part]);
    $this->assertEquals($this->entryEntity->getParts(), [$this->part]);
  }

  public function testSettingEmptyArrayResetsParts()
  {
    $this->entryEntity->addParts([$this->part]);
    $this->assertEquals($this->entryEntity->getParts(), [$this->part]);

    $this->entryEntity->setParts([]);
    $this->assertEquals($this->entryEntity->getParts(), []);
  }

  public function testAddingParts()
  {
    $this->entryEntity->addParts([$this->part]);
    $this->assertEquals($this->entryEntity->getParts(), [$this->part]);

    $this->entryEntity->addParts([$this->secondPart]);
    $this->assertEquals($this->entryEntity->getParts(), [$this->part, $this->secondPart]);
  }

  public function testEntriesAreAddedByAscendingIdNoMatterTheOrderTheyAreAddedIn()
  {
    $this->entryEntity->addParts([$this->secondPart, $this->part]);
    $this->assertEquals($this->entryEntity->getParts(), [$this->part, $this->secondPart]);
  }

  public function testSerializingToJsonWithPartsSet()
  {
    $this->entryEntity->setParts([$this->part]);

    $expected = json_encode(
      [
        "nameNumber" => $this->nameNumber,
        "name" => $this->name,
        "mainPart" => $this->mainPart,
        "amountOfOrder" => $this->amountOfOrder,
        "unitOfOrder" => $this->unitOfOrder,
        "reasonOfOrder" => $this->reasonOfOrder,
        "priorityOfOrder" => $this->priorityOfOrder,
        "observations" => $this->observations,
        "consumableId" => $this->consumableId,
        "parts" => [$this->part]
      ]
    );
    $this->assertJsonStringEqualsJsonString($expected, json_encode($this->entryEntity));
  }

  public function testSerializingToJsonWithIdSet()
  {
    $expected = json_encode(
      [
        "nameNumber" => $this->nameNumber,
        "name" => $this->name,
        "mainPart" => $this->mainPart,
        "amountOfOrder" => $this->amountOfOrder,
        "unitOfOrder" => $this->unitOfOrder,
        "reasonOfOrder" => $this->reasonOfOrder,
        "priorityOfOrder" => $this->priorityOfOrder,
        "observations" => $this->observations,
        "consumableId" => $this->consumableId,
        "parts" => [],
        "id" => $this->id
      ]
    );
    $this->assertJsonStringEqualsJsonString($expected, json_encode($this->entryEntityWithIdSet));
  }

  public function testSerializingToJsonWithIdNotSet()
  {
    $expected = json_encode(
      [
        "nameNumber" => $this->nameNumber,
        "name" => $this->name,
        "mainPart" => $this->mainPart,
        "amountOfOrder" => $this->amountOfOrder,
        "unitOfOrder" => $this->unitOfOrder,
        "reasonOfOrder" => $this->reasonOfOrder,
        "priorityOfOrder" => $this->priorityOfOrder,
        "observations" => $this->observations,
        "consumableId" => $this->consumableId,
        "parts" => [],
      ]
    );

    $this->assertJsonStringEqualsJsonString($expected, json_encode($this->entryEntity));
  }

  public function testSerializingToJsonWithNullInputs()
  {
    $expected = json_encode([
      "nameNumber" => "",
      "name" => "",
      "mainPart" => "",
      "amountOfOrder" => "",
      "unitOfOrder" => "",
      "reasonOfOrder" => "",
      "priorityOfOrder" => "",
      "observations" => "",
      "parts" => [],
      "consumableId" => "",
    ]);

    $actual = new EntryEntity(null, null, null, null, null, null, null, null, null);
    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($actual)
    );
  }

  public function testSerializingToJsonWithNullInputsButIdSet()
  {
    $expected = json_encode([
      "nameNumber" => "",
      "name" => "",
      "mainPart" => "",
      "amountOfOrder" => "",
      "unitOfOrder" => "",
      "reasonOfOrder" => "",
      "priorityOfOrder" => "",
      "observations" => "",
      "consumableId" => "",
      "parts" => [],
      "id" => 2,
    ]);

    $actual = new EntryEntity(null, null, null, null, null, null, null, null, null, 2);
    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($actual)
    );
  }
}
