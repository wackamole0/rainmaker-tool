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

  public function removeContainer(Container $container) {
    return null;
  }

  public function getAllParentContainers($status = null)
  {
    return $this->excludeContainersWithStatuses(
      $this->getProjectContainers(),
      $this->setDefaultStatusIfEmpty($status)
    );
  }

  public function getProjectContainers()
  {
    return $this->projectContainers;
  }

  public function getProjectParentContainers($status = null)
  {
    return $this->excludeContainersWithStatuses(
      $this->projectContainers,
      $this->setDefaultStatusIfEmpty($status)
    );
  }

  public function getProjectBranchContainers(Container $container, $status = null)
  {
    return $this->excludeContainersWithStatuses(
      $this->branchContainers,
      $this->setDefaultStatusIfEmpty($status)
    );
  }

  public function getAllProjectBranchContainers($status = null)
  {
    return $this->excludeContainersWithStatuses(
      $this->allBranchContainers,
      $this->setDefaultStatusIfEmpty($status)
    );
  }

  public function getAllContainersOrderedForHostsInclude($status = null)
  {
    return $this->excludeContainersWithStatuses(
      $this->allContainersOrderedForHostsInclude,
      $this->setDefaultStatusIfEmpty($status)
    );
  }

  public function getParentContainer(Container $container)
  {
    if (null !== $container->getParentId()) {
      return $this->parentContainer;
    }

    return $container;
  }

  /**
   * @param Container[] $containers
   * @param $statuses
   * @return Container[]
   */
  protected function excludeContainersWithStatuses($containers, $statuses)
  {
    if (!is_array($statuses)) {
      $statuses = array($statuses);
    }

    $filteredContainers = array();
    foreach ($containers as $container) {
      if (!in_array($container->getState(), $statuses)) {
        $filteredContainers[] = $container;
      }
    }

    return $filteredContainers;
  }

}
