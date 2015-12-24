<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\ComponentManager\RainmakerProfileManager;

/**
 * Configure the DNS settings for the container.
 *
 * @package Rainmaker\Task\Subtask
 */
class ConfigureProjectBranchProfileSettings extends Task
{

  public function performTask()
  {
    $manager = new RainmakerProfileManager($this->getEntityManager(), $this->getProcessRunner(), $this->getFilesystem());
    $manager->configureProjectBranchProfileSettings($this->getContainer());
  }

}
