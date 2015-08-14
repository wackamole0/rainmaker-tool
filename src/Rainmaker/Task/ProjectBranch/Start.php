<?php

namespace Rainmaker\Task\ProjectBranch;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Starts a Rainmaker project branch container.
 *
 * @package Rainmaker\Task\ProjectBranch
 */
class Start extends Task {

  protected $list = '';

  public function performTask()
  {
    $this->log(\Monolog\Logger::DEBUG, 'Starting container [' . $this->getContainer()->getName() . ']');

    $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $lxc->startProjectBranchContainer($this->getContainer());
  }

}
