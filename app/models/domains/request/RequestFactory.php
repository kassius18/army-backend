<?php

namespace app\models\domains\request;

use app\models\domains\request_entry\EntryFactory;

class RequestFactory
{
  public static function createRequestEntityFromArrayOfRecords(array $records): array
  {
    $arrayOfRequestEntitites = [];
    foreach ($records as $record) {
      array_push($arrayOfRequestEntitites, self::createRequestEntityFromRecord($record));
    }
    return $arrayOfRequestEntitites;
  }

  public static function createRequestEntityFromRecord(array $record): RequestEntity
  {
    $entryEntitiesThatBelongToRequest = [];
    foreach ($record as $entry) {
      if (
        ($entry['request_phi_first_part'] === $entry['phi_first_part'])
        &&
        ($entry['request_year'] === $entry['year'])
      ) {
        $entry = EntryFactory::createEntryFromRecord($entry);
        array_push($entryEntitiesThatBelongToRequest, $entry);
      }
    }
    $requestEntity = new RequestEntity(
      $record[0]['phi_first_part'],
      $record[0]['phi_second_part'],
      $record[0]['year'],
      $record[0]['month'],
      $record[0]['day'],
      $entryEntitiesThatBelongToRequest
    );
    return $requestEntity;
  }

  public static function createRequestFromUserInput(array $userPostInput): RequestEntity
  {
    $arrayOfEntryEntities = [];
    foreach ($userPostInput['entries'] as $entry) {
      $entryEntity = EntryFactory::createEntryFromUserInput(
        $userPostInput['firstPartOfPhi'],
        $userPostInput['year'],
        $entry
      );
      array_push($arrayOfEntryEntities, $entryEntity);
    }
    return new RequestEntity(
      $userPostInput['firstPartOfPhi'],
      $userPostInput['secondPartOfPhi'],
      $userPostInput['year'],
      $userPostInput['month'],
      $userPostInput['day'],
      $arrayOfEntryEntities
    );
  }

  /* private function separateArrayToUniqueRequests(array $dbRecords) */
  /* { */
  /*   var_dump($dbRecords); */
  /*   $oldFirstPartOfPhi = 0; */
  /*   $newFirstPartOfPhi = 0; */
  /*   $oldSecondPartOfPhi = 0; */
  /*   $newSecondPartOfPhi = 0; */
  /*   $oldYear = 0; */
  /*   $newYear = 0; */

  /*   $compressedArray = []; */
  /*   foreach ($dbRecords as $record) { */
  /*     $newFirstPartOfPhi = $record['firstPartOfPhi']; */
  /*     $newSecondPartOfPhi = $record['secondPartOfPhi']; */
  /*     $newYear = $record['year']; */

  /*     if ( */
  /*       ($oldFirstPartOfPhi !== $newFirstPartOfPhi) */
  /*       || */
  /*       ($oldSecondPartOfPhi !== $newFirstPartOfPhi) */
  /*       || */
  /*       ($oldYear !== $newYear) */
  /*     ) { */
  /*       array_push() */
  /*     } */
  /*   } */
  /* } */
}
