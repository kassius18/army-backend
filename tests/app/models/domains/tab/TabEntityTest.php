<?php

use app\models\domains\part\PartEntity;
use app\models\domains\tab\TabEntity;
use PHPUnit\Framework\TestCase;

class TabEntityTest extends TestCase
{
  private TabEntity $tabEntity;
  private PartEntity $part;
  private PartEntity $secondPart;
  private string $name = "someName";
  private string $usage = "someUsage";
  private string $observations = "obs";
  private int $startingTotal = 0;
  private int $id = 3;

  public function setUp(): void
  {
    $this->tabEntity = new TabEntity(
      $this->name,
      $this->usage,
      $this->observations,
      $this->startingTotal,
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
    $this->secondPart = new PartEntity(
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
    $this->assertEquals($this->name, $this->tabEntity->getName());
    $this->assertEquals($this->usage, $this->tabEntity->getUsage());
    $this->assertEquals($this->observations, $this->tabEntity->getObservations());
    $this->assertEquals($this->startingTotal, $this->tabEntity->getStartingTotal());
    $this->assertEquals($this->id, $this->tabEntity->getId());
  }

  public function testAddingParts()
  {
    $this->tabEntity->addParts([$this->part]);
    $this->assertEquals($this->tabEntity->getParts(), [$this->part]);

    $this->tabEntity->addParts([$this->secondPart]);
    $this->assertEquals($this->tabEntity->getParts(), [$this->part, $this->secondPart]);
  }

  public function testSettingParts()
  {
    $this->tabEntity->setParts([$this->part]);
    $this->assertEquals($this->tabEntity->getParts(), [$this->part]);
  }

  public function testSettingEmptyArrayResetsParts()
  {
    $this->tabEntity->addParts([$this->part]);
    $this->assertEquals($this->tabEntity->getParts(), [$this->part]);

    $this->tabEntity->setParts([]);
    $this->assertEquals($this->tabEntity->getParts(), []);
  }

  public function testSerializingToJson()
  {
    $expected = json_encode([
      "id" => $this->id,
      "name" => $this->name,
      "usage" => $this->usage,
      "startingTotal" => $this->startingTotal,
      "observations" => $this->observations,
      "parts" => [],
    ]);

    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($this->tabEntity)
    );
  }

  public function testSerializingToJsonWithNullInputs()
  {
    $expected = json_encode([
      "name" => "",
      "usage" => "",
      "observations" => "",
      "startingTotal" => 0,
      "id" => 3,
      "parts" => [],
    ]);

    $actual = new TabEntity(null, null, null, null, 3);
    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($actual)
    );
  }

  public function testSerializingToJsonWithPartsSet()
  {
    $this->tabEntity->setParts([$this->part]);
    $expected = json_encode([
      "name" => $this->tabEntity->getName(),
      "usage" => $this->tabEntity->getUsage(),
      "observations" => $this->tabEntity->getObservations(),
      "startingTotal" => $this->tabEntity->getStartingTotal(),
      "id" => $this->tabEntity->getId(),
      "parts" => [$this->part],
    ]);
    $this->assertJsonStringEqualsJsonString($expected, json_encode($this->tabEntity));
  }
}
