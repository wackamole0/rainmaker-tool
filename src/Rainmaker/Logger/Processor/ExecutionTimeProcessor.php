<?php

namespace Rainmaker\Logger\Processor;

class ExecutionTimeProcessor
{

  protected $startTime;

  public function __construct()
  {
    $this->resetStartTime();
  }

  /**
   * @param  array $record
   * @return array
   */
  public function __invoke(array $record)
  {
    $record['extra']['executionTime'] = microtime(true) - $this->startTime;

    return $record;
  }

  public function resetStartTime()
  {
    $this->startTime = microtime(true);
  }
}
