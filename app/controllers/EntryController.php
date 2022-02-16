<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\models\domains\request_entry\EntryFactory;
use app\models\domains\request_entry\EntryMapper;

class EntryController
{
  private Request $request;
  private EntryMapper $entryMapper;
  private Response $response;

  public function __construct(Request $request, EntryMapper $entryMapper, Response $response)
  {
    $this->request = $request;
    $this->entryMapper = $entryMapper;
    $this->response = $response;
  }

  public function handlePostRequest()
  {
    $entryAsArray = $this->request->getRequestBody();
    $entry = EntryFactory::createEntryFromUserInput($entryAsArray);
    $requestPrimaryKeys = [
      "firstPartOfPhi" => $entryAsArray["firstPartOfPhi"], "year" => $entryAsArray["year"]
    ];
    $entry = $this->entryMapper->saveEntryToRequest($entry, $requestPrimaryKeys);
    if ($entry) {
      $this->response->setResponseBody(json_encode($entry));
      $this->response->setStatusCode(201);
    } else {
      $this->response->setStatusCode(409);
    }

    $this->response->sendResponse();
  }

  public function handlePutRequest()
  {
    $entryAsArray = $this->request->getRequestBody();
    $splitUri = $this->request->splitRequestUri();
    $entryId = $splitUri[1];
    $entry = EntryFactory::createEntryFromUserInput($entryAsArray);
    $entry = $this->entryMapper->updateEntryById($entry, $entryId);
    if ($entry) {
      $this->response->setResponseBody(json_encode($entry));
      $this->response->setStatusCode(200);
    } else {
      $this->response->setStatusCode(404);
    }

    $this->response->sendResponse();
  }

  public function handleDeleteRequest()
  {
    $splitUri = $this->request->splitRequestUri();
    $entryId = $splitUri[1];
    if ($this->entryMapper->deleteEntryById($entryId)) {
      $this->response->setStatusCode(204);
    } else {
      $this->response->setStatusCode(404);
    }

    $this->response->sendResponse();
  }
}
