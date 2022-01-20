<?php

use app\models\domains\part\PartFactory;
use app\models\domains\part\PartMapper;
use fixtures\PartFixture;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class PartMapperTest extends TestCase
{
  private static ?PDO $pdo;
  private static PhinxApplication $phinxApp;
  private static PartFixture $fixture;
  private PartMapper $partMapper;

  private int $entryId = 1;
  private int $secondEntryId = 2;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = include(TEST_DIR . "/setDatabaseForTestsScript.php");
    self::$phinxApp = new PhinxApplication();
    self::$fixture = new PartFixture(self::$pdo);
  }

  public static function tearDownAfterClass(): void
  {
    self::$pdo = null;
  }

  protected function setUp(): void
  {
    self::$phinxApp->setAutoExit(false);
    self::$phinxApp->run(new StringInput('migrate -e testing'), new NullOutput());
    //It will create a foreign key of entry_id = 1 and =2 which can be used when inserting parts. 
    $sql = "INSERT INTO request_row () VALUES ();";
    self::$pdo->exec($sql);
    self::$pdo->exec($sql);
    $this->partMapper = new PartMapper(self::$pdo);
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testSavingPart()
  {
    $parts = $this->getAllFromPartTableDb();
    $this->assertCount(0, $parts);

    $part = self::$fixture->createParts(1, true)[0];
    $bool = $this->partMapper->savePartToEntry($part, 1);
    $this->assertTrue($bool);

    $parts = $this->getAllFromPartTableDb();
    $this->assertCount(1, $parts);
    $actual = $parts[0];

    $this->assertJsonStringEqualsJsonString(json_encode($part), json_encode($actual));
  }

  public function testGettingAllParts()
  {
    $expected = self::$fixture->createParts(3, true);

    self::$fixture->persistParts($expected, $this->entryId);
    /* Persisting them as children of a second Entry too makes the test make 
     * sure that only the parts that belong to the entry are returned */
    self::$fixture->persistParts($expected, $this->secondEntryId);

    $actual = $this->partMapper->getAllPartsByEntryId($this->entryId);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testGettingOnePartByUsingItsId()
  {
    $expected = self::$fixture->createParts(2, true);
    self::$fixture->persistParts($expected);

    $actual = $this->partMapper->getPartById(2);
    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual));
  }

  public function testDeletingOnePartById()
  {
    $expected = self::$fixture->createParts(2, true);
    self::$fixture->persistParts($expected);

    $parts = $this->getAllFromPartTableDb();
    $this->assertCount(2, $parts);

    $bool = $this->partMapper->deleteById($expected[0]->getId());
    $this->assertTrue($bool);

    $actual = $this->getAllFromPartTableDb();
    $this->assertCount(1, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual[0]));
  }

  public function testUpdatingOnePartById()
  {
    [$part, $editedPart] = self::$fixture->createParts(2, true);
    self::$fixture->persistParts([$part]);

    $parts = $this->getAllFromPartTableDb();
    $this->assertCount(1, $parts);
    $this->assertJsonStringEqualsJsonString(json_encode($part), json_encode($parts[0]));

    $bool = $this->partMapper->updateById($part->getId(), $editedPart);
    $this->assertTrue($bool);

    $parts = $this->getAllFromPartTableDb();
    $this->assertCount(1, $parts);
    $this->assertEqualsWithoutCheckingForId(json_encode($editedPart), json_encode($parts[0]));
  }

  private function getAllFromPartTableDb(): array
  {
    $sql = "SELECT * FROM part";
    $dbRecord = self::$pdo->query($sql)->fetchAll();
    return PartFactory::createManyPartsFromRecord($dbRecord);
  }

  private function assertEqualsWithoutCheckingForId($expected, $actual)
  {
    $expectedArr = json_decode($expected, true);
    $actualArr = json_decode($actual, true);
    unset($expectedArr["id"]);
    unset($actualArr["id"]);

    return $this->assertJsonStringEqualsJsonString(json_encode($expectedArr), json_encode($actualArr));
  }
}
