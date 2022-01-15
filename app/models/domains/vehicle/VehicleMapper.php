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

  public function saveVehicle(VehicleEntity $vehicle)
  {
    $sql = <<<SQL
 INSERT INTO vehicle(plate, vehicle_type) 
VALUES (
:plate,
:vehicleType
);
SQL;

    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      "plate" => $vehicle->getPlate(),
      "vehicleType" => $vehicle->getVehicleType()
    ]);
  }

  public function deleteVehicle(int $vehicleId)
  {
    $sql = <<<SQL
      DELETE FROM vehicle WHERE id = :vehicleId;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute(["vehicleId" => $vehicleId]);
  }

  public function updateVehicle(int $vehicleId, VehicleEntity $vehicle)
  {
    $sql = <<<SQL
UPDATE vehicle 
SET plate = :plate , vehicle_type= :vehicleType
WHERE id = :vehicleId;
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute([
      "plate" => $vehicle->getPlate(),
      "vehicleType" => $vehicle->getVehicleType(),
      "vehicleId" => $vehicleId
    ]);
  }
}
