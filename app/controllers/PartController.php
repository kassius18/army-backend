<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\models\domains\part\PartFactory;
use app\models\domains\part\PartMapper;

class PartController
{
  private Request $request;
  private PartMapper $partMapper;
  private Response $response;

  public function __construct(Request $request, PartMapper $partMapper, Response $response)
  {
    $this->request = $request;
    $this->partMapper = $partMapper;
    $this->response = $response;
  }

  public function handlePostRequest()
  {
    $partAsArray = $this->request->getRequestBody();
    $part = PartFactory::createPartFromUserInput($partAsArray);
    $part = $this->partMapper->savePartToEntry($part, $partAsArray["entryId"]);
    if ($part) {
      $this->response->setResponseBody(json_encode($part));
      $this->response->setStatusCode(201);
    } else {
      $this->response->setStatusCode(409);
    }

    $this->response->sendResponse();
  }

  public function handlePutRequest()
  {
    $partAsArray = $this->request->getRequestBody();
    $splitUri = $this->request->splitRequestUri();
    $partId = $splitUri[1];
    $part = PartFactory::createPartFromUserInput($partAsArray);
    $part = $this->partMapper->updatePartById($part, $partId);
    if ($part) {
      $this->response->setResponseBody(json_encode($part));
      $this->response->setStatusCode(200);
    } else {
      $this->response->setStatusCode(404);
    }

    $this->response->sendResponse();
  }

  public function handleDeleteRequest()
  {
    $splitUri = $this->request->splitRequestUri();
    $partId = $splitUri[1];
    if ($this->partMapper->deletePartById($partId)) {
      $this->response->setStatusCode(204);
    } else {
      $this->response->setStatusCode(404);
    }

    $this->response->sendResponse();
  }
}
