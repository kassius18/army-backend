<?php

declare(strict_types=1);

use app\core\Request;
use app\core\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
  private $request;
  private $router;

  protected function setUp(): void
  {
    $this->request = $this->createMock(Request::class);
    $this->router = new Router($this->request);
  }

  public function testSettingAGetRouteWorks()
  {
    $this->assertEmpty($this->router->getRoutes()["GET"]);
    $this->router->setGetRoute("/register", "SomeController");
    $this->assertNotEmpty($this->router->getRoutes()["GET"]);
    $this->assertEquals(["SomeController", "handleGetRequest"], $this->router->getRoutes()["GET"]["/register"]);
  }

  public function testSettingAPostRouteWorks()
  {
    $this->assertEmpty($this->router->getRoutes()["POST"]);
    $this->router->setPostRoute("/register", "SomeController", "handlePostRequest");
    $this->assertNotEmpty($this->router->getRoutes()["POST"]);
    $this->assertEquals(["SomeController", "handlePostRequest"], $this->router->getRoutes()["POST"]["/register"]);
  }

  public function testStaticGetRoute()
  {
    $this->router->setGetRoute("/dashboard", "app\controllers\dashboardController");
    [$controllerName, $action] = $this->router->route("GET", "/dashboard");
    $this->assertEquals($controllerName, "app\controllers\dashboardController");
    $this->assertEquals($action, "handleGetRequest");
  }

  public function testRouteThatDoesntExistGivesNotFound()
  {
    [$controllerName, $action] = $this->router->route("GET", "/asdf");
    $this->assertEquals($controllerName, "app\controllers\NotFoundController");
    $this->assertEquals($action, "handleAllRequests");
  }

  public function testStaticPostRoute()
  {
    $this->router->setPostRoute("/login", "app\controllers\LoginController");
    [$controllerName, $action] = $this->router->route("POST", "/login");
    $this->assertEquals($controllerName, "app\controllers\LoginController");
    $this->assertEquals($action, "handlePostRequest");
  }

  public function testDynamicRouteWithMultipleDynamiaParts()
  {
    $this->router->setGetRoute("/static1/:username/static2/:number", "app\controllers\UserController");
    [$controllerName, $action] = $this->router->route("GET", "/static1/username/static2/123");
    $this->assertEquals($controllerName, "app\controllers\UserController");
    $this->assertEquals($action, "handleGetRequest");
  }

  public function testSimilarRoutesDontMatch()
  {
    $this->router->setGetRoute("/user/:username", "app\controllers\UserController");
    [$controllerName, $action] = $this->router->route("GET", "/user");
    $this->assertEquals($controllerName, "app\controllers\NotFoundController");
    $this->assertEquals($action, "handleAllRequests");
  }

  public function testDynamicRoute()
  {
    $this->router->setGetRoute("/users/:username/settings", "app\controllers\UserController");

    [$controllerName, $action] = $this->router->route("GET", "/users/username/settings");
    $this->assertEquals($controllerName, "app\controllers\UserController");
    $this->assertEquals($action, "handleGetRequest");
  }
}
