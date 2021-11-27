create the find oneByPhiAndDate and find manyByDateInterval methods for the request


<?php

use PHPUnit\Framework\TestCase;

class RequestMapperTest extends TestCase
{
  public function testFindingOneByPhiAndDate()
  {
    //Get a new Pdo connection from the script here dont create a new one
    $databaseConnection = new PDO();


    $requestMapper = new RequestMapper($databaseConnection);
    $requestMapper->findOneByPhiAndDate(15, 200, 2021);
    //test two json objects are the same
  }
}

?>
