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

  public function getSubtasks()
  {
    return array(
      // Bootstrap container

      // Create container
      new \Rainmaker\Task\Subtask\CreateLinuxContainer(),

      // Configure container
      //new \Rainmaker\Task\Subtask\ConfigureLinuxContainer(),

      // Configure DHCP
      //new \Rainmaker\Task\Subtask\AddProjectDhcpSettings(),

      // Configure Bind
      //new \Rainmaker\Task\Subtask\AddProjectDnsSettings(),

      // Boot container
      //new \Rainmaker\Task\Subtask\StartLinuxContainer(),
    );
  }

}
