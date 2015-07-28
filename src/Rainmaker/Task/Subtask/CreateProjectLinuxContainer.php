<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Creates a Linux container for a Rainmaker project.
 *
 * @package Rainmaker\Task\Subtask
 */
class CreateProjectLinuxContainer extends Task
{

  public function performTask()
  {
    $this->log(\Monolog\Logger::DEBUG, 'Constructing container');

    $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $lxc->createProjectContainer($this->getContainer());
  }

}
