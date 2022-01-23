<?php

use app\models\domains\tab\TabEntity;
use PHPUnit\Framework\TestCase;

class TabEntityTest extends TestCase
{
  private TabEntity $tabEntity;
  private string $name = "someName";
  private string $usage = "someUsage";
  private string $observations = "obs";
  private int $id = 3;

  public function setUp(): void
  {
    $this->tabEntity = new TabEntity(
      $this->name,
      $this->usage,
      $this->observations,
      $this->id
    );
  }

  public function testEntityStructure()
  {
    $this->assertEquals($this->name, $this->tabEntity->getName());
    $this->assertEquals($this->usage, $this->tabEntity->getUsage());
    $this->assertEquals($this->observations, $this->tabEntity->getObservations());
    $this->assertEquals($this->id, $this->tabEntity->getId());
  }

  public function testSerializingToJson()
  {
    $expected = json_encode([
      "id" => $this->id,
      "name" => $this->name,
      "usage" => $this->usage,
      "observations" => $this->observations
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
      "id" => 3,
    ]);

    $actual = new TabEntity(null, null, null, 3);
    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($actual)
    );
  }
}
