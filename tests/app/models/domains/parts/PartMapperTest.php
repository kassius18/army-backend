<?php

use app\models\domains\part\PartMapper;
use common\MapperCommonMethods;
use common\SetDatabaseForTest;
use fixtures\PartFixture;
use PHPUnit\Framework\TestCase;

class PartMapperTest extends TestCase
{
  private static ?PDO $pdo;
  private static PartFixture $fixture;
  private PartMapper $partMapper;

  private int $entryId = 1;
  private int $secondEntryId = 2;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = SetDatabaseForTest::getConnection();
    self::$fixture = new PartFixture(self::$pdo);
  }

  public static function tearDownAfterClass(): void
  {
    self::$pdo = null;
  }

  protected function setUp(): void
  {
    SetDatabaseForTest::applyMigrations();
    //It will create a foreign key of entry_id = 1 and =2 which can be used when inserting parts. 
    $sql = "INSERT INTO request_row () VALUES ();";
    self::$pdo->exec($sql);
    self::$pdo->exec($sql);
    $this->partMapper = new PartMapper(self::$pdo);
  }

  protected function tearDown(): void
  {
    SetDatabaseForTest::removeMigrations();
  }

  public function testSavingPart()
  {
    $parts = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");
    $this->assertCount(0, $parts);

    $part = self::$fixture->createParts(1, true)[0];
    $this->partMapper->savePartToEntry($part, 1);

    $parts = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");
    $this->assertCount(1, $parts);
    $actual = $parts[0];

    $this->assertJsonStringEqualsJsonString(json_encode($part), json_encode($actual));
  }

  public function testSavingPartReturnsPartCreated()
  {
    $part = self::$fixture->createParts(1, true)[0];
    $expected = $this->partMapper->savePartToEntry($part, 1);

    [$actual] = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testFindingAllParts()
  {
    $expected = self::$fixture->createParts(3, true);

    self::$fixture->persistParts($expected, $this->entryId);
    //Adding extra parts to other entry to test that we find the correct ones
    self::$fixture->persistParts($expected, $this->secondEntryId);

    $actual = $this->partMapper->findAllPartsByEntryId($this->entryId);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testFindingOnePartById()
  {
    $expected = self::$fixture->createParts(2, true);
    self::$fixture->persistParts($expected);

    $actual = $this->partMapper->findPartById(2);
    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual));
  }

  public function testDeletingOnePartById()
  {
    $expected = self::$fixture->createParts(2, true);
    self::$fixture->persistParts($expected);

    $parts = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");
    $this->assertCount(2, $parts);

    $bool = $this->partMapper->deletePartById($expected[0]->getId());
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");
    $this->assertCount(1, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual[0]));
  }

  public function testUpdatingOnePartById()
  {
    [$part, $secondPart] = self::$fixture->createParts(2, true);
    [$editedPart] = self::$fixture->createParts(1, true);
    self::$fixture->persistParts([$part, $secondPart]);

    $parts = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");
    $this->assertCount(2, $parts);

    $this->partMapper->updatePartById($editedPart, $part->getId());

    $parts = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");
    $this->assertCount(2, $parts);

    $this->assertJsonStringEqualsJsonString(json_encode($editedPart), json_encode($parts[0]));
    $this->assertJsonStringNotEqualsJsonString(json_encode($editedPart), json_encode($parts[1]));
  }

  public function testUpdatingOnePartReturnsPartUpdated()
  {
    [$part] = self::$fixture->createParts(1, true);
    [$editedPart] = self::$fixture->createParts(1, true);
    self::$fixture->persistParts([$part]);

    $expected = $this->partMapper->updatePartById($editedPart, $part->getId(),);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "part");

    $this->assertJsonStringEqualsJsonString(
      json_encode($expected),
      json_encode($actual[0])
    );
  }
}
