<?php

namespace Rainmaker\Task\Project;

use Rainmaker\Task\TaskWithSubtasks;

/**
 * Creates a Rainmaker project Linux Container
 *
 * @package Rainmaker\Task\Project
 */
class Create extends TaskWithSubtasks
{

  protected $startProjectContainerAfterBuild = false;

  public function getStartProjectContainerAfterBuild()
  {
    return $this->startProjectContainerAfterBuild;
  }

  public function setStartProjectContainerAfterBuild($startProjectContainerAfterBuild)
  {
    $this->startProjectContainerAfterBuild = $startProjectContainerAfterBuild;
  }

  public function startProjectContainerAfterBuild()
  {
    return $this->getStartProjectContainerAfterBuild() === true;
  }

  public function getSubtasks()
  {
    $subtasks = array(
      // Bootstrap container

      // Create container
      new \Rainmaker\Task\Subtask\CreateLinuxContainer(),

      // Configure container
      new \Rainmaker\Task\Subtask\ConfigureLinuxContainer(),

      // Configure host
      new \Rainmaker\Task\Subtask\ConfigureLinuxHost(),

      // Configure DHCP
      new \Rainmaker\Task\Subtask\AddProjectDhcpSettings(),

      // Configure Bind
      new \Rainmaker\Task\Subtask\AddProjectDnsSettings(),

      // Configure fstab
      new \Rainmaker\Task\Subtask\AddProjectFstabEntries()
    );

    // Boot container
    if ($this->startProjectContainerAfterBuild()) {
      $subtasks[] = new \Rainmaker\Task\Subtask\StartLinuxContainer();
    }

    return $subtasks;
  }

  protected function generateLogHeader()
  {
    return 'Began creating new project container with name: ' . $this->getContainer()->getName();
  }

}
