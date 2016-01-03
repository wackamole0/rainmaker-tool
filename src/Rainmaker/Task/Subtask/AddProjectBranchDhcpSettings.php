<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\DhcpManager;

/**
 * Configure the DHCP settings for the project branch container.
 *
 * @package Rainmaker\Task\Subtask
 */
class AddProjectBranchDhcpSettings extends Task
{

    public function performTask()
    {
        $dhcp = new DhcpManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
        $dhcp->createProjectBranchDhcpSettings($this->getContainer(), true);
    }

}
