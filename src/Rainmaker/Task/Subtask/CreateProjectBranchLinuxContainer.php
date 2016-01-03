<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Creates a Linux container for a Rainmaker project branch.
 *
 * @package Rainmaker\Task\Subtask
 */
class CreateProjectBranchLinuxContainer extends Task
{

    public function performTask()
    {
        $this->log(\Monolog\Logger::DEBUG, 'Constructing container');

        $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
        $lxc->createProjectBranchContainer($this->getContainer());
    }

}
