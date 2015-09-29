<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\DhcpManager;

/**
 * Removes the DHCP settings for the project branch container.
 *
 * @package Rainmaker\Task\Subtask
 */
class RemoveProjectBranchDhcpSettings extends Task
{

  public function performTask()
  {
    $dhcp = new DhcpManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $dhcp->removeProjectBranchDhcpSettings($this->getContainer(), true);
  }

}
