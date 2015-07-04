<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Creates a Linux container for a Rainmaker project
 *
 * @package Rainmaker\Task\Subtask
 */
class CreateLinuxContainer extends Task
{

  public function performTask()
  {
    $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $lxc->createContainer($this->getContainer());
  }

}
