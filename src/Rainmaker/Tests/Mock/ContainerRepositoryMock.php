<?php

namespace Rainmaker\Tests\Mock;

use Rainmaker\Entity\ContainerRepository;

/**
 *
 */
class ContainerRepositoryMock extends ContainerRepository
{

  public $allParentContainers = array();
  public $allContainersOrderedForHostsInclude = array();

  public function __construct()
  {

  }

  public function getProjectContainers()
  {
    return array();
  }

  public function getProjectParentContainers()
  {
    return array();
  }

  public function getAllContainersOrderedForHostsInclude() {
    return $this->allContainersOrderedForHostsInclude;
  }

  public function getAllParentContainers() {
    return $this->allParentContainers;
  }

}
