<?php

declare(strict_types=1);

namespace app\core;

class Response
{
  private array $headers = [];
  private string $response = "";

  public function setHeaders($headers): void
  {
    $this->headers = $headers;
  }

  public function getHeaders(): array
  {
    return $this->headers;
  }

  public function setStatusCode(int $statusCode): void
  {
    http_response_code($statusCode);
  }

  public function setResponseBody(?string $response): void
  {
    $this->response = $response ?: "";
  }

  public function getResponseBody(): string
  {
    return $this->response;
  }

  public function sendResponse()
  {
    $this->sendHeaders();
    $this->sendBody();
  }

  private function sendHeaders(): void
  {
    foreach ($this->headers as $header) {
      header($header);
    }
  }

  private function sendBody(): void
  {
    echo $this->response;
  }
}
