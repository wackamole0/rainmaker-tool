<?php

namespace Rainmaker\Logger;

use Monolog\Logger;
use Rainmaker\Logger\Handler\StringBufferHandler;
use Rainmaker\Logger\Formatter\TaskLogFormatter;

/**
 *
 */
class TaskLogger extends Logger
{

  protected $log = null;

  public function __construct($name)
  {
    parent::__construct($name);
    $this->log = new StringBufferHandler(Logger::DEBUG);
    $this->log->setFormatter(new TaskLogFormatter());
    $this->pushHandler($this->log);
  }

  public function getLogBufferContents()
  {
    return $this->log->bufferContents();
  }

}
