<?php

namespace app\models\domains\request_entry;

use JsonSerializable;

class EntryEntity implements JsonSerializable
{
  private int $firstPartOfPhi;
  private int $secondPartOfPhi;
  private int $year;
  private string $nameNumber;
  private string $name;
  private string $mainPart;
  private int $amountOfOrder;
  private string $unitOfOrder;
  private int $reasonOfOrder;
  private int $priorityOfOrder;
  private string $observations;
  private ?int $id;

  public function __construct(
    int $firstPartOfPhi,
    int $secondPartOfPhi,
    int $year,
    string $nameNumber,
    string $name,
    string $mainPart,
    int $amountOfOrder,
    string $unitOfOrder,
    int $reasonOfOrder,
    int $priorityOfOrder,
    string $observations,
    int $id = null
  ) {
    $this->firstPartOfPhi = $firstPartOfPhi;
    $this->secondPartOfPhi = $secondPartOfPhi;
    $this->year = $year;
    $this->nameNumber = $nameNumber;
    $this->name = $name;
    $this->mainPart = $mainPart;
    $this->amountOfOrder = $amountOfOrder;
    $this->unitOfOrder = $unitOfOrder;
    $this->reasonOfOrder = $reasonOfOrder;
    $this->priorityOfOrder = $priorityOfOrder;
    $this->observations = $observations;
    $this->id = $id;
  }

  public function getFirstPhi(): int
  {
    return $this->firstPartOfPhi;
  }
  public function getSecondPhi(): int
  {
    return $this->secondPartOfPhi;
  }
  public function getYear(): int
  {
    return $this->year;
  }
  public function getNameNumber()
  {
    return $this->nameNumber;
  }
  public function getName()
  {
    return $this->name;
  }
  public function getMainPart()
  {
    return $this->mainPart;
  }
  public function getAmountOfOrder()
  {
    return $this->amountOfOrder;
  }
  public function getUnitOfOrder()
  {
    return $this->unitOfOrder;
  }
  public function getReasonOfOrder()
  {
    return $this->reasonOfOrder;
  }
  public function getPriorityOfOrder()
  {
    return $this->priorityOfOrder;
  }
  public function getObservations()
  {
    return $this->observations;
  }

  public function getId()
  {
    return $this->id;
  }

  public function jsonSerialize(): array
  {
    if (isset($this->id)) {
      return [
        'firstPartOfPhi' => $this->firstPartOfPhi,
        'secondPartOfPhi' => $this->secondPartOfPhi,
        'year' => $this->year,
        'nameNumber' => $this->nameNumber,
        'name' => $this->name,
        'mainPart' => $this->mainPart,
        'amountOfOrder' => $this->amountOfOrder,
        'unitOfOrder' => $this->unitOfOrder,
        'reasonOfOrder' => $this->reasonOfOrder,
        'priorityOfOrder' => $this->priorityOfOrder,
        'observations' => $this->observations,
        'id' => $this->id
      ];
    }

    return [
      'firstPartOfPhi' => $this->firstPartOfPhi,
      'secondPartOfPhi' => $this->secondPartOfPhi,
      'year' => $this->year,
      'nameNumber' => $this->nameNumber,
      'name' => $this->name,
      'mainPart' => $this->mainPart,
      'amountOfOrder' => $this->amountOfOrder,
      'unitOfOrder' => $this->unitOfOrder,
      'reasonOfOrder' => $this->reasonOfOrder,
      'priorityOfOrder' => $this->priorityOfOrder,
      'observations' => $this->observations,
    ];
  }
}