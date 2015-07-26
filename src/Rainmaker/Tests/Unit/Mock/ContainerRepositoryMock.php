<?php

namespace Rainmaker\Tests\Unit\Mock;

use Rainmaker\Entity\ContainerRepository;
use Rainmaker\Entity\Container;

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

  public function saveContainer(Container $container)
  {
    return null;
  }

  public function getProjectContainers()
  {
    return $this->getAllParentContainers();
  }

  public function getProjectParentContainers()
  {
    return $this->getAllParentContainers();
  }

  public function getProjectBranchContainers(Container $container)
  {
    return array();
  }

  public function getAllProjectBranchContainers()
  {
    return array();
  }

  public function getAllContainersOrderedForHostsInclude() {
    return $this->allContainersOrderedForHostsInclude;
  }

  public function getAllParentContainers() {
    return $this->allParentContainers;
  }

  // DNS methods

  public function getDnsRecordsForProjectContainer(Container $container)
  {
    return array(
      array(
        'hostname'  => 'cluster',
        'ipAddress' => '10.100.1.1',
      )
    );
  }

  public function getDnsPtrRecordsForProjectContainer(Container $container)
  {
    return array(
      array(
        'hostname'  => 'cluster.test.localdev.',
        'ipAddress' => '1',
      )
    );
  }

}
