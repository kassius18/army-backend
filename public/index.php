<?php

declare(strict_types=1);

ini_set("display_errors", "On");
ini_set("display_startup_errors", "On");
ini_set("track_errors", "On");
ini_set("html_errors", "On");
error_reporting(E_ALL);

use app\core\Request;
use app\core\Response;
use app\core\Router;

define("ROOT_DIR", dirname(__DIR__));
define("APP_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "app");
define("MIGRATION_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "migrations");
define("SEEDER_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "seeds");
define("PUBLIC_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "public");

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(APP_DIR);
$dotenv->load();

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
  'dsn' => $_ENV["driver"] . ":host=" . $_ENV["host"] . ";dbname=" . $_ENV["dbname_dev"],
  'username' => $_ENV["username"],
  'password' => $_ENV["password"],
  'pdo_options' => [
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ],
  Request::class => DI\create()->constructor($_GET, $_POST, json_decode(file_get_contents('php://input'), true), $_SERVER, $_FILES, getallheaders()),
  PDO::class => DI\create()->constructor(DI\get('dsn'), DI\get('username'), DI\get('password'), DI\get('pdo_options'))
]);

$container = $builder->build();

$request = $container->get(Request::class);
$router = $container->get(Router::class);
$response = $container->get(Response::class);

$router->setGetRoute("/requests", "app\controllers\RequestController");
$router->setPostRoute("/requests", "app\controllers\RequestController");
$router->setDeleteRoute("/requests/:id", "app\controllers\RequestController");
$router->setPutRoute("/requests/:id", "app\controllers\RequestController");

$router->setPostRoute("/entries", "app\controllers\EntryController");
$router->setDeleteRoute("/entries/:id", "app\controllers\EntryController");
$router->setPutRoute("/entries/:id", "app\controllers\EntryController");

$router->setPostRoute("/parts", "app\controllers\PartController");
$router->setDeleteRoute("/parts/:id", "app\controllers\PartController");
$router->setPutRoute("/parts/:id", "app\controllers\PartController");

$router->setGetRoute("/tabs", "app\controllers\TabController");
$router->setGetRoute("/tabs/:id", "app\controllers\TabController");
$router->setDeleteRoute("/tabs/:id", "app\controllers\TabController");
$router->setPostRoute("/tabs", "app\controllers\TabController");
$router->setPutRoute("/tabs/:id", "app\controllers\TabController");

$router->setGetRoute("/vehicles", "app\controllers\VehicleController");
$router->setPostRoute("/vehicles", "app\controllers\VehicleController");
$router->setDeleteRoute("/vehicles/:id", "app\controllers\VehicleController");
$router->setPutRoute("/vehicles/:id", "app\controllers\VehicleController");

[$controllerName, $action] = $router->route($request->getRequestMethod(), $request->getRequestUri());

$controller = $container->get($controllerName);
$controller->$action();

/* die(); */
/* try { */
/*   $controller = $container->get($controllerName); */
/*   $controller->$action(); */
/* } catch (Exception $e) { */
/*   $response->setStatusCode(500); */
/*   $response->sendResponse(); */
/* } */
