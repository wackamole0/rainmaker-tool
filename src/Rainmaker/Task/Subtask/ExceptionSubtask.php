<?php

namespace Rainmaker\Task\Subtask;

use Rainmaker\RainmakerException;

class ExceptionSubtask extends Subtask
{

  public function performSubtask()
  {
    throw new RainmakerException('Something bad');
  }

}
