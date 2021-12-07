<?php

use app\models\domains\request\RequestEntity;
use app\models\domains\request\RequestFactory;
use PHPUnit\Framework\TestCase;

class RequestFactoryTest extends TestCase
{
  private array $dbRecord;
  private array $userPostInput;
  private RequestEntity $request;

  protected function setUp(): void
  {
    $this->dbRecord = [
      'phi_first_part' => 1,
      'phi_second_part' => 2,
      'year' => 3,
      'month' => 4,
      'day' => 5,
    ];
    $this->userPostInput = [
      'firstPartOfPhi' => 1,
      'secondPartOfPhi' => 2,
      'year' => 3,
      'month' => 4,
      'day' => 5,
    ];

    $this->request = new RequestEntity(1, 2, 3, 4, 5);
  }

  public function testCreatingRequestFromDatabaseRecord()
  {
    $requestCreateFromFactory = RequestFactory::createRequestEntityFromRecord($this->dbRecord);
    $this->assertEquals($this->request, $requestCreateFromFactory);
  }
  public function testCreatingRequestFromUserInput()
  {
    $requestCreateFromFactory = RequestFactory::createRequestFromUserInput($this->userPostInput);
    $this->assertEquals($this->request, $requestCreateFromFactory);
  }
}
