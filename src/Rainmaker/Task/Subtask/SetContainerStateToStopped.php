<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Rainmaker\Entity\Container;

/**
 * Marks the container as being in the stopped state.
 *
 * @package Rainmaker\Task\Subtask
 */
class SetContainerStateToStopped extends Task
{

  public function performTask()
  {
    $this->getContainer()->setState(Container::STATE_STOPPED);
    $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($this->getContainer());
  }

}
