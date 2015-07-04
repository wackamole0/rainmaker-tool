<?php

namespace Rainmaker\Task;

use Rainmaker\RainmakerException;

/**
 * A class for representing the unit of work required to perform some task
 *
 * @package Rainmaker\Task
 */
abstract class Task
{

  /**
   * @var \Rainmaker\Entity\Container
   */
  protected $container = NULL;

  protected $entityManager = NULL;

  /**
   * @var \Rainmaker\Process\ProcessRunner
   */
  protected $processRunner = NULL;

  /**
   * @var \Rainmaker\Util\Filesystem
   */
  protected $filesystem = NULL;

  /**
   * @return \Rainmaker\Entity\Container
   */
  public function getContainer()
  {
    return $this->container;
  }

  /**
   * @param \Rainmaker\Entity\Container $container
   * @return Task $this
   */
  public function setContainer($container)
  {
    $this->container = $container;

    return $this;
  }

  public function getEntityManager()
  {
    return $this->entityManager;
  }

  public function setEntityManager($entityManager)
  {
    $this->entityManager = $entityManager;

    return $this;
  }

  /**
   * @return \Rainmaker\Process\ProcessRunner
   */
  protected function getProcessRunner()
  {
    return $this->processRunner;
  }

  /**
   * @param \Rainmaker\Process\ProcessRunner $processRunner
   * @return Task $this
   */
  public function setProcessRunner($processRunner)
  {
    $this->processRunner = $processRunner;

    return $this;
  }

  /**
   * @return \Rainmaker\Util\Filesystem $filesystem
   */
  protected function getFilesystem()
  {
    return $this->filesystem;
  }

  /**
   * @param \Rainmaker\Util\Filesystem $filesystem
   * @return Task $this
   */
  public function setFilesystem($filesystem)
  {
    $this->filesystem = $filesystem;

    return $this;
  }

  /**
   * @return void
   * @throws RainmakerException
   * @throws \Exception
   */
  public function performTask()
  {
    throw new \LogicException('You must override the performTask() method in the concrete command class.');
  }

}
