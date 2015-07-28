<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\FstabManager;

/**
 * Configure the Fstab entries for the project branch container.
 *
 * @package Rainmaker\Task\Subtask
 */
class AddProjectBranchFstabEntries extends Task
{

  public function performTask()
  {
    $fstab = new FstabManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $fstab->createProjectBranchFstabEntries($this->getContainer(), true);
  }

}
