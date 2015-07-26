<?php

namespace RainmakerCliBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rainmaker\Logger\TaskLogger;
use Rainmaker\Process\ProcessRunner;
use Rainmaker\Util\Filesystem;

abstract class RainmakerCommand extends ContainerAwareCommand
{

  protected $task = NULL;
  protected $taskLogger = NULL;
  protected $containerEntity = NULL;

  protected function configure()
  {
    parent::configure();
    $this->setTask($this->task());
  }

  public function getEntityManager()
  {
    return $this->getContainer()->get('doctrine')->getManager();
  }

  public function setTask($task)
  {
    $this->task = $task;

    return $this;
  }

  protected function getTask()
  {
    return $this->task;
  }

  public function task()
  {
    throw new \LogicException('You must override the getTask() method in the concrete command class.');
  }

  protected function getConfiguredTask()
  {
    if (null == $this->taskLogger) {
      $this->taskLogger = new TaskLogger('tasklogger');
    }

    return $this->task
      ->setLogger($this->taskLogger)
      ->setEntityManager($this->getEntityManager())
      ->setProcessRunner(new ProcessRunner())
      ->setFilesystem(new Filesystem());
  }

  public function setContainerEntity($containerEntity)
  {
    $this->containerEntity = $containerEntity;

    return $this;
  }

  public function getContainerEntity()
  {
    return $this->containerEntity;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->task->performTask();
  }

}
