<?php

namespace Rainmaker\Tests\Unit\Fake;

use Rainmaker\Task\Task;
use Rainmaker\RainmakerException;

/**
 * A basic fake Task that will throw a generic RainmakerException and can be used in tests
 *
 * @package Rainmaker\Tests\Unit\Fake
 */
class ExceptionTaskFake extends Task
{

  public function performTask()
  {
    throw new RainmakerException();
  }

}
