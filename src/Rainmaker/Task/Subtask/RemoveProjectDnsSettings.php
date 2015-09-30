<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\BindManager;

/**
 * Remove the DNS settings for the container.
 *
 * @package Rainmaker\Task\Subtask
 */
class RemoveProjectDnsSettings extends Task
{

  public function performTask()
  {
    $dns = new BindManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $dns->removeDnsZoneForProjectContainer($this->getContainer(), true);
  }

}
