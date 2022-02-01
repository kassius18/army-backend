<?php

use app\models\domains\part\PartMapper;
use app\models\domains\request_entry\EntryMapper;
use common\MapperCommonMethods;
use fixtures\EntryFixture;
use fixtures\PartFixture;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class EntryMapperTest extends TestCase
{
  private static ?PDO $pdo;
  private static PhinxApplication $phinxApp;
  private static EntryFixture $fixture;
  private static PartFixture $partFixture;

  private array $requestPrimaryKeys = ["firstPartOfPhi" => 1, "year" => 2];
  private array $otherRequestPrimaryKeys = ["firstPartOfPhi" => 2, "year" => 3];
  private int $consumableId = 1;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = include(TEST_DIR . "/setDatabaseForTestsScript.php");
    self::$phinxApp = new PhinxApplication();
    self::$fixture = new EntryFixture(self::$pdo);
    self::$partFixture = new PartFixture(self::$pdo);
  }

  public static function tearDownAfterClass(): void
  {
    self::$pdo = null;
  }

  protected function setUp(): void
  {
    self::$phinxApp->setAutoExit(false);
    self::$phinxApp->run(new StringInput('migrate -e testing'), new NullOutput());
    self::$fixture->setConsumableIdForTest($this->consumableId);
    //Inserting a row into parent table to test methods that require parent's PK
    $sql = "INSERT INTO `request` (`phi_first_part`, `year`) VALUES (1, 2);";
    self::$pdo->exec($sql);
    $sql = "INSERT INTO `request` (`phi_first_part`, `year`) VALUES (2, 3);";
    self::$pdo->exec($sql);
    $sql = "INSERT INTO `tab` (`tab_id`,`name`, `usage`) VALUES ({$this->consumableId}, 'test', 'test');";
    self::$pdo->exec($sql);
    $this->entryMapper = new EntryMapper(self::$pdo);
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testFindingAllEntriesByPhiAndYear()
  {
    $expected = self::$fixture->createEntries(2, true);
    self::$fixture->persistEntries($expected, $this->requestPrimaryKeys);

    //Adding extra entries to another request  to test that we find the correct ones
    $entries = self::$fixture->createEntries(3, true);
    self::$fixture->persistEntries($entries, $this->otherRequestPrimaryKeys);

    $actual = $this->entryMapper->findAllByPhiAndYear(
      $this->requestPrimaryKeys["firstPartOfPhi"],
      $this->requestPrimaryKeys["year"]
    );

    $this->assertCount(2, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testFindOneEntryById()
  {
    $entries = self::$fixture->createEntries(2, true);
    self::$fixture->persistEntries($entries, $this->requestPrimaryKeys);

    $actual = $this->entryMapper->findEntryById($entries[0]->getId());
    $this->assertJsonStringEqualsJsonString(json_encode($entries[0]), json_encode($actual));
  }

  public function testSavingEntry()
  {
    $entries = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(0, $entries);

    $expected = self::$fixture->createEntries(1, true);
    $this->entryMapper->saveEntryToRequest($expected[0], $this->requestPrimaryKeys);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(1, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($expected[0]), json_encode($actual[0]));
  }

  public function testSavingEntryReturnsCreatedEntry()
  {
    $entries = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(0, $entries);

    [$entry] = self::$fixture->createEntries(1, true);
    $expected = $this->entryMapper->saveEntryToRequest($entry, $this->requestPrimaryKeys);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual[0]));
  }

  public function testDeletingOneById()
  {
    $expected = self::$fixture->createEntries(2, true);
    self::$fixture->persistEntries($expected);

    $bool = $this->entryMapper->deleteEntryById($expected[0]->getId());
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(1, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual[0]));
  }

  public function testDeletingEntryDeletesChildrenPartsToo()
  {
    $expected = self::$fixture->createEntries(2, true);
    self::$fixture->persistEntries($expected);
    $partsForEntryOne = self::$partFixture->createParts(3, true);
    self::$partFixture->persistParts($partsForEntryOne, $expected[0]->getId());
    $partsForEntryTwo = self::$partFixture->createParts(2, true, 4);
    self::$partFixture->persistParts($partsForEntryTwo, $expected[1]->getId());

    $actualEntries = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(2, $actualEntries);

    $actualParts = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");
    $this->assertCount(5, $actualParts);

    $bool = $this->entryMapper->deleteEntryById($expected[0]->getId());
    $this->assertTrue($bool);

    $actualEntries = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertcount(1, $actualEntries);

    $actualParts = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");
    $this->assertcount(2, $actualParts);
  }

  public function testUpdatingOneById()
  {
    [$entry, $secondEntry] = self::$fixture->createEntries(2, true);
    [$editedEntry] = self::$fixture->createEntries(1, true);
    self::$fixture->persistEntries([$entry, $secondEntry]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(2, $actual);

    $this->entryMapper->updateEntryById($editedEntry, $entry->getId());

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(2, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($editedEntry), json_encode($actual[0]));
    $this->assertJsonStringNotEqualsJsonString(json_encode($entry), json_encode($actual[1]));
  }

  public function testUpdatingReturnsUpdatedEntry()
  {
    [$entry] = self::$fixture->createEntries(1, true);
    [$editedEntry] = self::$fixture->createEntries(1, true);
    self::$fixture->persistEntries([$entry]);

    $expectedEntry = $this->entryMapper->updateEntryById($editedEntry, $entry->getId());
    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(1, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($expectedEntry), json_encode($actual[0]));
  }
}
