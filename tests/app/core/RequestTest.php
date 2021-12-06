<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use app\core\Request;

class RequestTest extends TestCase
{
  public function testGetRequestMethodReturnsCorrectMethod()
  {
    $router = new Request([], [], ["REQUEST_METHOD" => "GET"], [], []);

    $this->assertEquals("GET", $router->getRequestMethod());
  }

  public function testMethodsThatCheckWhatTypeOfHttpRequestIsBeingMade()
  {
    $router = new Request([], [], ["REQUEST_METHOD" => "POST"], [], []);

    $this->assertTrue($router->isMethodPOST());
    $this->assertFalse($router->isMethodGET());
  }

  public function testRouteIsSplitCorrectly()
  {
    $router = new Request([], [], ["REQUEST_URI" => "/user/test/something"], [], []);
    $splitUri = $router->splitRequestUri();

    $this->assertEquals($splitUri[0], "user");
    $this->assertEquals($splitUri[1], "test");
    $this->assertEquals($splitUri[2], "something");
  }

  public function testSanitazation()
  {
    $router = new Request(
      ["><\"'"],
      ["><\"'"],
      ["><\"';:/"],
      [],
      []
    );

    $encodedValues = [
      ">" => "&#62;",
      "<" => "&#60;",
      "\"" => "&#34;",
      "'" => "&#39;"
    ];

    $getRequestData = $router->getGetUserInput();
    $getPostData = $router->getPostUserInput();
    $getServerData = $router->getServerInput();

    $this->assertEquals(
      $getRequestData,
      [
        $encodedValues[">"] .
          $encodedValues["<"] .
          $encodedValues["\""] .
          $encodedValues["'"]
      ]
    );

    $this->assertEquals(
      $getPostData,
      [
        $encodedValues[">"] .
          $encodedValues["<"] .
          $encodedValues["\""] .
          $encodedValues["'"]
      ]
    );

    $this->assertEquals(
      $getServerData,
      [
        $encodedValues[">"] .
          $encodedValues["<"] .
          $encodedValues["\""] .
          $encodedValues["'"] . ";:/"
      ]
    );
  }

  public function testHeadersAreRetreivedFromRequest()
  {
    $router = new Request([], [], ["REQUEST_METHOD" => "GET"], [], ["Header" => "someHeader"]);

    $this->assertEquals(["Header" => "someHeader"], $router->getHeaders("Header"));
  }
}
