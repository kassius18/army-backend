<?php

namespace app\models\domains\tab;

use JsonSerializable;

class TabEntity implements JsonSerializable
{
  private ?int $id;
  private ?string $name;
  private ?string $usage;
  private ?string $observations;

  public function __construct(?string $name, ?string $usage, ?string $observations, ?int $id)
  {
    $this->id = $id;
    $this->name = $name;
    $this->usage = $usage;
    $this->observations = $observations;
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

  public function jsonSerialize(): array
  {
    $arrayWithoutId = [
      "name" => $this->name ?: "",
      "usage" => $this->usage ?: "",
      "observations" => $this->observations ?: "",
      "id" => $this->id,
    ];

    return $arrayWithoutId;
  }
}
