<?php

namespace Rainmaker\Process;

use Symfony\Component\Process\Process;

/**
 * A wrapper class for executing processes.
 *
 * @package Rainmaker\Process
 * @return void
 */
class ProcessRunner {

  /**
   * @param Process $process
   */
  public function run(Process $process) {
    $process->mustRun();
  }

}
