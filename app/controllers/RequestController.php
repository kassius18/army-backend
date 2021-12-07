<?php

namespace app\controllers;

use app\core\Request;
use app\models\domains\request\RequestFactory;
use app\models\domains\request\RequestMapper;

class RequestController
{
  private Request $request;
  private RequestMapper $requestMapper;

  public function __construct(Request $request, RequestMapper $requestMapper)
  {
    $this->request = $request;
    $this->requestMapper = $requestMapper;
  }

  public function handleGetRequest()
  {
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo "this works";
  }

  public function handlePostRequest()
  {
    echo "Post still works";
    $userPostInput = $this->request->getPostUserInput();
    $getRequestsToBeSavedFromUserInput = $userPostInput["requests"];
    $arrayOfRequests = [];
    foreach ($getRequestsToBeSavedFromUserInput as $key => $requestAsArray) {
      $arrayOfRequests[] = RequestFactory::createRequestFromUserInput($requestAsArray);
    }
    $this->requestMapper->saveManyRecords($arrayOfRequests);
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
