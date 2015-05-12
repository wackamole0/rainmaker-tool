<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\Task\Task;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Creates a Linux container for a Rainmaker project
 *
 * @package Rainmaker\Task\Subtask
 */
class CreateLinuxContainer extends Task
{

  public function performTask()
  {
    try {
      $process = new Process('lxc-clone _golden-proj_ ' . $this->getContainer()->getName());
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

}
