<?php

namespace Rainmaker\ComponentManager;

/**
 * Abstract base class used by all classes that manager a component of the Rainmaker ecosystem.
 *
 * @package Rainmaker\ComponentManager
 */
abstract class ComponentManager {

  protected $entityManager  = NULL;

  /**
   * @var \Rainmaker\Process\ProcessRunner
   */
  protected $processRunner  = NULL;

  /**
   * @var \Rainmaker\Util\Filesystem
   */
  protected $filesystem     = NULL;

  /**
   * @var \Rainmaker\Entity\Container
   */
  protected $container      = NULL;

  public function __construct($entityManager, $processRunner, $filesystem)
  {
    $this->entityManager  = $entityManager;
    $this->processRunner  = $processRunner;
    $this->filesystem     = $filesystem;
  }

  /**
   * @return \Rainmaker\Entity\Container
   */
  public function getContainer()
  {
    return $this->container;
  }

  /**
   * @param \Rainmaker\Entity\Container $container
   * @return ComponentManager $this
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
   * @return ComponentManager $this
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
   * @return ComponentManager $this
   */
  public function setFilesystem($filesystem)
  {
    $this->filesystem = $filesystem;

    return $this;
  }

}
