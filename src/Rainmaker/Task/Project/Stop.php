<?php

namespace Rainmaker\Task\Project;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Stops a Rainmaker project container.
 *
 * @package Rainmaker\Task\Project
 */
class Stop extends Task {

  protected $list = '';

  public function performTask()
  {
    $this->log(\Monolog\Logger::DEBUG, 'Stopping container [' . $this->getContainer()->getName() . ']');

    $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $lxc->stopProjectContainer($this->getContainer());
  }

}
