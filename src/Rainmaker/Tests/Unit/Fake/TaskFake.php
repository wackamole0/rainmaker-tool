<?php

namespace Rainmaker\Tests\Unit\Fake;

use Rainmaker\Task\Task;

/**
 * A basic fake Task that can be used in tests
 *
 * @package Rainmaker\Tests\Unit\Fake
 */
class TaskFake extends Task
{

  public function performTask()
  {
    // Nop
  }

}
