<?php

declare(strict_types=1);

namespace app\core;

class Router
{
  private array $routes = ["GET" => [], "POST" => []];
  private string $controllerName;
  private string $action;

  public function setGetRoute(string $Uri, string $controllerName): void
  {
    $action = "handleGetRequest";
    $this->routes["GET"][$Uri] = [$controllerName, $action];
  }

  public function setPostRoute(string $Uri, string $controllerName): void
  {
    $action = "handlePostRequest";
    $this->routes["POST"][$Uri] = [$controllerName, $action];
  }

  public function getRoutes()
  {
    return $this->routes;
  }

  private function isDynamic($route)
  {
    if (strpos($route, ":")) {
      return true;
    }
    return false;
  }

  private function splitPathToSections(string $path)
  {
    $path = explode("/", $path);
    array_shift($path);
    return $path;
  }

  private function matchUriWithDynamicRoutes($routes, $requestUri)
  {
    $matchingRoutes = [];

    foreach (array_keys($routes) as $route) {
      if ($this->isDynamic($route)) {
        $routeSplit = $this->splitPathToSections($route);
        $requestSplit = $this->splitPathToSections($requestUri);

        if (count($requestSplit) !== count($routeSplit)) {
          continue;
        }

        $matches = true;

        foreach ($routeSplit as $key => $routeSection) {
          if (strpos($routeSection, ":") === false) {
            if ($routeSection !== $requestSplit[$key]) {
              $matches = false;
              break;
            }
          }
        }

        if ($matches) {
          $matchingRoutes[] = $route;
        }
      }
    }
    return $matchingRoutes;
  }

  private function isStaticRoute($requestMethod, $requestUri)
  {
    return (!empty($this->routes[$requestMethod][$requestUri]));
  }

  public function route($requestMethod, $requestUri): array
  {
    $routes = $this->routes[$requestMethod];

    if ($this->isStaticRoute($requestMethod, $requestUri)) {
      $this->controllerName = $routes[$requestUri][0];
      $this->action = $routes[$requestUri][1] ?? $this->getActionName($requestMethod);
    } else {
      $matchingRoutes = $this->matchUriWithDynamicRoutes($routes, $requestUri);

      if (count($matchingRoutes) === 1) {
        $matchingRoute  = $matchingRoutes[0];

        $this->controllerName = $routes[$matchingRoute][0];
        $this->action = $routes[$matchingRoute][1] ?? $this->getActionName($requestMethod);
      }
    }

    if (empty($this->controllerName)) {
      $this->controllerName = "app\controllers\NotFoundController";
      $this->action = "handleAllRequests";
    }

    return [$this->controllerName, $this->action];
  }

  private function getActionName($requestMethod)
  {
    if ($requestMethod === "POST") {
      return "handlePostRequest";
    }
    return "index";
  }
}
