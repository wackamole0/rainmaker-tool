<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\DhcpManager;

/**
 * Removes the DHCP settings for the container.
 *
 * @package Rainmaker\Task\Subtask
 */
class RemoveProjectDhcpSettings extends Task
{

    public function performTask()
    {
        $dhcp = new DhcpManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
        $dhcp->removeProjectDhcpSettings($this->getContainer(), true);
    }

}
