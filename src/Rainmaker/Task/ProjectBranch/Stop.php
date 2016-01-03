<?php

namespace Rainmaker\Task\ProjectBranch;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\LxcManager;

/**
 * Stops a Rainmaker project branch container.
 *
 * @package Rainmaker\Task\ProjectBranch
 */
class Stop extends Task
{

    protected $list = '';

    public function performTask()
    {
        $this->log(\Monolog\Logger::DEBUG, 'Stopping container [' . $this->getContainer()->getName() . ']');

        $lxc = new LxcManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
        $lxc->stopProjectBranchContainer($this->getContainer());
    }

}
