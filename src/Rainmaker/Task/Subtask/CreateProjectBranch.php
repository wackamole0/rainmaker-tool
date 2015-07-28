<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\TaskWithSubtasks;
use Rainmaker\RainmakerException;

/**
 * Creates a Linux container for a Rainmaker project branch.
 *
 * @package Rainmaker\Task\Subtask
 */
class CreateProjectBranch extends TaskWithSubtasks
{

  protected $startContainerAfterBuild = false;

  public function getStartContainerAfterBuild()
  {
    return $this->startContainerAfterBuild;
  }

  public function setStartContainerAfterBuild($startContainerAfterBuild)
  {
    $this->startContainerAfterBuild = $startContainerAfterBuild;

    return $this;
  }

  public function startContainerAfterBuild()
  {
    return $this->getStartContainerAfterBuild() === true;
  }

  public function getSubtasks()
  {
    $subtasks = array(
      // Bootstrap container

      // Create container
      new \Rainmaker\Task\Subtask\CreateProjectBranchLinuxContainer(),

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
    if ($this->startContainerAfterBuild()) {
      $subtasks[] = new \Rainmaker\Task\Subtask\StartLinuxContainer();
    }

    return $subtasks;
  }

}
