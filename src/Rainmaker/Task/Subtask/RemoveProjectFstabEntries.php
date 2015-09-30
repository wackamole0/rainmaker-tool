<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\FstabManager;

/**
 * Remove the Fstab entries for the project container.
 *
 * @package Rainmaker\Task\Subtask
 */
class RemoveProjectFstabEntries extends Task
{

  public function performTask()
  {
    $fstab = new FstabManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $fstab->removeProjectFstabEntries($this->getContainer());
  }

}
