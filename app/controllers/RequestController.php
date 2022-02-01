<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\models\domains\request\RequestFactory;
use app\models\domains\request\RequestMapper;

class RequestController
{
  private Request $request;
  private RequestMapper $requestMapper;
  private Response $response;

  public function __construct(Request $request, RequestMapper $requestMapper, Response $response)
  {
    $this->request = $request;
    $this->requestMapper = $requestMapper;
    $this->response = $response;
  }

  public function handleGetRequest()
  {
    $getRequest = $this->request->getQueryParameters();
    if ($getRequest['findBy'] === 'date') {
      echo (json_encode($this->requestMapper->findAllByDateInterval(
        [$getRequest["startYear"]]
      )));
    } else if ($getRequest['findBy'] === 'phi') {
      echo (json_encode($this->requestMapper->findOneByPhiAndYear(
        $getRequest['firstPartOfPhi'],
        $getRequest['secondPartOfPhi'],
        $getRequest['year'],
      )));
    }
  }

  public function handlePostRequest()
  {
    $requestAsArray = $this->request->getRequestBody();
    $request = RequestFactory::createRequestFromUserInput($requestAsArray);
    if ($this->requestMapper->saveRequest($request)) {
      $request = $this->requestMapper->findOneByPhiAndYear(
        $request->getFirstPhi(),
        $request->getYear()
      );

      $this->response->setResponseBody(json_encode($request));
    } else {
      //handle not saving
    }

    $this->response->sendResponse();
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
