<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\models\domains\request\RequestFactory;
use app\models\domains\request\RequestMapper;
use app\models\domains\request_entry\EntryFactory;
use app\models\domains\request_entry\EntryMapper;

class RequestController
{
  private Request $request;
  private RequestMapper $requestMapper;
  private EntryMapper $entryMapper;
  private Response $response;

  public function __construct(Request $request, RequestMapper $requestMapper, EntryMapper $entryMapper, Response $response)
  {
    $this->request = $request;
    $this->requestMapper = $requestMapper;
    $this->entryMapper = $entryMapper;
    $this->response = $response;
  }

  public function handleGetRequest()
  {
    $getRequest = $this->request->getQueryParameters();
    if ($getRequest["findBy"] === "date") {
      $startYear[0] = (int)$getRequest["startYear"];

      if (isset($getRequest["startMonth"]) && $getRequest["startMonth"] !== null) {
        $startYear[1] = (int)$getRequest["startMonth"];
      }

      if (isset($getRequest["startDay"]) && $getRequest["startDay"] !== null) {
        $startYear[2] = (int)$getRequest["startDay"];
      }

      if (isset($getRequest["endYear"]) && $getRequest["endYear"] !== null) {
        $endYear[0] = (int)$getRequest["endYear"];
      }

      if (isset($getRequest["endMonth"]) && $getRequest["endMonth"] !== null) {
        $endYear[1] = (int)$getRequest["endMonth"];
      }

      if (isset($getRequest["endDay"]) && $getRequest["endDay"] !== null) {
        $endYear[2] = (int)$getRequest["endDay"];
      }

      if (!isset($endYear)) {
        $endYear = $startYear;
      }

      if ($startYear[0] === 0) {
        $endYear = [];
        $endYear = [9999];
      }
      $requests = $this->requestMapper->findAllByDateInterval($startYear, $endYear);
      $this->response->setResponseBody(
        json_encode($requests)
      );
    } else if ($getRequest["findBy"] === "phi") {
      $this->response->setResponseBody(
        json_encode($this->requestMapper->findManyByPhi($getRequest["phi"]))
      );
    } else if ($getRequest["findBy"] === "phi-year") {
      $this->response->setResponseBody(
        json_encode($this->requestMapper->findOneByPhiAndYear($getRequest["phi"], $getRequest["year"]))
      );
    } else if ($getRequest["findBy"] === "vehicle") {
      $this->response->setResponseBody(
        json_encode($this->requestMapper->findAllByVehicle($getRequest["vehicleId"]))
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
    if (isset($requestAsArray["copy"]) && isset($requestAsArray["entries"])) {
      $entries = EntryFactory::createManyEntriesFromUserInput($requestAsArray["entries"]);
      foreach ($entries as $entry) {
        $this->entryMapper->saveEntryToRequest($entry, [
          "firstPartOfPhi" => $request->getFirstPhi(),
          "year" => $request->getYear(),
        ]);
      }
    }
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
