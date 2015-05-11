<?php

namespace Rainmaker\Tests\Task;

use Rainmaker\Tests\Fake\TaskWithSubtasksFake;
use Rainmaker\Tests\Fake\ExceptionTaskWithSubtasksFake;

/**
 * Unit tests \Rainmaker\Task\TaskWithSubtasks
 *
 * @package Rainmaker\Tests\Task
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
