<?php

namespace Rainmaker\Task\ProjectBranch;

use Rainmaker\Task\TaskWithSubtasks;
use Rainmaker\RainmakerException;
use Rainmaker\Entity\Container;

/**
 * Clones a Rainmaker project branch Linux Container.
 *
 * @package Rainmaker\Task\Project
 */
class CreateClone extends TaskWithSubtasks
{

  protected $startBranchContainerAfterBuild = false;

  public function getStartBranchContainerAfterBuild()
  {
    return $this->startBranchContainerAfterBuild;
  }

  public function setStartBranchContainerAfterBuild($startBranchContainerAfterBuild)
  {
    $this->startBranchContainerAfterBuild = $startBranchContainerAfterBuild;

    return $this;
  }

  public function startBranchProjectContainerAfterBuild()
  {
    return $this->getStartBranchContainerAfterBuild() === true;
  }

  public function getSubtasks()
  {
    $subtasks = array(
      // Bootstrap container

      // Create container
      new \Rainmaker\Task\Subtask\CloneProjectBranchLinuxContainer(),

      // Configure container
      new \Rainmaker\Task\Subtask\ConfigureProjectBranchLinuxContainer(),

      // Configure host
      new \Rainmaker\Task\Subtask\ConfigureProjectBranchLinuxHost(),

      // Configure DHCP
      new \Rainmaker\Task\Subtask\AddProjectBranchDhcpSettings(),

      // Configure Bind
      new \Rainmaker\Task\Subtask\AddProjectBranchDnsSettings(),

      // Configure fstab
      new \Rainmaker\Task\Subtask\AddProjectBranchFstabEntries(),

      // Configure Nfs
      new \Rainmaker\Task\Subtask\AddProjectBranchNfsEntries()
    );

    // Boot container
    if ($this->startBranchProjectContainerAfterBuild()) {
      $subtasks[] = new \Rainmaker\Task\Subtask\StartLinuxContainer();
    }

    return $subtasks;
  }

  public function performTask() {
    try {
      $this->getContainer()->setState(Container::STATE_PROVISIONING);
      $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($this->getContainer());
      parent::performTask();
      $this->getContainer()->setState(Container::STATE_STOPPED);
      $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($this->getContainer());
    } catch (RainmakerException $e) {
      throw $e;
    }
  }

  protected function generateLogHeader()
  {
    return 'Cloning project branch container with name: ' . $this->getContainer()->getName();
  }

}
