<?php

namespace app\controllers;

use app\core\Request;
use app\models\domains\request\RequestFactory;
use app\models\domains\request\RequestMapper;
use app\models\domains\request_entry\EntryFactory;
use app\models\domains\request_entry\EntryMapper;

class RequestController
{
  private Request $request;
  private RequestMapper $requestMapper;
  private EntryMapper $entryMapper;
  public function __construct(Request $request, RequestMapper $requestMapper, EntryMapper $entryMapper)
  {
    $this->request = $request;
    $this->requestMapper = $requestMapper;
    $this->entryMapper = $entryMapper;
  }

  public function handleGetRequest()
  {
    $getRequest = $this->request->getGetUserInput();
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
    $requestAsArray = $this->request->getPostUserInput();
    $request = RequestFactory::createRequestFromUserInput($requestAsArray);
    $this->requestMapper->saveRequest($request, $this->entryMapper);
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
