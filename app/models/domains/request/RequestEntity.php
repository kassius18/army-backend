<?php

namespace app\models\domains\request;

use JsonSerializable;

class RequestEntity implements JsonSerializable
{
  private ?int $firstPartOfPhi;
  private ?int $secondPartOfPhi;
  private ?int $year;
  private ?int $month;
  private ?int $day;
  private ?array $entries = null;
  private ?int $id;

  public function __construct(
    ?int $firstPartOfPhi,
    ?int $secondPartOfPhi,
    ?int $year,
    ?int $month,
    ?int $day,
    ?int $id = null
  ) {
    $this->firstPartOfPhi = $firstPartOfPhi;
    $this->secondPartOfPhi = $secondPartOfPhi;
    $this->year = $year;
    $this->month = $month;
    $this->day = $day;
    $this->id = $id;
  }

  public function getFirstPhi(): ?int
  {
    return $this->firstPartOfPhi;
  }
  public function getSecondPhi(): ?int
  {
    return $this->secondPartOfPhi;
  }
  public function getYear(): ?int
  {
    return $this->year;
  }
  public function getMonth(): ?int
  {
    return $this->month;
  }

  public function getDay(): ?int
  {
    return $this->day;
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEntries(): ?array
  {
    return $this->entries;
  }

  public function setEntries(array $entries): void
  {
    $this->entries = $entries;
  }

  public function addEntries(array $entries): void
  {
    if (!$this->entries) {
      $this->entries = [];
    }
    array_push($this->entries, ...$entries);
    usort($this->entries, function ($firstEntry, $secondEntry) {
      if ($firstEntry->getId() === $secondEntry->getId()) {
        return 0;
      }
      return ($firstEntry->getId() < $secondEntry->getId()) ? -1 : 1;
    });
  }

  public function jsonSerialize(): array
  {
    $json = [
      'firstPartOfPhi' => $this->firstPartOfPhi ?: "",
      'secondPartOfPhi' => $this->secondPartOfPhi ?: "",
      'year' => $this->year ?: "",
      'month' => $this->month ?: "",
      'day' => $this->day ?: "",
    ];

    if (isset($this->id)) {
      $json["id"] = $this->id;
    }

    if ($this->entries) {
      $json["entries"] = json_encode($this->entries);
    }

    return $json;
  }
}
