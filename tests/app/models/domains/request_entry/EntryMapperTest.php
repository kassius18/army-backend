<?php

use app\models\domains\request_entry\EntryEntity;
use app\models\domains\request_entry\EntryMapper;
use app\models\domains\request_entry\EntryFactory;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class EntryMapperTest extends TestCase
{
  private static PhinxApplication $phinxApp;
  private static $pdo;
  private EntryMapper $entryMapper;

  private int  $firstPartOfPhi = 15;
  private int  $secondPartOfPhi = 2000;
  private int  $year = 2021;
  private int  $id = 1;
  private string $nameNumber = '9S9972';
  private string $name = 'ΦΙΛΤΡΟ ΑΕΡΑ ΕΣΩΤ';
  private string $mainPart = 'Π/Θ';
  private int $amountOfOrder = 1;
  private string $unitOfOrder = 'τεμ.';
  private int $reasonOfOrder = 04;
  private int $priorityOfOrder = 50;
  private string $observations = 'Π/Θ CAT';

  private string $differentName = 'ΦΙΛΤΡΟ';

  private EntryEntity $entryWithoutId;
  private EntryEntity $entryWithoutIdWithDifferentName;
  private EntryEntity $entryWithIdWithDifferentName;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = include(TEST_DIR . "/setDatabaseForTestsScript.php");
    self::$phinxApp = new PhinxApplication();
  }

  public static function tearDownAfterClass(): void
  {
    self::$pdo = null;
  }

  protected function setUp(): void
  {
    self::$phinxApp->setAutoExit(false);
    self::$phinxApp->run(new StringInput('migrate -e testing'), new NullOutput());
    //Necessary for this test since the foreign keys need to already exist in the Request Table
    //This means the primary keys used in this test are arbitrary and set equal to those in the seed file
    self::$phinxApp->run(new StringInput('seed:run -e testing -s RequestTableSeed'), new NullOutput());
    $this->entryMapper = new EntryMapper(self::$pdo);

    //Entries id is given by the db as auto increment so entries without id are meant to be saved, while
    //entries with id are meant to be used to updates the ones without id. 
    $this->entryWithoutId = new EntryEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
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

    $this->entryWithoutIdWithDifferentName = new EntryEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->year,
      $this->nameNumber,
      $this->differentName,
      $this->mainPart,
      $this->amountOfOrder,
      $this->unitOfOrder,
      $this->reasonOfOrder,
      $this->priorityOfOrder,
      $this->observations,
      $this->id
    );

    $this->entryWithIdWithDifferentName = new EntryEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->year,
      $this->nameNumber,
      $this->differentName,
      $this->mainPart,
      $this->amountOfOrder,
      $this->unitOfOrder,
      $this->reasonOfOrder,
      $this->priorityOfOrder,
      $this->observations,
      $this->id
    );
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testFindingAllEntriesByPhiAndYear()
  {
    $this->entryMapper->saveEntry($this->entryWithoutId);
    $this->entryMapper->saveEntry($this->entryWithoutIdWithDifferentName);
    $result = $this->entryMapper->findAllByPhiAndYear($this->firstPartOfPhi, $this->secondPartOfPhi, $this->year);
    $this->assertCount(2, $result);
    $this->testTwoEntriesAreEqualWithoutCheckingForId($result[0], $this->entryWithoutId);
    $this->testTwoEntriesAreEqualWithoutCheckingForId($result[1], $this->entryWithoutIdWithDifferentName);
  }

  public function testSavingOneUnique()
  {
    $result = $this->entryMapper->saveEntry($this->entryWithoutId);
    $this->assertTrue($result);
    $dbRecord = self::$pdo->query("SELECT * FROM request_row WHERE id=1")->fetchAll();
    $entryFromDatabaseRecord = EntryFactory::createEntryFromRecord($dbRecord[0]);
    $this->testTwoEntriesAreEqualWithoutCheckingForId($entryFromDatabaseRecord, $this->entryWithoutId);
  }

  public function testSavingDuplicateUpdates()
  {
    $this->entryMapper->saveEntry($this->entryWithoutId);
    $this->entryMapper->updateEntry($this->entryWithIdWithDifferentName);
    $dbRecord = self::$pdo->query("SELECT * FROM request_row WHERE id={$this->entryWithIdWithDifferentName->getId()}")->fetchAll();
    $entryFromDatabaseRecord = EntryFactory::createEntryFromRecord($dbRecord[0]);
    $this->assertJsonStringEqualsJsonString(json_encode($entryFromDatabaseRecord), json_encode($this->entryWithIdWithDifferentName));
  }

  public function testDeletingOneByPhiDateAndId()
  {
    $this->entryMapper->saveEntry($this->entryWithoutId);
    $this->entryMapper->deleteOneByFullPhiYearAndId($this->firstPartOfPhi, $this->secondPartOfPhi, $this->year, $this->id);
    $dbRecord = self::$pdo->query("SELECT * FROM request_row WHERE id={$this->entryWithIdWithDifferentName->getId()}")->fetchAll();
    $this->assertCount(0, $dbRecord);
  }

  private function testTwoEntriesAreEqualWithoutCheckingForId(EntryEntity $firstEntry, EntryEntity $secondEntry)
  {
    $firstEntryAsArray = json_decode(json_encode($firstEntry), true);
    $secondEntryAsArray = json_decode(json_encode($secondEntry), true);

    unset($firstEntryAsArray['id']);
    unset($secondEntryAsArray['id']);

    $this->assertEquals($firstEntryAsArray, $secondEntryAsArray);
  }
}
