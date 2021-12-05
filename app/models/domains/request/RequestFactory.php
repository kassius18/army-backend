<?php

namespace app\models\domains\request;

class RequestFactory
{
  public static function createRequestEntityFromArrayOfRecords(array $records): array
  {
    $arrayOfRequestEntitites = [];
    foreach ($records as $key => $record) {
      array_push($arrayOfRequestEntitites, self::createRequestEntityFromRecord($record));
    }
    return $arrayOfRequestEntitites;
  }

  public static function createRequestEntityFromRecord(array $record): RequestEntity
  {
    return new RequestEntity(
      $record['phi_first_part'],
      $record['phi_second_part'],
      $record['year'],
      $record['month'],
      $record['day']
    );
  }
}
