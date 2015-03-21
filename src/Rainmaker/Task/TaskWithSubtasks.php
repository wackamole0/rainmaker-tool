<?php

namespace Rainmaker\Task;

use Rainmaker\RainmakerException;

abstract class TaskWithSubtasks extends Task
{

  protected $subtasks = array();

  public function __construct()
  {
    $this->addSubtasks($this->getSubtasks());
  }

  protected function subtasks()
  {
    return $this->subtasks;
  }

  public function getSubtasks()
  {
    return array();
  }

  public function addSubtasks($subtasks) {
    $this->subtasks += $subtasks;
  }

  public function performTask()
  {
    $subtasks = $this->subtasks();
    $subtaskCount = count($subtasks);
    $subtasksComplete = FALSE;

    try {
      for ($i = 0; $i < $subtaskCount; $i++) {
        $subtasks[$i]->setContainer($this->container)->setOutputInterface($this->output)->performSubtask();
        $this->output->writeln($i);
      }
      $subtasksComplete = TRUE;
    }
    catch (RainmakerException $e) {
      $subtasksComplete = FALSE;
      $this->output->writeln("Exception hit during task $i");
    }

    if ($subtasksComplete) {
      return;
    }

    try {
      for (; $i >= 0; $i--) {
        $subtasks[$i]->performCleanup();
        $this->output->writeln($i);
      }
    }
    catch (RainmakerException $e) {
      ;
    }
  }

  public function performCleanup()
  {
    ;
  }

}
