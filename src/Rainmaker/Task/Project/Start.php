<?php

namespace Rainmaker\Task\Project;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Starts a Rainmaker project container.
 *
 * @package Rainmaker\Task\Project
 */
class Start extends Task {

  protected $list = '';

  public function performTask()
  {
    $this->log(\Monolog\Logger::DEBUG, 'Starting container [' . $this->getContainer()->getName() . ']');

    $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $lxc->startProjectContainer($this->getContainer());
  }

}
