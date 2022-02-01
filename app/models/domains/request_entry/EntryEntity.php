<?php

namespace app\models\domains\request_entry;

use JsonSerializable;

class EntryEntity implements JsonSerializable
{
  private ?string $nameNumber;
  private ?string $name;
  private ?string $mainPart;
  private ?int $amountOfOrder;
  private ?string $unitOfOrder;
  private ?int $reasonOfOrder;
  private ?int $priorityOfOrder;
  private ?string $observations;
  private ?int $consumableId;
  private ?array $parts = null;
  private ?int $id;

  public function __construct(
    ?string $nameNumber,
    ?string $name,
    ?string $mainPart,
    ?int $amountOfOrder,
    ?string $unitOfOrder,
    ?int $reasonOfOrder,
    ?int $priorityOfOrder,
    ?string $observations,
    ?int $consumableId,
    ?int $id = null
  ) {
    $this->nameNumber = $nameNumber;
    $this->name = $name;
    $this->mainPart = $mainPart;
    $this->amountOfOrder = $amountOfOrder;
    $this->unitOfOrder = $unitOfOrder;
    $this->reasonOfOrder = $reasonOfOrder;
    $this->priorityOfOrder = $priorityOfOrder;
    $this->observations = $observations;
    $this->consumableId = $consumableId;
    $this->id = $id;
  }

  public function getFirstPhi(): int
  {
    return $this->firstPartOfPhi;
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

  public function getConsumableId()
  {
    return $this->consumableId;
  }

  public function getId()
  {
    return $this->id;
  }

  public function addParts(array $parts): void
  {
    if (!$this->parts) {
      $this->parts = [];
    }
    array_push($this->parts, ...$parts);
    usort($this->parts, function ($firstPart, $secondPart) {
      if ($firstPart->getId() === $secondPart->getId()) {
        return 0;
      }
      return ($firstPart->getId() < $secondPart->getId()) ? -1 : 1;
    });
  }

  public function setParts(array $parts)
  {
    $this->parts = $parts;
  }

  public function getParts(): ?array
  {
    return $this->parts;
  }

  public function jsonSerialize(): array
  {

    $json = [
      "nameNumber" => $this->nameNumber ?: "",
      "name" => $this->name ?: "",
      "mainPart" => $this->mainPart ?: "",
      "amountOfOrder" => $this->amountOfOrder ?: "",
      "unitOfOrder" => $this->unitOfOrder ?: "",
      "reasonOfOrder" => $this->reasonOfOrder ?: "",
      "priorityOfOrder" => $this->priorityOfOrder ?: "",
      "observations" => $this->observations ?: "",
      "consumableId" => $this->consumableId ?: "No"
    ];

    if (isset($this->id)) {
      $json["id"] = $this->id;
    }

    if ($this->parts) {
      $json["parts"] = json_encode($this->parts);
    }

    return $json;
  }
}
