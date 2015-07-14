<?php

namespace Rainmaker\Task;

use Rainmaker\RainmakerException;
use Monolog\Logger;

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
    $this->sendLogHeader();

    $subtasks = $this->subtasks();
    $subtaskCount = count($subtasks);

    try {
      for ($i = 0; $i < $subtaskCount; $i++) {
        $subtasks[$i]
          ->setContainer($this->container)
          ->setEntityManager($this->entityManager)
          ->setProcessRunner($this->processRunner)
          ->setFilesystem($this->filesystem)
          ->setLogger($this->logger)
          ->performTask();
      }
    }
    catch (RainmakerException $e) {
      $this->log(Logger::ERROR,
        "Exception Message:\n\n" . $e->getMessage() . "\n\n" .
        "Exception Trace:\n\n" . $e->getTraceAsString() . "\n"
      );
      throw $e;
    }

  }

  /**
   * Send the the header message for this subtask to the logger
   */
  protected function sendLogHeader()
  {
    $header = $this->generateLogHeader();
    if (!empty($header) && !is_null($this->logger)) {
      $this->log(Logger::INFO, $header);
    }
  }

  /**
   * Returns the log header message for this subtask
   *
   * @return string|null
   */
  protected function generateLogHeader()
  {
    return NULL;
  }

}
