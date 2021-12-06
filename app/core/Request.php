<?php

declare(strict_types=1);

namespace app\core;

class Request
{
  private $get;
  private $post;
  private $server;
  private $files;
  private $headers;

  public function __construct($get, $post, $server, $files, $headers)
  {
    $this->get = filter_var_array($get, FILTER_SANITIZE_SPECIAL_CHARS);
    $this->post = filter_var_array($post, FILTER_SANITIZE_SPECIAL_CHARS);
    $this->server = filter_var_array($server, FILTER_SANITIZE_SPECIAL_CHARS);
    $this->files = $files;
    $this->headers = $headers;
  }

  public function getRequestMethod()
  {
    return $this->server["REQUEST_METHOD"];
  }

  public function getRequestUri()
  {
    return $this->server["REQUEST_URI"];
  }

  public function getHeaders()
  {
    return $this->headers;
  }

  public function isMethodPOST()
  {
    if ($this->getRequestMethod() === "POST") {
      return true;
    }
    return false;
  }

  public function isMethodGET()
  {
    if ($this->getRequestMethod() === "GET") {
      return true;
    }
    return false;
  }

  public function getPostUserInput()
  {
    return $this->post;
  }

  public function getGetUserInput()
  {
    return $this->get;
  }

  public function getServerInput()
  {
    return $this->server;
  }

  public function getFilesInput()
  {
    return $this->files;
  }

  public function splitRequestUri()
  {
    $requestUri = $this->getRequestUri();
    $splitUri = explode("/", $requestUri);
    array_shift($splitUri);

    return $splitUri;
  }
}
