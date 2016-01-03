<?php

namespace Rainmaker\Tests\Unit\Task;

use Rainmaker\Tests\Unit\Fake\TaskFake;
use Rainmaker\Tests\Unit\Fake\ExceptionTaskFake;

/**
 * Unit tests \Rainmaker\Task\Task
 *
 * @package Rainmaker\Tests\Unit\Task
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
