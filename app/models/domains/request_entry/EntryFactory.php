<?php

namespace app\models\domains\request_entry;

class EntryFactory
{
  public static function createEntryFromArrayOfRecords(array $records): array
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
      $record['request_phi_first_part'],
      $record['request_year'],
      $record['name_number'],
      $record['name'],
      $record['main_part'],
      $record['amount_of_order'],
      $record['unit_of_order'],
      $record['reason_of_order'],
      $record['priority_of_order'],
      $record['observations'],
      $record['id']
    );
  }

  public static function createEntryFromUserInput(int $firstPartOfPhi, int $year, array $record): EntryEntity
  {
    return new EntryEntity(
      $firstPartOfPhi,
      $year,
      $record['nameNumber'],
      $record['name'],
      $record['mainPart'],
      $record['amountOfOrder'],
      $record['unitOfOrder'],
      $record['reasonOfOrder'],
      $record['priorityOfOrder'],
      $record['observations'],
    );
  }

  public static function createEntriesFromArrayOfUserInput(int $firstPartOfPhi, int $year, array $record): array
  {
    $arrayOfRequestEntitites = [];
    foreach ($record as $entry) {
      array_push($arrayOfRequestEntitites, self::createEntryFromUserInput(
        $firstPartOfPhi,
        $year,
        $entry
      ));
    }
    return $arrayOfRequestEntitites;
  }
}
