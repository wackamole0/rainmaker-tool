<?php

namespace Rainmaker\Task\Project;

use Rainmaker\Task\TaskWithSubtasks;

class Create extends TaskWithSubtasks
{

  public function getSubtasks()
  {
    return array(
      new \Rainmaker\Task\Subtask\EchoSubtask(),
      new \Rainmaker\Task\Subtask\EchoSubtask(),
      new \Rainmaker\Task\Subtask\ExceptionSubtask(),
      new \Rainmaker\Task\Subtask\EchoSubtask(),
      new \Rainmaker\Task\Subtask\EchoSubtask(),
    );
  }

}
