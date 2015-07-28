<?php

namespace Rainmaker\Tests\Unit\Mock;

use Rainmaker\Entity\ContainerRepository;
use Rainmaker\Entity\Container;

/**
 *
 */
class ContainerRepositoryMock extends ContainerRepository
{

  public $projectContainers = array();
  public $branchContainers = array();
  public $allBranchContainers = array();
  public $allContainersOrderedForHostsInclude = array();
  public $parentContainer = null;

  public function __construct()
  {

  }

  public function saveContainer(Container $container)
  {
    return null;
  }

  public function getAllParentContainers() {
    return $this->getProjectContainers();
  }

  public function getProjectContainers()
  {
    return $this->projectContainers;
  }

  public function getProjectParentContainers()
  {
    return $this->getProjectContainers();
  }

  public function getProjectBranchContainers(Container $container)
  {
    return $this->branchContainers;
  }

  public function getAllProjectBranchContainers()
  {
    return $this->allBranchContainers;
  }

  public function getAllContainersOrderedForHostsInclude() {
    return $this->allContainersOrderedForHostsInclude;
  }

  public function getParentContainer(Container $container)
  {
    if (null !== $container->getParentId()) {
      return $this->parentContainer;
    }

    return $container;
  }

}
