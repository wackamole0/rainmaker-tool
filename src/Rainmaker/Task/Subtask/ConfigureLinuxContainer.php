<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Configure a Linux container for a Rainmaker project
 *
 * @package Rainmaker\Task\Subtask
 */
class ConfigureLinuxContainer extends Task
{

  public function performTask()
  {
    $this->log(\Monolog\Logger::DEBUG, 'Configuring container');

    $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $lxc->configureContainer($this->getContainer());
  }

}
