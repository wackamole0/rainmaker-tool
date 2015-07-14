<?php

namespace Rainmaker\Tests\Unit\Task;

use Rainmaker\Tests\Unit\Fake\TaskWithSubtasksFake;
use Rainmaker\Tests\Unit\Fake\ExceptionTaskWithSubtasksFake;

/**
 * Unit tests \Rainmaker\Task\TaskWithSubtasks
 *
 * @package Rainmaker\Tests\Unit\Task
 */
class TaskWithSubtasksTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Tests that a task with subtasks that run to completion without failing does not throw an exception
   */
  public function testTask()
  {
    $task = new TaskWithSubtasksFake();
    $task->performTask();
  }

  /**
   * Tests that a task with a failing subtask throw an exception
   *
   * @expectedException \Rainmaker\RainmakerException
   */
  public function testExceptionTask()
  {
    $task = new ExceptionTaskWithSubtasksFake();
    $task->performTask();
  }

}
