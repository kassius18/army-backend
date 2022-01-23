<?php

use app\models\domains\tab\TabMapper;
use common\MapperCommonMethods;
use fixtures\TabFixture;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class TabMapperTest extends TestCase
{
  private static ?PDO $pdo;
  private static PhinxApplication $phinxApp;
  private static TabFixture $fixture;
  private TabMapper $tabMapper;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = include(TEST_DIR . "/setDatabaseForTestsScript.php");
    self::$phinxApp = new PhinxApplication();
    self::$fixture = new TabFixture(self::$pdo);
  }

  public static function tearDownAfterClass(): void
  {
    self::$pdo = null;
  }

  protected function setUp(): void
  {
    self::$phinxApp->setAutoExit(false);
    self::$phinxApp->run(new StringInput('migrate -e testing'), new NullOutput());
    $this->tabMapper = new TabMapper(self::$pdo);
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testGettingAllTabsFromDb()
  {
    $expected = self::$fixture->createTabs(2);
    self::$fixture->persistTabs($expected);

    $actual = $this->tabMapper->getAllTabs();
    $this->assertCount(2, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testSavingTab()
  {
    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(0, $actual);

    $expected = self::$fixture->createTabs(1);
    $bool = $this->tabMapper->saveTab($expected[0]);
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(1, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testDeletingTab()
  {
    $expected = self::$fixture->createTabs(2);
    self::$fixture->persistTabs($expected);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(2, $actual);

    $bool = $this->tabMapper->deleteTab(1);
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(1, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual[0]));
  }

  public function testEditingTab()
  {
    [$tab, $secondTab] = self::$fixture->createTabs(2);
    [$editedTab] = self::$fixture->createTabs(1);
    self::$fixture->persistTabs([$tab, $secondTab]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(2, $actual);

    $bool = $this->tabMapper->updateTab($editedTab, $editedTab->getId());
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(2, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($editedTab), json_encode($actual[0]));
    MapperCommonMethods::testTwoEntitiesAreNotEqualWithoutCheckingForId($secondTab, $editedTab);
  }
}
