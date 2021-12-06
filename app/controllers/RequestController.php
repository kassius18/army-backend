<?php

namespace app\controllers;

use app\models\domains\request\RequestMapper;

class RequestController
{
  private $httpRequest;

  public function __construct()
  {
  }

  public function handleGetRequest()
  {
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo "this works";
  }

  public function handlePostRequest()
  {
    return null;
  }

  public function handlePatchRequest()
  {
    return null;
  }
  public function handleDeleteRequest()
  {
    return null;
  }
}
