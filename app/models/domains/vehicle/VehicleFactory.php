<?php

namespace app\models\domains\vehicle;

class VehicleFactory
{
  static function createVehicleFromRecord(array $dbRecord)
  {
    return new VehicleEntity(
      $dbRecord['plate'],
      $dbRecord['vehicle_type'],
      $dbRecord['id']
    );
  }

  static function createManyVehiclesFromRecord(array $dbRecords)
  {
    $vehicles = [];
    foreach ($dbRecords as $dbRecord) {
      $vehicleEntity = self::createVehicleFromRecord($dbRecord);
      array_push($vehicles, $vehicleEntity);
    }

    return $vehicles;
  }

  static function createVehicleFromPost(array $userPostInput)
  {
    return new VehicleEntity($userPostInput['plate'], $userPostInput['vehicleType']);
  }
}
