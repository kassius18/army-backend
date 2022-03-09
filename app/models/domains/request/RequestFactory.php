<?php

namespace app\models\domains\request;

use app\models\domains\part\PartFactory;
use app\models\domains\request_entry\EntryFactory;

class RequestFactory
{
  public static function createRequestsFromJOINRecord(array $records): array
  {
    $arrayOfRequests = [];
    $listOfPartIds = [];
    $listOfEntryIds = [];
    $listOfRequestIds = [];

    foreach ($records as $record) {

      $requestRecord = array_slice($record, 0, 7, true);
      $entryRecord = array_slice($record, 7, 11, true);
      $partRecord = array_slice($record, 18);

      if (!isset($listOfRequestIds[$requestRecord["request_id"]])) {
        $request = self::createRequestFromRecord($requestRecord);
        $listOfRequestIds[$request->getId()] = $request;
        array_push($arrayOfRequests, $request);
      } else {
        $request = $listOfRequestIds[$requestRecord["request_id"]];
      }

      if (!isset($listOfEntryIds[$entryRecord["request_row_id"]])) {
        if ($record["request_row_id"]) {
          $entry = EntryFactory::createEntryFromRecord($entryRecord);
          $listOfEntryIds[$entry->getId()] = $entry;
          $request->addEntries([$entry]);
        }
      } else {
        $entry = $listOfEntryIds[$entryRecord["request_row_id"]];
      }

      if (!isset($listOfPartIds[$partRecord["part_id"]])) {

        if ($record["part_id"]) {
          $part = PartFactory::createPartFromRecord($partRecord);
          $listOfPartIds[$part->getId()] = $part;
          $entry->addParts([$part]);
        }
      } else {
        $part = $listOfPartIds[$partRecord["part_id"]];
      }
    }
    return $arrayOfRequests;
  }

  public static function createManyRequestsFromRecord(array $records): array
  {
    $requests = [];
    foreach ($records as $record) {
      $request = self::createRequestFromRecord($record);
      array_push($requests, $request);
    }
    return $requests;
  }

  public static function createRequestFromRecord(array $record): RequestEntity
  {
    return new RequestEntity(
      $record["phi_first_part"],
      $record["phi_second_part"],
      $record["year"],
      $record["month"],
      $record["day"],
      $record["request_vehicle_id"],
      $record["request_id"]
    );
  }

  public static function createRequestFromUserInput(array $userPostInput): RequestEntity
  {
    return new RequestEntity(
      $userPostInput["firstPartOfPhi"] ?: null,
      $userPostInput["secondPartOfPhi"] ?: null,
      $userPostInput["year"] ?: null,
      $userPostInput["month"] ?: null,
      $userPostInput["day"] ?: null,
      $userPostInput["vehicleId"] ?: null,
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
