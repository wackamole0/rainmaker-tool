<?php

namespace Rainmaker\Tests\Fake;

use Rainmaker\Task\Task;

/**
 * A basic fake Task that can be used in tests
 *
 * @package Rainmaker\Tests\Fake
 */
class TaskFake extends Task
{

  public function performTask()
  {
    // Nop
  }

}
