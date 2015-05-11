<?php

namespace Rainmaker\Tests\Task;

use Rainmaker\Tests\Fake\TaskFake;
use Rainmaker\Tests\Fake\ExceptionTaskFake;

/**
 * Unit tests \Rainmaker\Task\Task
 *
 * @package Rainmaker\Tests\Task
 */
class TaskTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Tests that a successful task does not throw an exception
   */
  public function testTask()
  {
    $task = new TaskFake();
    $task->performTask();
  }

  /**
   * Tests that a failing task does throw an exception
   *
   * @expectedException \Rainmaker\RainmakerException
   */
  public function testExceptionTask()
  {
    $task = new ExceptionTaskFake();
    $task->performTask();
  }

}
