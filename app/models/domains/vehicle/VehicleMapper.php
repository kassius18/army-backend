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

  public function findVehicleById(int $id)
  {
    $sql = <<<SQL
SELECT * FROM vehicle WHERE vehicle_id = :id;
SQL;
    $stm = $this->pdo->prepare($sql);
    $stm->execute([
      "id" => $id,
    ]);
    return VehicleFactory::createVehicleFromRecord($stm->fetch());
  }

  public function getAllVehicles()
  {
    $sql = "SELECT * FROM vehicle";
    $statement = $this->pdo->prepare($sql);
    $statement->execute();
    $vehicles = VehicleFactory::createManyVehiclesFromRecord($statement->fetchAll());
    return $vehicles;
  }

  public function saveVehicle(VehicleEntity $vehicle): false|VehicleEntity
  {
    $sql = <<<SQL
 INSERT INTO vehicle(vehicle_id,plate, vehicle_type) 
VALUES (
:id,
:plate,
:vehicleType
);
SQL;

    $statement = $this->pdo->prepare($sql);
    if ($statement->execute([
      "plate" => $vehicle->getPlate(),
      "vehicleType" => $vehicle->getVehicleType(),
      "id" => $vehicle->getId()
    ])) {
      $lastId = $this->pdo->lastInsertId();
      return $this->findVehicleById($lastId);
    } else {
      return false;
    }
  }

  public function deleteVehicle(int $vehicleId): bool
  {
    $sql = <<<SQL
      DELETE FROM vehicle WHERE vehicle_id = :vehicleId;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute(["vehicleId" => $vehicleId]);
  }

  public function updateVehicle(VehicleEntity $vehicle, int $vehicleId): false|VehicleEntity
  {
    $sql = <<<SQL
UPDATE vehicle 
SET
    plate = :plate ,
    vehicle_type= :vehicleType
WHERE
    vehicle_id = :vehicleId;
SQL;
    $statement = $this->pdo->prepare($sql);
    if ($statement->execute([
      "plate" => $vehicle->getPlate(),
      "vehicleType" => $vehicle->getVehicleType(),
      "vehicleId" => $vehicleId
    ])) {
      return $this->findVehicleById($vehicleId);
    } else {
      return false;
    };
  }
}
