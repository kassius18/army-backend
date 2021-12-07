<?php

use app\models\domains\request\RequestEntity;
use app\models\domains\request_entry\EntryEntity;
use app\models\domains\request_entry\EntryFactory;
use Dotenv\Parser\Entry;
use PHPUnit\Framework\TestCase;

class EntryFactoryTest extends TestCase
{
  private array $dbRecord;
  private array $userPostInput;
  private EntryEntity $entry;

  protected function setUp(): void
  {
    $this->dbRecord =
      [
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
      ];
    $this->userPostInput = [
      'firstPartOfPhi' => 1,
      'secondPartOfPhi' => 2,
      'year' => 3,
      'nameNumber' => 'nameNumber',
      'name' => 'name',
      'mainPart' => 'mainPart',
      'amountOfOrder' => 4,
      'unitOfOrder' => "unit",
      'reasonOfOrder' => 5,
      'priorityOfOrder' => 6,
      'observations' => 'obs',
      'id' => 7
    ];

    $this->entry = new EntryEntity(
      1,
      2,
      3,
      'nameNumber',
      'name',
      'mainPart',
      4,
      "unit",
      5,
      6,
      'obs',
      7
    );
  }

  public function testCreatingRequestFromDatabaseRecord()
  {
    $entryCreatedFromDb = EntryFactory::createEntryFromRecord($this->dbRecord);
    $this->assertEquals($this->entry, $entryCreatedFromDb);
  }
  public function testCreatingRequestFromUserInput()
  {
    $entryCreatedFromUserInput = EntryFactory::createEntryFromUserInput($this->userPostInput);
    $this->assertEquals($this->entry, $entryCreatedFromUserInput);
  }
}
