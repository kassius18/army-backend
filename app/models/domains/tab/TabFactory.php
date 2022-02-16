<?php

namespace app\models\domains\tab;

use app\models\domains\part\PartFactory;

class TabFactory
{

  public static function createTabsFromJOINRecord(array $records): array
  {
    $arrayOfTabs = [];
    $listOfPartIds = [];
    $listOfTabIds = [];

    foreach ($records as $record) {

      $tabRecord = array_slice($record, 0, 5, true);
      $partRecord = array_slice($record, 5);

      if (!isset($listOfTabIds[$tabRecord["tab_id"]])) {
        $tab = self::createTabFromRecord($tabRecord);
        $listOfTabIds[$tab->getId()] = $tab;
        array_push($arrayOfTabs, $tab);
      } else {
        $tab = $listOfTabIds[$tabRecord["tab_id"]];
      }

      if (!isset($listOfPartIds[$partRecord["part_id"]])) {
        if ($record["part_id"]) {
          $part = PartFactory::createPartFromRecord($partRecord);
          $listOfPartIds[$part->getId()] = $part;
          $tab->addParts([$part]);
        }
      } else {
        $part = $listOfPartIds[$partRecord["part_id"]];
      }
    }
    return $arrayOfTabs;
  }

  static function createTabFromRecord(array $dbRecord)
  {
    return new TabEntity(
      $dbRecord["name"],
      $dbRecord["usage"],
      $dbRecord["observations"],
      $dbRecord["starting_total"],
      $dbRecord["tab_id"]
    );
  }

  static function createManyTabsFromRecord(array $dbRecords)
  {
    $tabs = [];
    foreach ($dbRecords as $dbRecord) {
      $tabEntity = self::createTabFromRecord($dbRecord);
      array_push($tabs, $tabEntity);
    }

    return $tabs;
  }

  static function createTabFromInput(array $userPostInput)
  {
    return new TabEntity(
      $userPostInput["name"] ?: null,
      $userPostInput["usage"] ?: null,
      $userPostInput["observations"] ?: null,
      $userPostInput["startingTotal"] ?: null,
      $userPostInput["id"]
    );
  }
}
