<?php

declare(strict_types=1);

namespace common;

use Phinx\Console\PhinxApplication;
use Dotenv\Dotenv;
use \PDO;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class SetDatabaseForTest
{
  private static PhinxApplication $phinxApp;
  private static string $migrations = "test";
  /* private static string $migrations = ""; */

  public static function getConnection(): PDO
  {
    $dotenv = Dotenv::createImmutable(APP_DIR);
    $dotenv->load();
    $dsn = $_ENV["driver"] . ":host=" . $_ENV["host"] . ";dbname=" . $_ENV["dbname_test"];
    $username = $_ENV["username"];
    $password = $_ENV["password"];
    $options = [
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    return new PDO($dsn, $username, $password, $options);
  }

  public static function applyMigrations($migrations = null): void
  {
    if (!isset(self::$phinxApp)) {
      self::$phinxApp =  new PhinxApplication();
    }
    if ($migrations !== null) {
      self::$migrations = $migrations;
    }

    $stringInput = "migrate -e testing" . (self::$migrations === "test" ? " -c ./tests/phinx.php" : "");
    self::$phinxApp->setAutoExit(false);
    self::$phinxApp->run(new StringInput($stringInput), new NullOutput());
  }

  public static function removeMigrations($migrations = "test"): void
  {
    $stringInput = "rollback -e testing -t 0" . (self::$migrations === "test" ? " -c ./tests/phinx.php" : "");
    self::$phinxApp->run(new StringInput($stringInput), new NullOutput());
  }
}
