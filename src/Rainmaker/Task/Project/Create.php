<?php

namespace Rainmaker\Task\Project;

use Rainmaker\Task\TaskWithSubtasks;
use Rainmaker\RainmakerException;
use Rainmaker\Entity\Container;

/**
 * Creates a Rainmaker project Linux Container.
 *
 * @package Rainmaker\Task\Project
 */
class Create extends TaskWithSubtasks
{

  protected $startProjectContainerAfterBuild = false;

  protected $branchContainer = null;

  protected $startBranchContainerAfterBuild = false;

  public function getStartProjectContainerAfterBuild()
  {
    return $this->startProjectContainerAfterBuild;
  }

  public function setStartProjectContainerAfterBuild($startProjectContainerAfterBuild)
  {
    $this->startProjectContainerAfterBuild = $startProjectContainerAfterBuild;

    return $this;
  }

  public function startProjectContainerAfterBuild()
  {
    return $this->getStartProjectContainerAfterBuild() === true;
  }

  public function getBranchContainer()
  {
    return $this->branchContainer;
  }

  public function setBranchContainer($branchContainer)
  {
    $this->branchContainer = $branchContainer;

    return $this;
  }

  public function getStartBranchContainerAfterBuild()
  {
    return $this->startBranchContainerAfterBuild;
  }

  public function setStartBranchContainerAfterBuild($startBranchContainerAfterBuild)
  {
    $this->startBranchContainerAfterBuild = $startBranchContainerAfterBuild;

    return $this;
  }

  public function getSubtasks()
  {
    $subtasks = array(
      // Bootstrap container

      // Create container
      new \Rainmaker\Task\Subtask\CreateProjectLinuxContainer(),

      // Configure container
      new \Rainmaker\Task\Subtask\ConfigureProjectLinuxContainer(),

      // Configure host
      new \Rainmaker\Task\Subtask\ConfigureProjectLinuxHost(),

      // Configure DHCP
      new \Rainmaker\Task\Subtask\AddProjectDhcpSettings(),

      // Configure Bind
      new \Rainmaker\Task\Subtask\AddProjectDnsSettings(),

      // Configure fstab
      new \Rainmaker\Task\Subtask\AddProjectFstabEntries(),

      // Mark container as stopped
      new \Rainmaker\Task\Subtask\SetContainerStateToStopped()
    );

    // Boot container
    if ($this->startProjectContainerAfterBuild()) {
      $subtasks[] = new \Rainmaker\Task\Subtask\StartLinuxContainer();
    }

    return $subtasks;
  }

  public function performTask() {
    try {
      $this->getContainer()->setState(Container::STATE_PROVISIONING);
      parent::performTask();
      if (!empty($this->branchContainer)) {
        $projectBranchTask = new \Rainmaker\Task\Subtask\CreateProjectBranch();
        $this->prepareSubtask($projectBranchTask);
        $projectBranchTask
          ->setContainer($this->branchContainer)
          ->setStartContainerAfterBuild($this->getStartBranchContainerAfterBuild())
          ->performTask();
      }
    } catch (RainmakerException $e) {
      throw $e;
    }
  }

  protected function generateLogHeader()
  {
    return 'Began creating new project container with name: ' . $this->getContainer()->getName();
  }

}
