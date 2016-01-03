<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\DhcpManager;

/**
 * Configure the DHCP settings for the container.
 *
 * @package Rainmaker\Task\Subtask
 */
class AddProjectDhcpSettings extends Task
{

    public function performTask()
    {
        $dhcp = new DhcpManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
        $dhcp->createProjectDhcpSettings($this->getContainer(), true);
    }

}
