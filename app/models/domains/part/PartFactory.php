<?php

namespace app\models\domains\part;

class PartFactory
{
  public static function createPartFromRecord(array $dbRecord): PartEntity
  {
    return new PartEntity(
      $dbRecord["date_recieved"],
      $dbRecord["pie_number"],
      $dbRecord["amount_recieved"],
      $dbRecord["tab_used"],
      $dbRecord["date_used"],
      $dbRecord["amount_used"],
      $dbRecord["part_id"]
    );
  }

  public static function createPartFromUserInput(array $userInput): PartEntity
  {
    return new PartEntity(
      $userInput["dateRecieved"] ?: null,
      $userInput["pieNumber"] ?: null,
      $userInput["amountRecieved"] ?: null,
      $userInput["tabUsed"] ?: null,
      $userInput["dateUsed"] ?: null,
      $userInput["amountUsed"] ?: null,
    );
  }

  public static function createManyPartsFromRecord(array $dbRecords): array
  {
    $allParts = [];
    foreach ($dbRecords as $record) {
      $part = self::createPartFromRecord($record);
      array_push($allParts, $part);
    }
    return $allParts;
  }

  public static function createManyPartsFromUserInput(array $userInput): array
  {
    $allParts = [];
    foreach ($userInput as $array) {
      $part = self::createPartFromUserInput($array);
      array_push($allParts, $part);
    }
    return $allParts;
  }
}
