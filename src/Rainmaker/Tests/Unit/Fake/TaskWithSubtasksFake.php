<?php

namespace Rainmaker\Tests\Unit\Fake;

use Rainmaker\Task\TaskWithSubtasks;

/**
 * A basic fake task with fake subtasks that can be used in tests
 *
 * @package Rainmaker\Tests\Unit\Fake
 */
class TaskWithSubtasksFake extends TaskWithSubtasks
{

  public function getSubtasks()
  {
    return array(
      new TaskFake(),
      new TaskFake(),
      new TaskFake()
    );
  }

}
