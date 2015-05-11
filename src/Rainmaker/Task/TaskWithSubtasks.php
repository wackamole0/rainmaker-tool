<?php

namespace Rainmaker\Task;

use Rainmaker\RainmakerException;

/**
 * A class for representing a the unit of work required to perform some task by breaking the task up into subtasks
 *
 * @package Rainmaker\Task
 */
abstract class TaskWithSubtasks extends Task
{

  /**
   * @var Task[]
   */
  protected $subtasks = array();

  public function __construct()
  {
    $this->addSubtasks($this->getSubtasks());
  }

  /**
   * @return Task[]
   */
  protected function subtasks()
  {
    return $this->subtasks;
  }

  /**
   * @return Task[]
   */
  public function getSubtasks()
  {
    return array();
  }

  /**
   * @param Task[] $subtasks
   */
  public function addSubtasks($subtasks) {
    $this->subtasks += $subtasks;
  }

  /**
   * @throws RainmakerException
   * @throws \Exception
   */
  public function performTask()
  {
    $subtasks = $this->subtasks();
    $subtaskCount = count($subtasks);

    try {
      for ($i = 0; $i < $subtaskCount; $i++) {
        $subtasks[$i]
          ->setContainer($this->container)
          ->setProcessRunner($this->processRunner)
          ->setFilesystem($this->filesystem)
          ->performTask();
      }
    }
    catch (RainmakerException $e) {
      throw $e;
    }

  }

}
