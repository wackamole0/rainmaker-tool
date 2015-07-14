<?php

namespace Rainmaker\Tests\Unit\Mock;

use Rainmaker\Process\ProcessRunner;
use Symfony\Component\Process\Process;

/**
 * Mocks out Rainmaker\Process\ProcessRunner so that the process passed to it is never actually executed but
 * still return a successful status to the caller.
 *
 * @package Rainmaker\Tests\Unit\Mock
 */
class ProcessRunnerMock extends ProcessRunner {

  public function run(Process $process) {
    return true;
  }

}
