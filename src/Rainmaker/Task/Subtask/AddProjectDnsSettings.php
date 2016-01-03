<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\BindManager;

/**
 * Configure the DNS settings for the container.
 *
 * @package Rainmaker\Task\Subtask
 */
class AddProjectDnsSettings extends Task
{

    public function performTask()
    {
        $dns = new BindManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
        $dns->configureDnsZoneForProjectContainer($this->getContainer(), true);
    }

}
