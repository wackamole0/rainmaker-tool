<?php

namespace Rainmaker\Task;

abstract class Task
{

  protected $output = NULL;

  protected $container = NULL;

  public function getOutputInterface()
  {
    return $this->output;
  }

  public function setOutputInterface($output)
  {
    $this->output = $output;

    return $this;
  }

  public function getContainer()
  {
    return $this->container;
  }

  public function setContainer($container)
  {
    $this->container = $container;

    return $this;
  }

  public function performTask()
  {
    throw new \LogicException('You must override the performTask() method in the concrete command class.');
  }

  public function performCleanup()
  {
    ;
  }

}
