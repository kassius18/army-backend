<?php

use app\models\domains\request\RequestEntity;
use PHPUnit\Framework\TestCase;

class RequestEntityTest extends TestCase
{
  private int $firstPartOfPhi = 15;
  private int $secondPartOfPhi = 2000;
  private int $year = 2001;
  private int $month = 05;
  private int $day = 15;
  private int $id = 1;
  private RequestEntity $requestEntity;
  private RequestEntity $requestEntityWithIdSet;

  public function setUp(): void
  {
    $this->requestEntity = new RequestEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->year,
      $this->month,
      $this->day
    );

    $this->requestEntityWithIdSet = new RequestEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->year,
      $this->month,
      $this->day,
      $this->id
    );
  }

  public function testEntityStructure()
  {
    $this->assertEquals($this->requestEntity->getFirstPhi(), $this->firstPartOfPhi);
    $this->assertEquals($this->requestEntity->getSecondPhi(), $this->secondPartOfPhi);
    $this->assertEquals($this->requestEntity->getYear(), $this->year);
    $this->assertEquals($this->requestEntity->getMonth(), $this->month);
    $this->assertEquals($this->requestEntity->getDay(), $this->day);
    $this->assertEquals($this->requestEntityWithIdSet->getId(), $this->id);
  }

  public function testSerializingToJsonWithIdSet()
  {
    $expected = json_encode(
      [
        "firstPartOfPhi" => $this->firstPartOfPhi,
        "secondPartOfPhi" => $this->secondPartOfPhi,
        "year" => $this->year,
        "month" => $this->month,
        "day" => $this->day,
        "id" => $this->id
      ]
    );

    $this->assertJsonStringEqualsJsonString($expected, json_encode($this->requestEntityWithIdSet));
  }

  public function testSerializingToJsonWithIdNotSet()
  {
    $expected = json_encode(
      [
        "firstPartOfPhi" => $this->firstPartOfPhi,
        "secondPartOfPhi" => $this->secondPartOfPhi,
        "year" => $this->year,
        "month" => $this->month,
        "day" => $this->day
      ]
    );

    $this->assertJsonStringEqualsJsonString($expected, json_encode($this->requestEntity));
  }

  public function testSerializingToJsonWithNullInputs()
  {
    $expected = json_encode([
      "firstPartOfPhi" => "",
      "secondPartOfPhi" => "",
      "year" => "",
      "month" => "",
      "day" => ""
    ]);

    $actual = new RequestEntity(null, null, null, null, null);
    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($actual)
    );
  }

  public function testSerializingToJsonWithNullInputsButIdSet()
  {
    $expected = json_encode([
      "firstPartOfPhi" => "",
      "secondPartOfPhi" => "",
      "year" => "",
      "month" => "",
      "day" => "",
      "id" => $this->id
    ]);

    $actual = new RequestEntity(null, null, null, null, null, $this->id);
    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($actual)
    );
  }
}
