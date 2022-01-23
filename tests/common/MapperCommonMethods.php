<?php

namespace common;


use function PHPUnit\Framework\assertJsonStringEqualsJsonString;
use function PHPUnit\Framework\assertJsonStringNotEqualsJsonString;

class MapperCommonMethods
{
  public static function testTwoEntitiesAreEqualWithoutCheckingForId(mixed $firstEntry, mixed $secondEntry): void
  {
    $firstEntryAsArray = json_decode(json_encode($firstEntry), true);
    $secondEntryAsArray = json_decode(json_encode($secondEntry), true);

    unset($firstEntryAsArray["id"]);
    unset($secondEntryAsArray["id"]);

    assertJsonStringEqualsJsonString(json_encode($firstEntryAsArray), json_encode($secondEntryAsArray));
  }

  public static function testTwoEntitiesAreNotEqualWithoutCheckingForId(mixed $firstEntry, mixed $secondEntry): void
  {
    $firstEntryAsArray = json_decode(json_encode($firstEntry), true);
    $secondEntryAsArray = json_decode(json_encode($secondEntry), true);

    unset($firstEntryAsArray["id"]);
    unset($secondEntryAsArray["id"]);

    assertJsonStringNotEqualsJsonString(json_encode($firstEntryAsArray), json_encode($secondEntryAsArray));
  }

  public static function getAllFromDBTable($pdo, $tableName): array
  {
    switch ($tableName) {
      case "part":
        $tableName = "part";
        $factory = '\app\models\domains\part\PartFactory';
        $method = "createManyPartsFromRecord";
        break;
      case "entry":
        $tableName = "request_row";
        $factory = '\app\models\domains\request_entry\EntryFactory';
        $method = "createManyEntriesFromRecord";
        break;
      case "request":
        $tableName = "request";
        $factory = '\app\models\domains\request\RequestFactory';
        $method = "createManyRequestsFromRecord";
        break;
      case "tab":
        $tableName = "tab";
        $factory = '\app\models\domains\tab\TabFactory';
        $method = "createManyTabsFromRecord";
        break;
      case "vehicle":
        $tableName = "vehicle";
        $factory = '\app\models\domains\vehicle\VehicleFactory';
        $method = "createManyVehiclesFromRecord";
        break;
    }
    $sql = "SELECT * FROM {$tableName} ORDER BY id";
    $dbRecord = $pdo->query($sql)->fetchAll();
    return call_user_func_array([$factory, $method], [$dbRecord]);
  }
}
