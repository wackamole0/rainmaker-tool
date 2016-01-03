<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Start a Rainmaker project container.
 *
 * @package Rainmaker\Task\Subtask
 */
class StartLinuxContainer extends Task
{

    public function performTask()
    {
        $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
        $lxc->startContainer($this->getContainer());
    }

}
