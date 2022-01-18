<?php

use app\models\domains\request_entry\EntryEntity;
use PHPUnit\Framework\TestCase;

class EntryEntityTest extends TestCase
{
  private int $firstPartOfPhi;
  private int $year;
  private int $id;
  private string $nameNumber;
  private string $name;
  private string $mainPart;
  private int $amountOfOrder;
  private string $unitOfOrder;
  private int $reasonOfOrder;
  private int $priorityOfOrder;
  private string $observations;

  private EntryEntity $entryEntity;

  public function setUp(): void
  {
    $this->firstPartOfPhi = 15;
    $this->year = 2001;
    $this->id = 1;
    $this->nameNumber = '9S9972';
    $this->name = 'ΦΙΛΤΡΟ ΑΕΡΑ ΕΣΩΤ';
    $this->mainPart = 'Π/Θ';
    $this->amountOfOrder = 1;
    $this->unitOfOrder =     'τεμ.';
    $this->reasonOfOrder = 4;
    $this->priorityOfOrder = 50;
    $this->observations = 'Π/Θ CAT';

    $this->entryEntity = new EntryEntity(
      $this->firstPartOfPhi,
      $this->year,
      $this->nameNumber,
      $this->name,
      $this->mainPart,
      $this->amountOfOrder,
      $this->unitOfOrder,
      $this->reasonOfOrder,
      $this->priorityOfOrder,
      $this->observations,
    );
    $this->entryEntityWithIdSet = new EntryEntity(
      $this->firstPartOfPhi,
      $this->year,
      $this->nameNumber,
      $this->name,
      $this->mainPart,
      $this->amountOfOrder,
      $this->unitOfOrder,
      $this->reasonOfOrder,
      $this->priorityOfOrder,
      $this->observations,
      $this->id
    );
  }

  public function testEntityStructure()
  {
    $this->assertEquals($this->entryEntity->getFirstPhi(), $this->firstPartOfPhi);
    $this->assertEquals($this->entryEntity->getYear(), $this->year);
    $this->assertEquals($this->entryEntity->getNameNumber(), $this->nameNumber);
    $this->assertEquals($this->entryEntity->getName(), $this->name);
    $this->assertEquals($this->entryEntity->getMainPart(), $this->mainPart);
    $this->assertEquals($this->entryEntity->getAmountOfOrder(), $this->amountOfOrder);
    $this->assertEquals($this->entryEntity->getUnitOfOrder(), $this->unitOfOrder);
    $this->assertEquals($this->entryEntity->getReasonOfOrder(), $this->reasonOfOrder);
    $this->assertEquals($this->entryEntity->getPriorityOfOrder(), $this->priorityOfOrder);
    $this->assertEquals($this->entryEntity->getObservations(), $this->observations);
    $this->assertEquals($this->entryEntityWithIdSet->getId(), $this->id);
  }

  public function testSerializingToJsonWithIdSet()
  {
    $expected = json_encode(
      [
        'firstPartOfPhi' => $this->firstPartOfPhi,
        'year' => $this->year,
        'nameNumber' => $this->nameNumber,
        'name' => $this->name,
        'mainPart' => $this->mainPart,
        'amountOfOrder' => $this->amountOfOrder,
        'unitOfOrder' => $this->unitOfOrder,
        'reasonOfOrder' => $this->reasonOfOrder,
        'priorityOfOrder' => $this->priorityOfOrder,
        'observations' => $this->observations,
        'id' => $this->id
      ]
    );
    $this->assertJsonStringEqualsJsonString($expected, json_encode($this->entryEntityWithIdSet));
  }

  public function testSerializingToJsonWithIdNotSet()
  {
    $expected = json_encode(
      [
        'firstPartOfPhi' => $this->firstPartOfPhi,
        'year' => $this->year,
        'nameNumber' => $this->nameNumber,
        'name' => $this->name,
        'mainPart' => $this->mainPart,
        'amountOfOrder' => $this->amountOfOrder,
        'unitOfOrder' => $this->unitOfOrder,
        'reasonOfOrder' => $this->reasonOfOrder,
        'priorityOfOrder' => $this->priorityOfOrder,
        'observations' => $this->observations,
      ]
    );

    $this->assertJsonStringEqualsJsonString($expected, json_encode($this->entryEntity));
  }
}
