<?php

namespace app\controllers;

use app\core\Response;
use app\core\Request;
use app\models\domains\tab\TabFactory;
use app\models\domains\tab\TabMapper;
use PDOException;

class TabController
{
  private Request $request;
  private TabMapper $tabMapper;
  private Response $response;

  public function __construct(Request $request, TabMapper $tabMapper, Response $response)
  {
    $this->request = $request;
    $this->tabMapper = $tabMapper;
    $this->response = $response;
  }

  public function handleGetRequest()
  {
    $requestUri = $this->request->splitRequestUri();
    if (isset($requestUri[1])) {
      $tabId = $requestUri[1];
      try {
        $parts = $this->tabMapper->findAllPartsThatBelongToTab($tabId);
        $this->response->setStatusCode(200);
        $this->response->setResponseBody(json_encode(
          [$parts]
        ));
      } catch (PDOException $e) {
        $this->response->setStatusCode(500);
      }
    } else {
      $getRequest = $this->request->getQueryParameters();
      if (isset($getRequest["showNonEmpty"]) && $getRequest["showNonEmpty"] === "true") {
        try {
          $idOfNonEmptyTabs = $this->tabMapper->getIdsOfNonEmptyTabs();
          $arrayOfIdAndParts = [];
          foreach ($idOfNonEmptyTabs as $id) {
            $arrayOfIdAndParts[$id] = $this->tabMapper->findAllPartsThatBelongToTab($id);
          }
          $this->response->setResponseBody(json_encode($arrayOfIdAndParts));
          $this->response->setStatusCode(200);
        } catch (PDOException $e) {
        }
      } else {
        try {
          $allTabs = $this->tabMapper->getAllTabs();
          $this->response->setResponseBody(json_encode($allTabs));
          $this->response->setStatusCode(200);
        } catch (PDOException $e) {
          $this->response->setStatusCode(500);
        }
      }
    }
    $this->response->sendResponse();
  }

  public function handlePostRequest()
  {
    $tabAsArray = $this->request->getRequestBody();
    $tab = TabFactory::createTabFromInput($tabAsArray);
    try {
      $tab = $this->tabMapper->saveTab($tab);
      $this->response->setResponseBody(json_encode($tab));
      $this->response->setStatusCode(201);
    } catch (PDOException $e) {
      if ($e->getCode() === '23000') {
        $this->response->setStatusCode(409);
      } else {
        $this->response->setStatusCode(500);
      }
    }
    $this->response->sendResponse();
  }

  public function handlePutRequest()
  {
    $tabAsArray = $this->request->getRequestBody();
    $tab = TabFactory::createTabFromInput($tabAsArray);
    $tabId = json_decode($this->request->splitRequestUri()[1]);
    if (is_int($tabId)) {
      try {
        $tab = $this->tabMapper->updateTab($tab, $tabId);
        $this->response->setStatusCode(201);
        $this->response->setResponseBody(json_encode($tab));
      } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
          $this->response->setStatusCode(409);
        } else {
        }
        $this->response->setStatusCode(500);
      }
    }
    $this->response->sendResponse();
  }

  public function handleDeleteRequest()
  {
    $tabId = json_decode($this->request->splitRequestUri()[1]);
    if (is_int($tabId)) {
      try {
        if ($this->tabMapper->deleteTab($tabId)) {
          $this->response->setStatusCode(204);
        };
      } catch (PDOException $e) {
        $this->response->setStatusCode(500);
      }
    }
    $this->response->sendResponse();
  }
}
