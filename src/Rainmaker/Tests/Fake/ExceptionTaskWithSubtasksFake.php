<?php

namespace Rainmaker\Tests\Fake;

use Rainmaker\Task\TaskWithSubtasks;

/**
 * A basic fake task with fake subtasks that will throw a generic RainmakerException and can be used in tests
 *
 * @package Rainmaker\Tests\Fake
 */
class ExceptionTaskWithSubtasksFake extends TaskWithSubtasks
{

  public function getSubtasks()
  {
    return array(
      new TaskFake(),
      new ExceptionTaskFake(),
      new TaskFake()
    );
  }

}
