<?php

namespace app\models\domains\request;

class RequestFactory
{
  public static function createManyRequestsFromRecord(array $records): array
  {
    $arrayOfRequest = [];
    foreach ($records as $record) {
      array_push($arrayOfRequest, self::createRequestFromRecord($record));
    }
    return $arrayOfRequest;
  }

  public static function createRequestFromRecord(array $record): RequestEntity
  {
    return new RequestEntity(
      $record['phi_first_part'],
      $record['phi_second_part'],
      $record['year'],
      $record['month'],
      $record['day'],
      $record['id']
    );
  }

  public static function createRequestFromUserInput(array $userPostInput): RequestEntity
  {
    return new RequestEntity(
      $userPostInput['firstPartOfPhi'] ?: null,
      $userPostInput['secondPartOfPhi'] ?: null,
      $userPostInput['year'] ?: null,
      $userPostInput['month'] ?: null,
      $userPostInput['day'] ?: null,
    );
  }

  public static function createManyRequestsFromUserInput(array $records): array
  {
    $arrayOfRequests = [];
    foreach ($records as $record) {
      array_push($arrayOfRequests, self::createRequestFromUserInput($record));
    }
    return $arrayOfRequests;
  }
}
