<?php

declare(strict_types=1);

use app\core\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
  private Response $response;

  protected function setUp(): void
  {
    $this->response = new Response();
  }

  public function testSettingHeaders()
  {
    $headers = ["header" => "headerValue"];
    $this->response->setHeaders($headers);
    $actual = $this->response->getHeaders($headers);
    $this->assertEquals($headers, $actual);
  }

  public function testSettingBody()
  {
    $responseBody = json_encode(["jsonKey" => "jsonValue"]);
    $this->response->setResponseBody($responseBody);
    $actual = $this->response->getResponseBody();
    $this->assertEquals($responseBody, $actual);
  }

  public function testNullResponeBodies()
  {
    $responseBody = null;
    $this->response->setResponseBody($responseBody);
    $actual = $this->response->getResponseBody();
    $this->assertEquals("", $actual);
  }

  public function testSettingHttpStatusCode()
  {
    $statusCode = 404;
    $this->response->setStatusCode($statusCode);
    $actualStatusCode = http_response_code();
    $this->assertSame($statusCode, $actualStatusCode);
  }

  public function testSendingResponse()
  {
    $headers = ["Location: http://test.com"];
    $responseBody = json_encode(["jsonKey" => "jsonValue"]);
    $this->response->setHeaders($headers);
    $this->response->setResponseBody($responseBody);
    $this->response->sendResponse();
    $actualHeaders = xdebug_get_headers();
    $this->assertEquals($headers, $actualHeaders);
    $this->expectOutputString($responseBody);
  }
}
