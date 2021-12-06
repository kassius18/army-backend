<?php

declare(strict_types=1);

use app\core\Dispatcher;
use app\core\Request;
use app\core\Router;

define("ROOT_DIR", dirname(__DIR__));
define("APP_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "app");
define("MIGRATION_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "migrations");
define("SEEDER_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "seeds");
define("SNIPPET_DIR", APP_DIR . DIRECTORY_SEPARATOR . "snippets");
define("DASHBOARD_SNIPPET_DIR", SNIPPET_DIR . DIRECTORY_SEPARATOR . "dashboard");
define("LOGIN_SNIPPET_DIR", SNIPPET_DIR . DIRECTORY_SEPARATOR . "login");
define("REGISTER_SNIPPET_DIR", SNIPPET_DIR . DIRECTORY_SEPARATOR . "register");
define("USER_SNIPPET_DIR", SNIPPET_DIR . DIRECTORY_SEPARATOR . "user");
define("LAYOUT_DIR", SNIPPET_DIR . DIRECTORY_SEPARATOR . "layouts");
define("VIEW_DIR", APP_DIR . DIRECTORY_SEPARATOR . "views");
define("UTILITIES_DIR", APP_DIR . DIRECTORY_SEPARATOR . "utilities");
define("RULES_DIR", UTILITIES_DIR . DIRECTORY_SEPARATOR . "rules");
define("PUBLIC_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "public");
define("CSS_DIR", PUBLIC_DIR . DIRECTORY_SEPARATOR . "css");
define("JS_DIR", PUBLIC_DIR . DIRECTORY_SEPARATOR . "js");

/* require_once(APP_DIR . "/handlers/errorHandler.php"); */
/* require_once(APP_DIR . "/handlers/exceptionHandler.php"); */
/* require_once(APP_DIR . "/handlers/shutdownHandler.php"); */

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(APP_DIR);
$dotenv->load();

//Container
$builder = new DI\ContainerBuilder();

$builder->addDefinitions([
  'dsn' => $_ENV["driver"] . $_ENV["host"] . $_ENV["dbname"],
  'username' => $_ENV["username"],
  'password' => $_ENV["password"],
  'pdo_options' => [
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ],
  Request::class => DI\create()->constructor($_GET, $_POST, $_SERVER, $_FILES, getallheaders()),
  PDO::class => DI\create()->constructor(DI\get('dsn'), DI\get('username'), DI\get('password'), DI\get('pdo_options'))
]);

$container = $builder->build();

$request = $container->get(Request::class);
$router = $container->get(Router::class);

/* $router->setGetRoute("/", ["app\controllers\HomeController"]); */

/* $router->setGetRoute("/register", ["app\controllers\RegistrationController"]); */
/* $router->setPostRoute("/register", ["app\controllers\RegistrationController"]); */

$router->setGetRoute("/requests", "app\controllers\RequestController");
/* $router->setGetRoute("/entry", "app\controllers\LoginController", "handleGetRequest", "private"); */
/* $router->setPostRoute("/entry", "app\controllers\LoginController", "handlePostRequest", "public"); */
/* $router->setPostRoute("/", "app\controllers\LoginController", "handlePostRequest", "public"); */

/* $router->setGetRoute("/dashboard", "app\controllers\DashboardController", "handleGetRequest", "private"); */

/* $router->setGetRoute("/signout", ["app\controllers\SignOutController"]); */

/* $router->setGetRoute("/user/:username", ["app\controllers\UserController"]); */
/* $router->setGetRoute("/user/:username/settings", ["app\controllers\UserController", "handleSettingsGetRequest"]); */
/* $router->setPostRoute("/user/:username/update-password", ["app\controllers\UserController", "handleUpdateUserPasswordPostRequest"]); */
/* $router->setPostRoute("/user/:username/update-image", ["app\controllers\UserController", "handleUpdateUserProfileImagePostRequest"]); */

/* $router->setGetRoute("/users", ["app\controllers\UsersController"]); */

/* $router->setGetRoute("/ingredients", ["app\controllers\IngredientController"]); */
$router->setGetRoute("/products", "app\controllers\ProductController", "handleGetRequest", "public");

[$controllerName, $action] = $router->route($request->getRequestMethod(), $request->getRequestUri());
$controller = $container->get($controllerName);
$controller->$action();
