<?php

namespace app\models\domains\vehicle;

use PDO;

class VehicleMapper
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function getAllVehicles()
  {
    $sql = "SELECT * FROM vehicle";
    $statement = $this->pdo->prepare($sql);
    $statement->execute();
    $vehicles = VehicleFactory::createManyVehiclesFromRecord($statement->fetchAll());
    return $vehicles;
  }

  public function saveVehicle(VehicleEntity $vehicle): bool
  {
    $sql = <<<SQL
 INSERT INTO vehicle(id,plate, vehicle_type) 
VALUES (
:id,
:plate,
:vehicleType
);
SQL;

    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      "plate" => $vehicle->getPlate(),
      "vehicleType" => $vehicle->getVehicleType(),
      "id" => $vehicle->getId()
    ]);
  }

  public function deleteVehicle(int $vehicleId): bool
  {
    $sql = <<<SQL
      DELETE FROM vehicle WHERE id = :vehicleId;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute(["vehicleId" => $vehicleId]);
  }

  public function updateVehicle(VehicleEntity $vehicle, int $vehicleId): bool
  {
    $sql = <<<SQL
UPDATE vehicle 
SET
    plate = :plate ,
    vehicle_type= :vehicleType
WHERE
    id = :vehicleId;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      "plate" => $vehicle->getPlate(),
      "vehicleType" => $vehicle->getVehicleType(),
      "vehicleId" => $vehicleId
    ]);
  }
}
