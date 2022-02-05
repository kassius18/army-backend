<?php

namespace app\models\domains\tab;

use JsonSerializable;

class TabEntity implements JsonSerializable
{
  private ?int $id;
  private ?string $name;
  private ?string $usage;
  private ?string $observations;
  private ?int $startingTotal;

  public function __construct(
    ?string $name,
    ?string $usage,
    ?string $observations,
    ?int $startingTotal,
    ?int $id
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->usage = $usage;
    $this->observations = $observations;
    $this->startingTotal = $startingTotal;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getUsage()
  {
    return $this->usage;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getObservations()
  {
    return $this->observations;
  }

  public function getStartingTotal()
  {
    return $this->startingTotal;
  }

  public function jsonSerialize(): array
  {
    return [
      "name" => $this->name ?: "",
      "usage" => $this->usage ?: "",
      "observations" => $this->observations ?: "",
      "startingTotal" => $this->startingTotal ?: 0,
      "id" => $this->id,
    ];
  }
}
