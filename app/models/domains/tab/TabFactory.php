<?php

namespace app\models\domains\tab;

class TabFactory
{
  static function createTabFromRecord(array $dbRecord)
  {
    return new TabEntity(
      $dbRecord["name"],
      $dbRecord["usage"],
      $dbRecord["observations"],
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
      $userPostInput["id"]
    );
  }
}
