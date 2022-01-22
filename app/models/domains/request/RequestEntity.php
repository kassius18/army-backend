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

    return $json;
  }
}
