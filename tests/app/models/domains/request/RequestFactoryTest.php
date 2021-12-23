<?php

use app\models\domains\request\RequestEntity;
use app\models\domains\request\RequestFactory;
use app\models\domains\request_entry\EntryEntity;
use fixtures\RequestFactoryFixture;
use PHPUnit\Framework\TestCase;

class RequestFactoryTest extends TestCase
{
  private static array $dbRecord;
  private static array $userPostInput;
  private static RequestFactoryFixture $fixture;
  private RequestEntity $request;
  private RequestEntity $requestFromUserPost;

  public static function setUpBeforeClass(): void
  {
    self::$fixture = new RequestFactoryFixture();
    self::$dbRecord = [[
      'phi_first_part' => 1,
      'phi_second_part' => 2,
      'year' => 3,
      'month' => 4,
      'day' => 5,
      'request_phi_first_part' => 1,
      'request_phi_second_part' => 2,
      'request_year' => 3,
      'name_number' => 'nameNumber',
      'name' => 'name',
      'main_part' => 'mainPart',
      'amount_of_order' => 4,
      'unit_of_order' => "unit",
      'reason_of_order' => 5,
      'priority_of_order' => 6,
      'observations' => 'obs',
      'id' => 7
    ]];

    self::$userPostInput = [
      'firstPartOfPhi' => 1,
      'secondPartOfPhi' => 2,
      'year' => 3,
      'month' => 4,
      'day' => 5,
      'entries' => [[
        'nameNumber' => 'nameNumber',
        'name' => 'name',
        'mainPart' => 'mainPart',
        'amountOfOrder' => 4,
        'unitOfOrder' => "unit",
        'reasonOfOrder' => 5,
        'priorityOfOrder' => 6,
        'observations' => 'obs'
      ]]
    ];
  }

  protected function setUp(): void
  {
    $entry = new EntryEntity(
      self::$dbRecord[0]['request_phi_first_part'],
      self::$dbRecord[0]['request_phi_second_part'],
      self::$dbRecord[0]['request_year'],
      self::$dbRecord[0]['name_number'],
      self::$dbRecord[0]['name'],
      self::$dbRecord[0]['main_part'],
      self::$dbRecord[0]['amount_of_order'],
      self::$dbRecord[0]['unit_of_order'],
      self::$dbRecord[0]['reason_of_order'],
      self::$dbRecord[0]['priority_of_order'],
      self::$dbRecord[0]['observations'],
      self::$dbRecord[0]['id']
    );

    $entryWithoutId = new EntryEntity(
      self::$dbRecord[0]['request_phi_first_part'],
      self::$dbRecord[0]['request_phi_second_part'],
      self::$dbRecord[0]['request_year'],
      self::$dbRecord[0]['name_number'],
      self::$dbRecord[0]['name'],
      self::$dbRecord[0]['main_part'],
      self::$dbRecord[0]['amount_of_order'],
      self::$dbRecord[0]['unit_of_order'],
      self::$dbRecord[0]['reason_of_order'],
      self::$dbRecord[0]['priority_of_order'],
      self::$dbRecord[0]['observations'],
    );


    $this->request = new RequestEntity(
      self::$dbRecord[0]['phi_first_part'],
      self::$dbRecord[0]['phi_second_part'],
      self::$dbRecord[0]['year'],
      self::$dbRecord[0]['month'],
      self::$dbRecord[0]['day'],
      [$entry]
    );
    $this->requestFromUserPost = new RequestEntity(
      self::$dbRecord[0]['phi_first_part'],
      self::$dbRecord[0]['phi_second_part'],
      self::$dbRecord[0]['year'],
      self::$dbRecord[0]['month'],
      self::$dbRecord[0]['day'],
      [$entryWithoutId]
    );
  }

  public function testCreatingRequestFromDatabaseRecord()
  {
    $requestCreateFromFactory = RequestFactory::createRequestEntityFromRecord(self::$dbRecord);
    $this->assertEquals($this->request, $requestCreateFromFactory);
  }

  public function testCreatingRequestFromUserInput()
  {
    $requestCreateFromFactory = RequestFactory::createRequestFromUserInput(self::$userPostInput);
    $this->assertEquals($this->requestFromUserPost, $requestCreateFromFactory);
  }

  public function testEntriesWithDifferentPrimaryKeyGetIgnored()
  {
    $requestEntity = self::$fixture->testEntriesBelongToRequestsFixture();
    $arrayOfEntriesOfRequest = $requestEntity->getEntries();
    foreach ($arrayOfEntriesOfRequest as $entry) {
      $this->assertEquals($requestEntity->getFirstPhi(), $entry->getFirstPhi());
      $this->assertEquals($requestEntity->getSecondPhi(), $entry->getSecondPhi());
      $this->assertEquals($requestEntity->getYear(), $entry->getYear());
    }
  }
}
