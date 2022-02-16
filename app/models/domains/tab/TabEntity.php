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
  private ?array $parts = null;

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

  public function getParts()
  {
    return $this->parts;
  }

  public function setParts(array $parts): void
  {
    $this->parts = $parts;
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

  public function jsonSerialize(): array
  {
    $json = [
      "name" => $this->name ?: "",
      "usage" => $this->usage ?: "",
      "observations" => $this->observations ?: "",
      "startingTotal" => $this->startingTotal ?: 0,
      "id" => $this->id,
      "parts" => $this->parts ?: [],
    ];

    return $json;
  }
}
