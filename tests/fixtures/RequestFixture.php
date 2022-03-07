<?php

namespace fixtures;

use app\models\domains\request\RequestEntity;

class RequestFixture
{
  private \PDO $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function createRequests(
    int $numberOfRequestsToCreate,
    bool $withId = false,
    bool|int $startingFromOne = true
  ): array {
    $requests = [];

    $id = 0;

    if ($startingFromOne !== true) {
      $id = $startingFromOne;
    }

    for ($num = 1; $num <= $numberOfRequestsToCreate; $num++) {
      $id++;
      array_push($requests, $this->createOneRequestEntity($withId ? $id : null, []));
    }

    return $requests;
  }

  public function createRequestsWithInputs(
    int $numberOfRequestsToCreate,
    array $inputs,
    bool $withId = false,
    bool|int $startingFromOne = true
  ) {
    $requests = [];

    $id = 0;

    if ($startingFromOne !== true) {
      $id = $startingFromOne;
    }

    for ($num = 1; $num <= $numberOfRequestsToCreate; $num++) {
      $id++;
      array_push($requests, $this->createOneRequestEntity($withId ? $id : null, $inputs));
    }

    return $requests;
  }

  private function createOneRequestEntity($id = null, $inputs = [])
  {
    return new RequestEntity(
      isset($inputs["firstPartOfPhi"]) ? $inputs["firstPartOfPhi"] : rand(),
      isset($inputs["secondPartOfPhi"]) ? $inputs["secondPartOfPhi"] : rand(),
      isset($inputs["year"]) ? rand($inputs["year"][0], $inputs["year"][1]) : rand(),
      isset($inputs["month"]) ? rand($inputs["month"][0], $inputs["month"][1]) : rand(),
      isset($inputs["day"]) ? rand($inputs["day"][0], $inputs["day"][1]) : rand(),
      isset($inputs["vehicleId"]) ? $inputs["vehicleId"] : null,
      $id
    );
  }

  public function persistRequests(array $requests)
  {
    foreach ($requests as $request)
      $this->persistRequest($request);
  }

  private function persistRequest(RequestEntity $request)
  {
    $sql = <<<SQL
INSERT INTO request(
    phi_first_part,
    phi_second_part,
    year,
    month,
    day,
    request_vehicle_id
)
VALUES(
    :firstPartOfPhi,
    :secondPartOfPhi,
    :year,
    :month,
    :day,
    :vehicleId
);
SQL;
    $stm = $this->pdo->prepare($sql);
    $stm->execute([
      "firstPartOfPhi" => $request->getFirstPhi(),
      "secondPartOfPhi" => $request->getSecondPhi(),
      "year" => $request->getYear(),
      "month" => $request->getMonth(),
      "day" => $request->getDay(),
      "vehicleId" => $request->getVehicleId(),
    ]);
  }

  public function sortRequestsByYear(array $requests): array
  {
    usort($requests, "self::sortByYear");
    return $requests;
  }

  public function sortRequestsByDate(array $requests): array
  {
    usort($requests, "self::sortByDate");
    return $requests;
  }

  private function sortByDate(RequestEntity $firstRequest, RequestEntity $secondRequest)
  {
    if ($firstRequest->getYear() === $secondRequest->getYear()) {
      if ($firstRequest->getMonth() === $secondRequest->getMonth()) {
        if ($firstRequest->getDay() === $secondRequest->getDay()) {
          if ($firstRequest->getId() === $secondRequest->getId()) {

            return 0;
          }
          return ($firstRequest->getId() < $secondRequest->getId()) ? -1 : 1;
        }
        return ($firstRequest->getDay() < $secondRequest->getDay()) ? -1 : 1;
      }
      return ($firstRequest->getMonth() < $secondRequest->getMonth()) ? -1 : 1;
    }
    return ($firstRequest->getYear() < $secondRequest->getYear()) ? -1 : 1;
  }

  private function sortByYear(RequestEntity $firstRequest, RequestEntity $secondRequest)
  {
    if ($firstRequest->getYear() === $secondRequest->getYear()) {
      return 0;
    }
    return ($firstRequest->getYear() < $secondRequest->getYear()) ? -1 : 1;
  }

  public function createOneRequestWithVehicle($vehicleId = null, $startId = 0)
  {
    $request = $this->createRequestsWithInputs(
      1,
      ["vehicleId" => $vehicleId],
      true,
      $startId
    );

    return $request;
  }
}
