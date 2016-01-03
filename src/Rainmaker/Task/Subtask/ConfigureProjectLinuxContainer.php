<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Configure a Linux container for a Rainmaker project.
 *
 * @package Rainmaker\Task\Subtask
 */
class ConfigureProjectLinuxContainer extends Task
{

    public function performTask()
    {
        $this->log(\Monolog\Logger::DEBUG, 'Configuring container');

        $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
        $lxc->configureProjectContainer($this->getContainer());
    }

}
