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
    if ($getRequest["findBy"] === "date") {
      $startYear[0] = $getRequest["startYear"];

      if (isset($getRequest["startMonth"]) && $getRequest["startMonth"] !== null) {
        $startYear[1] = $getRequest["startMonth"];
      }

      if (isset($getRequest["startDay"]) && $getRequest["startDay"] !== null) {
        $startYear[2] = $getRequest["startDay"];
      }

      if (isset($getRequest["endYear"]) && $getRequest["endYear"] !== null) {
        $endYear[0] = $getRequest["endYear"];
      }

      if (isset($getRequest["endMonth"]) && $getRequest["endMonth"] !== null) {
        $endYear[1] = isset($getRequest["endMonth"]);
      }

      if (isset($getRequest["endDay"]) && $getRequest["endDay"] !== null) {
        $endYear[2] = isset($getRequest["endDay"]);
      }

      if (!isset($endYear)) {
        $endYear = $startYear;
      }

      $this->response->setResponseBody(
        json_encode($this->requestMapper->findAllByDateInterval($startYear, $endYear))
      );
    } else if ($getRequest["findBy"] === "phi") {
      $this->response->setResponseBody(
        json_encode($this->requestMapper->findManyByPhi($getRequest["phi"]))
      );
    } else if ($getRequest["findBy"] = "phi-year") {
      $this->response->setResponseBody(
        json_encode($this->requestMapper->findOneByPhiAndYear($getRequest["phi"], $getRequest["year"]))
      );
    }
    $this->response->setStatusCode(200);
    $this->response->sendResponse();
  }

  public function handlePostRequest()
  {
    $requestAsArray = $this->request->getRequestBody();
    $request = RequestFactory::createRequestFromUserInput($requestAsArray);
    $request = $this->requestMapper->saveRequest($request);
    if ($request) {
      $this->response->setResponseBody(json_encode($request));
      $this->response->setStatusCode(201);
    } else {
      $this->response->setStatusCode(409);
    }

    $this->response->sendResponse();
  }

  public function handlePutRequest()
  {
    $requestAsArray = $this->request->getRequestBody();
    $splitUri = $this->request->splitRequestUri();
    $requestId = $splitUri[1];
    $request = RequestFactory::createRequestFromUserInput($requestAsArray);
    $request = $this->requestMapper->updateRequest($request, $requestId);
    if ($request) {
      $this->response->setResponseBody(json_encode($request));
      $this->response->setStatusCode(200);
    } else {
      $this->response->setStatusCode(404);
    }

    $this->response->sendResponse();
  }

  public function handleDeleteRequest()
  {
    $splitUri = $this->request->splitRequestUri();
    $requestId = $splitUri[1];
    if ($this->requestMapper->deleteRequestById($requestId)) {
      $this->response->setStatusCode(204);
    } else {
      $this->response->setStatusCode(404);
    }
    $this->response->sendResponse();
  }
}
