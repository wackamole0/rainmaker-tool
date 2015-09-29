<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\BindManager;

/**
 * Remove the DNS settings for the project branch container.
 *
 * @package Rainmaker\Task\Subtask
 */
class RemoveProjectBranchDnsSettings extends Task
{

  public function performTask()
  {
    $dns = new BindManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $dns->removeDnsZoneForProjectBranchContainer($this->getContainer(), true);
  }

}
