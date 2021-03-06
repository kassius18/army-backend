<?php

use app\models\domains\tab\TabMapper;
use common\MapperCommonMethods;
use common\SetDatabaseForTest;
use fixtures\EntryFixture;
use fixtures\PartFixture;
use fixtures\TabFixture;
use PHPUnit\Framework\TestCase;

class TabMapperTest extends TestCase
{
  private static ?PDO $pdo;
  private static TabFixture $fixture;
  private static EntryFixture $entryFixture;
  private static PartFixture $partFixture;
  private TabMapper $tabMapper;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = SetDatabaseForTest::getConnection();
    self::$fixture = new TabFixture(self::$pdo);
    self::$entryFixture = new EntryFixture(self::$pdo);
    self::$partFixture = new PartFixture(self::$pdo);
  }

  public static function tearDownAfterClass(): void
  {
    self::$pdo = null;
  }

  protected function setUp(): void
  {
    SetDatabaseForTest::applyMigrations();
    $this->tabMapper = new TabMapper(self::$pdo);
  }

  protected function tearDown(): void
  {
    SetDatabaseForTest::removeMigrations();
  }

  public function testGettingAllTabsFromDb()
  {
    $expected = self::$fixture->createTabs(2);
    self::$fixture->persistTabs($expected);

    $actual = $this->tabMapper->getAllTabs();
    $this->assertCount(2, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testFindingOneTabById()
  {
    $expected = self::$fixture->createTabs(2, true);
    self::$fixture->persistTabs($expected);

    $actual = $this->tabMapper->findTabById(2);
    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual));
  }

  public function testSavingTab()
  {
    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(0, $actual);

    $expected = self::$fixture->createTabs(1);
    $this->tabMapper->saveTab($expected[0]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(1, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testSavingTabReturnsTabCreated()
  {
    [$tab] = self::$fixture->createTabs(1);
    $expected = $this->tabMapper->saveTab($tab);

    [$actual] = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testSavingTabReturnsCreatedTab()
  {
    [$tab] = self::$fixture->createTabs(1);
    $expected = $this->tabMapper->saveTab($tab);

    [$actual] = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
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
    [$editedTab] = self::$fixture->createTabs(1, 2);
    self::$fixture->persistTabs([$tab, $secondTab]);

    $allTabsInDb = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(2, $allTabsInDb);

    $this->tabMapper->updateTab($editedTab, $tab->getId());

    $allTabsInDb = MapperCommonMethods::getAllFromDBTable(self::$pdo, "tab");
    $this->assertCount(2, $allTabsInDb);

    foreach ($allTabsInDb as $tab) {
      if ($tab->getId() === $editedTab->getId()) {
        $actual = $tab;
      } else {
        $nonEditedTab = $tab;
      }
    }

    $this->assertJsonStringEqualsJsonString(json_encode($editedTab), json_encode($actual));
    $this->assertJsonStringNotEqualsJsonString(
      json_encode($editedTab),
      json_encode($nonEditedTab)
    );
  }

  public function testEditingTabReturnsEditedTab()
  {
    [$tab] = self::$fixture->createTabs(1, true);
    [$editedTab] = self::$fixture->createTabs(1);
    self::$fixture->persistTabs([$tab]);

    $actual = $this->tabMapper->updateTab($editedTab, $editedTab->getId());

    $this->assertJsonStringEqualsJsonString(json_encode($editedTab), json_encode($actual));
  }

  public function testGettingIdsOfAllNonEmptyTabs()
  {
    $tabs = self::$fixture->createTabs(3, true, rand(1, 5));
    self::$fixture->persistTabs($tabs);

    $idsOfNonEmptyTabs = [$tabs[1]->getId()];

    self::$entryFixture->setConsumableIdForTest($tabs[1]->getId());
    $entriesThatBelongToTab = self::$entryFixture->createEntries(1, true);

    self::$entryFixture->setConsumableIdForTest(null);
    $orphanEntry = self::$entryFixture->createEntries(1, true);
    self::$entryFixture->persistEntries([...$entriesThatBelongToTab, ...$orphanEntry]);

    $partsThatBelongToEntryThatBelongsToTab = self::$partFixture->createParts(2, true);
    $partsThatBelongToOrphanEntry = self::$partFixture->createParts(2, true);

    self::$partFixture->persistParts(
      $partsThatBelongToEntryThatBelongsToTab,
      $entriesThatBelongToTab[0]->getId()
    );
    self::$partFixture->persistParts(
      $partsThatBelongToOrphanEntry,
      $orphanEntry[0]->getId()
    );
    $actual = $this->tabMapper->getIdsOfNonEmptyTabs();
    $this->assertEquals($idsOfNonEmptyTabs, $actual);
  }

  public function testGettingAllTabsWithParts()
  {
    $tabs = self::$fixture->createTabs(3);
    self::$fixture->persistTabs($tabs);

    self::$entryFixture->setConsumableIdForTest($tabs[0]->getId());
    $entriesThatBelongToTab = self::$entryFixture->createEntries(2, true);
    self::$entryFixture->setConsumableIdForTest($tabs[1]->getId());
    $entriesThatBelongToSecondTab = self::$entryFixture->createEntries(2, true, 2);

    self::$entryFixture->setConsumableIdForTest($tabs[2]->getId());
    $entriesThatBelongToDifferentTab = self::$entryFixture->createEntries(2, true, 4);

    self::$entryFixture->setConsumableIdForTest(null);
    $entriesThatBelongToNoTab = self::$entryFixture->createEntries(2, true, 6);

    $allEntries = [
      ...$entriesThatBelongToTab,
      ...$entriesThatBelongToSecondTab,
      ...$entriesThatBelongToDifferentTab,
      ...$entriesThatBelongToNoTab
    ];
    self::$entryFixture->persistEntries($allEntries);

    $partsThatBelongToFirstEntryThatBelongsToTab = self::$partFixture->createParts(2, true);
    self::$partFixture->persistParts(
      $partsThatBelongToFirstEntryThatBelongsToTab,
      $entriesThatBelongToTab[0]->getId()
    );

    $partsThatBelongToSecondEntryThatBelongsToTab = self::$partFixture->createParts(1, true, 2);
    self::$partFixture->persistParts(
      $partsThatBelongToSecondEntryThatBelongsToTab,
      $entriesThatBelongToTab[1]->getId()
    );

    $partsThatBelongToFirstEntryThatBelongsToSecondTab = self::$partFixture->createParts(1, true, 3);
    self::$partFixture->persistParts(
      $partsThatBelongToFirstEntryThatBelongsToSecondTab,
      $entriesThatBelongToSecondTab[0]->getId()
    );

    $partsThatBelongToSecondEntryThatBelongsToSecondTab = self::$partFixture->createParts(2, true, 4);
    self::$partFixture->persistParts(
      $partsThatBelongToSecondEntryThatBelongsToSecondTab,
      $entriesThatBelongToSecondTab[1]->getId()
    );

    $partsThatBelongToEntryWithNoTab = self::$partFixture->createParts(1, true, 8);
    self::$partFixture->persistParts($partsThatBelongToEntryWithNoTab);

    $tabs[0]->addParts([
      ...$partsThatBelongToFirstEntryThatBelongsToTab, ...$partsThatBelongToSecondEntryThatBelongsToTab
    ]);
    $tabs[1]->addParts([
      ...$partsThatBelongToFirstEntryThatBelongsToSecondTab, ...$partsThatBelongToSecondEntryThatBelongsToSecondTab
    ]);

    $actual = $this->tabMapper->getAllTabsWithParts();
    $this->assertJsonStringEqualsJsonString(
      json_encode($tabs),
      json_encode($actual)
    );
  }
  public function testGettingNonEmptyTabsWithParts()
  {
    $tabs = self::$fixture->createTabs(3);
    self::$fixture->persistTabs($tabs);

    self::$entryFixture->setConsumableIdForTest($tabs[0]->getId());
    $entriesThatBelongToTab = self::$entryFixture->createEntries(2, true);
    self::$entryFixture->setConsumableIdForTest($tabs[1]->getId());
    $entriesThatBelongToSecondTab = self::$entryFixture->createEntries(2, true, 2);

    self::$entryFixture->setConsumableIdForTest($tabs[2]->getId());
    $entriesThatBelongToDifferentTab = self::$entryFixture->createEntries(2, true, 4);

    self::$entryFixture->setConsumableIdForTest(null);
    $entriesThatBelongToNoTab = self::$entryFixture->createEntries(2, true, 6);

    $allEntries = [
      ...$entriesThatBelongToTab,
      ...$entriesThatBelongToSecondTab,
      ...$entriesThatBelongToDifferentTab,
      ...$entriesThatBelongToNoTab
    ];
    self::$entryFixture->persistEntries($allEntries);

    $partsThatBelongToFirstEntryThatBelongsToTab = self::$partFixture->createParts(2, true);
    self::$partFixture->persistParts(
      $partsThatBelongToFirstEntryThatBelongsToTab,
      $entriesThatBelongToTab[0]->getId()
    );

    $partsThatBelongToSecondEntryThatBelongsToTab = self::$partFixture->createParts(1, true, 2);
    self::$partFixture->persistParts(
      $partsThatBelongToSecondEntryThatBelongsToTab,
      $entriesThatBelongToTab[1]->getId()
    );

    $partsThatBelongToFirstEntryThatBelongsToSecondTab = self::$partFixture->createParts(1, true, 3);
    self::$partFixture->persistParts(
      $partsThatBelongToFirstEntryThatBelongsToSecondTab,
      $entriesThatBelongToSecondTab[0]->getId()
    );

    $partsThatBelongToSecondEntryThatBelongsToSecondTab = self::$partFixture->createParts(2, true, 4);
    self::$partFixture->persistParts(
      $partsThatBelongToSecondEntryThatBelongsToSecondTab,
      $entriesThatBelongToSecondTab[1]->getId()
    );

    $partsThatBelongToEntryWithNoTab = self::$partFixture->createParts(1, true, 8);
    self::$partFixture->persistParts($partsThatBelongToEntryWithNoTab);

    $tabs[0]->addParts([
      ...$partsThatBelongToFirstEntryThatBelongsToTab, ...$partsThatBelongToSecondEntryThatBelongsToTab
    ]);
    $tabs[1]->addParts([
      ...$partsThatBelongToFirstEntryThatBelongsToSecondTab, ...$partsThatBelongToSecondEntryThatBelongsToSecondTab
    ]);

    $actual = $this->tabMapper->getAllNonEmptyTabsWithParts();
    $this->assertJsonStringEqualsJsonString(
      json_encode([$tabs[0], $tabs[1]]),
      json_encode($actual)
    );
  }


  public function testGettingAllPartsBelogningToALLEntriesBelongingToATab()
  {
    $tabs = self::$fixture->createTabs(3);
    self::$fixture->persistTabs($tabs);

    $tabBeingTested = $tabs[0];

    self::$entryFixture->setConsumableIdForTest($tabBeingTested->getId());
    $entriesThatBelongToTab = self::$entryFixture->createEntries(2, true);

    self::$entryFixture->setConsumableIdForTest($tabs[1]->getId());
    $entriesThatBelongToDifferentTab = self::$entryFixture->createEntries(1, true, 2);

    self::$entryFixture->setConsumableIdForTest(null);
    $entriesThatBelongToNoTab = self::$entryFixture->createEntries(1, true, 3);

    $allEntries = [
      ...$entriesThatBelongToTab,
      ...$entriesThatBelongToDifferentTab,
      ...$entriesThatBelongToNoTab
    ];
    self::$entryFixture->persistEntries($allEntries);

    $partsThatBelongToFirstEntryThatBelongsToTab = self::$partFixture->createParts(2, true);
    self::$partFixture->persistParts(
      $partsThatBelongToFirstEntryThatBelongsToTab,
      $entriesThatBelongToTab[0]->getId()
    );

    $partsThatBelongToSecondEntryThatBelongsToTab = self::$partFixture->createParts(1, true, 2);
    self::$partFixture->persistParts(
      $partsThatBelongToSecondEntryThatBelongsToTab,
      $entriesThatBelongToTab[1]->getId()
    );

    $partsThatBelongToTabThatBelongsToDifferentEntry = self::$partFixture->createParts(2, true, 3);
    self::$partFixture->persistParts(
      $partsThatBelongToTabThatBelongsToDifferentEntry,
      $entriesThatBelongToDifferentTab[0]->getId()
    );

    $partsThatBelongToEntryWithNoTab = self::$partFixture->createParts(1, true, 5);
    self::$partFixture->persistParts($partsThatBelongToEntryWithNoTab);

    $partsExpected = [
      ...$partsThatBelongToFirstEntryThatBelongsToTab,
      ...$partsThatBelongToSecondEntryThatBelongsToTab
    ];

    $actualParts = $this->tabMapper->findAllPartsThatBelongToTab($tabBeingTested->getId());
    $this->assertJsonStringEqualsJsonString(
      json_encode($partsExpected),
      json_encode($actualParts)
    );
  }
}
