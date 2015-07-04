<?php

namespace Rainmaker\Tests\Mock;

/**
 *
 */
class EntityManagerMock
{

  public $mockClassMap = array(
    'Rainmaker:Container' => '\Rainmaker\Tests\Mock\ContainerRepositoryMock'
  );

  protected $mockClassCache = array();

  public function __construct()
  {

  }

  public function getRepository($entity) {

    if (empty($this->mockClassCache[$entity])) {
      $this->mockClassCache[$entity] = new $this->mockClassMap[$entity];
    }

    return $this->mockClassCache[$entity];

  }

}
