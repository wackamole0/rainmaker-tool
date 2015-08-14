<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Creates a clone of a Linux container for a Rainmaker project branch.
 *
 * @package Rainmaker\Task\Subtask
 */
class CloneProjectBranchLinuxContainer extends Task
{

  public function performTask()
  {
    $this->log(\Monolog\Logger::DEBUG, 'Cloning container');

    $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $lxc->cloneProjectBranchContainer($this->getContainer(), $this->getContainer()->getCloneSource());
  }

}
