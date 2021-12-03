<?php

use app\models\domains\request\RequestEntity;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class RequestEntityTest extends TestCase
{
  private int $firstPartOfPhi;
  private int $secondPartOfPhi;
  private int $year;
  private int $month;
  private int $day;
  private RequestEntity $requestEntity;

  public function setUp(): void
  {
    $this->firstPartOfPhi = 15;
    $this->secondPartOfPhi = 2000;
    $this->year = 2001;
    $this->month = 05;
    $this->day = 15;
    $this->requestEntity = new RequestEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->year,
      $this->month,
      $this->day,
    );
  }

  public function testEntityStructure()
  {
    $this->assertEquals($this->requestEntity->getFirstPhi(), $this->firstPartOfPhi);
    $this->assertEquals($this->requestEntity->getSecondPhi(), $this->secondPartOfPhi);
    $this->assertEquals($this->requestEntity->getYear(), $this->year);
    $this->assertEquals($this->requestEntity->getMonth(), $this->month);
    $this->assertEquals($this->requestEntity->getDay(), $this->day);
  }

  public function testSerializingToJson()
  {
    $expected = json_encode(
      [
        'firstPartOfPhi' => $this->firstPartOfPhi,
        'secondPartOfPhi' => $this->secondPartOfPhi,
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
      ]
    );

    $this->assertJsonStringEqualsJsonString($expected, json_encode($this->requestEntity));
  }
}
