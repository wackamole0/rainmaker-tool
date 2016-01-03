<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\NfsManager;

/**
 * Remove the NFS entries for the project branch container.
 *
 * @package Rainmaker\Task\Subtask
 */
class RemoveProjectBranchNfsEntries extends Task
{

    public function performTask()
    {
        $fstab = new NfsManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
        $fstab->createProjectBranchEntries($this->getContainer(), true);
    }

}
