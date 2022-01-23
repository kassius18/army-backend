<?php

namespace fixtures;

use app\models\domains\vehicle\VehicleEntity;
use PDO;

class VehicleFixture
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function createVehicles(int $numberOfVehiclesToCreate, bool|int $startingFromOne = true): array
  {
    $vehicles = [];

    $id = 0;

    if ($startingFromOne !== true) {
      $id = $startingFromOne;
    }

    for ($num = 1; $num <= $numberOfVehiclesToCreate; $num++) {
      $id++;
      $newVehicle = new VehicleEntity(
        uniqid(),
        uniqid(),
        $id
      );

      array_push($vehicles, $newVehicle);
    }
    return $vehicles;
  }

  public function persistVehicles(array $vehicles)
  {
    foreach ($vehicles as $vehicle)
      $this->persistVehicle($vehicle);
  }

  private function persistVehicle(VehicleEntity $vehicle)
  {
    $sql = <<<SQL
INSERT INTO vehicle(
    `id`,
    `plate`,
    `vehicle_type`
)
VALUES(
    :id,
    :plate,
    :vehicleType
);
SQL;

    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      "id" =>  $vehicle->getId(),
      "plate" =>  $vehicle->getPlate(),
      "vehicleType" => $vehicle->getVehicleType(),
    ]);
  }
}
