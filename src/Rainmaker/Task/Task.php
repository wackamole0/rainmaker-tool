<?php

namespace Rainmaker\Task;

use Rainmaker\RainmakerException;

/**
 * A class for representing the unit of work required to perform some task.
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
   * @var \Monolog\Logger
   */
  protected $logger = NULL;

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
   * @return \Monolog\Logger $logger
   */
  protected function getLogger()
  {
    return $this->logger;
  }

  /**
   * @param \Monolog\Logger
   * @return Task $this
   */
  public function setLogger($logger)
  {
    $this->logger = $logger;

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

  /**
   * Adds a log record.
   *
   * @param  integer $level   The logging level
   * @param  string  $message The log message
   * @param  array   $context The log context
   */
  public function log($level, $message, array $context = array())
  {
    if (!empty($this->logger)) {
      $this->logger->addRecord($level, $message, $context);
    }
  }

}
