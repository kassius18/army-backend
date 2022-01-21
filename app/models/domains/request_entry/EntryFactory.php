<?php

namespace app\models\domains\request_entry;

class EntryFactory
{
  public static function createManyEntriesFromRecord(array $records): array
  {
    $arrayOfRequestEntitites = [];
    foreach ($records as $record) {
      array_push($arrayOfRequestEntitites, self::createEntryFromRecord($record));
    }
    return $arrayOfRequestEntitites;
  }

  public static function createEntryFromRecord(array $record): EntryEntity
  {
    return new EntryEntity(
      $record["name_number"],
      $record["name"],
      $record["main_part"],
      $record["amount_of_order"],
      $record["unit_of_order"],
      $record["reason_of_order"],
      $record["priority_of_order"],
      $record["observations"],
      $record["id"]
    );
  }

  public static function createEntryFromUserInput(array $record): EntryEntity
  {
    return new EntryEntity(
      $record["nameNumber"] ?: null,
      $record["name"] ?: null,
      $record["mainPart"] ?: null,
      $record["amountOfOrder"] ?: null,
      $record["unitOfOrder"] ?: null,
      $record["reasonOfOrder"] ?: null,
      $record["priorityOfOrder"] ?: null,
      $record["observations"] ?: null,
    );
  }

  public static function createManyEntriesFromUserInput(array $records): array
  {
    $arrayOfEntries = [];
    foreach ($records as $record) {
      array_push($arrayOfEntries, self::createEntryFromUserInput($record));
    }
    return $arrayOfEntries;
  }
}
