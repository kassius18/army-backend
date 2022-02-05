<?php

namespace app\models\domains\part;

use JsonSerializable;

class PartEntity implements JsonSerializable
{

  private ?string $dateRecieved;
  private ?string $pieNumber;
  private ?int $amountRecieved;
  private ?string $tabUsed;
  private ?string $dateUsed;
  private ?int $amountUsed;
  private ?int $id;

  public function __construct(
    ?string $dateRecieved,
    ?string $pieNumber,
    ?int $amountRecieved,
    ?string $tabUsed,
    ?string $dateUsed,
    ?int $amountUsed,
    ?int $id = null
  ) {
    $this->dateRecieved = $dateRecieved;
    $this->pieNumber = $pieNumber;
    $this->amountRecieved = $amountRecieved;
    $this->tabUsed = $tabUsed;
    $this->dateUsed = $dateUsed;
    $this->amountUsed = $amountUsed;
    $this->id = $id;
  }

  public function getEntryId()
  {
    return $this->entryId;
  }
  public function getDateRecieved()
  {
    return $this->dateRecieved;
  }
  public function getPieNumber()
  {
    return $this->pieNumber;
  }
  public function getAmountRecieved()
  {
    return $this->amountRecieved;
  }
  public function getTabUsed()
  {
    return $this->tabUsed;
  }
  public function getDateUsed()
  {
    return $this->dateUsed;
  }
  public function getAmountUsed()
  {
    return $this->amountUsed;
  }
  public function getId()
  {
    return $this->id;
  }

  public function jsonSerialize(): array
  {
    $json = [
      "dateRecieved" => $this->dateRecieved ?: "",
      "pieNumber" => $this->pieNumber ?: "",
      "amountRecieved" => $this->amountRecieved ?: "",
      "tabUsed" => $this->tabUsed ?: "",
      "dateUsed" => $this->dateUsed ?: "",
      "amountUsed" => $this->amountUsed ?: "",
    ];
    if (isset($this->id)) {
      $json["id"] = $this->id;
    }
    return $json;
  }
}
