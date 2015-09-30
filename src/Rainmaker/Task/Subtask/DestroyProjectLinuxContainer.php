<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Destroys a Linux container for a Rainmaker project.
 *
 * @package Rainmaker\Task\Subtask
 */
class DestroyProjectLinuxContainer extends Task
{

  public function performTask()
  {
    $this->log(\Monolog\Logger::DEBUG, 'Destroying container');

    $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $lxc->destroyProjectContainer($this->getContainer());
  }

}
