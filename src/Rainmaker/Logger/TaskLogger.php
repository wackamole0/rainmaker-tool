<?php

namespace Rainmaker\Logger;

use Monolog\Logger;
use Rainmaker\Logger\Handler\StringBufferHandler;
use Rainmaker\Logger\Processor\ExecutionTimeProcessor;
use Rainmaker\Logger\Formatter\TaskLogFormatter;

/**
 *
 */
class TaskLogger extends Logger
{

  protected $log = null;
  protected $executionTime = null;

  public function __construct($name)
  {
    parent::__construct($name);
    $this->log = new StringBufferHandler(Logger::DEBUG);
    $this->log->setFormatter(new TaskLogFormatter());
    $this->pushHandler($this->log);

    $this->executionTime = new ExecutionTimeProcessor();
    $this->pushProcessor($this->executionTime);
  }

  public function getLogBufferContents()
  {
    return $this->log->bufferContents();
  }

}
