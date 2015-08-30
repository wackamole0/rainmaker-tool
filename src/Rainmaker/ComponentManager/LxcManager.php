<?php

namespace Rainmaker\ComponentManager;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Rainmaker\Process\Lxc\CreateProjectContainerProcess;
use Rainmaker\Process\Lxc\CreateProjectBranchContainerProcess;
use Rainmaker\Process\Lxc\CloneProjectBranchContainerProcess;
use Rainmaker\Process\Lxc\GetContainerStatusProcess;
use Rainmaker\Process\Lxc\StartProjectContainerProcess;
use Rainmaker\Process\Lxc\StopProjectContainerProcess;
use Rainmaker\Process\Lxc\StartProjectBranchContainerProcess;
use Rainmaker\Process\Lxc\StopProjectBranchContainerProcess;
use Rainmaker\Entity\Container;
use Rainmaker\Util\Template;

/**
 * A class for managing the Linux Containers (LXC) in a Rainmaker environment.
 *
 * @package Rainmaker\ComponentManager
 */
class LxcManager extends ComponentManager {

  const DEFAULT_LXC_BUILD_TIMEOUT = 300;

  /**
   * Create an new Linux container for the given Rainmaker project container.
   *
   * @param \Rainmaker\Entity\Container $container
   */
  public function createProjectContainer(Container $container)
  {
    $this->container = $container;

    try {
      $process = new CreateProjectContainerProcess($this->getContainer());
      $process->setTimeout(static::DEFAULT_LXC_BUILD_TIMEOUT);
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Create an new Linux container for the given Rainmaker project branch container.
   *
   * @param Container $container
   */
  public function createProjectBranchContainer(Container $container)
  {
    $this->container = $container;
    $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($container);
    try {
      if (Container::STATE_STOPPED == $project->getState()) {
        $this->startProjectContainer($project);
        sleep(10); // Give the container a chance to start and settle before trying to connect to it
      }

      $process = new CreateProjectBranchContainerProcess($this->getContainer(), $project);
      $process->setTimeout(static::DEFAULT_LXC_BUILD_TIMEOUT);
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Creates a clone of a Linux container for a Rainmaker project branch.
   *
   * @param Container $newBranchContainer
   * @param Container $sourceBranchContainer
   */
  public function cloneProjectBranchContainer(Container $newBranchContainer, Container $sourceBranchContainer)
  {
    $this->container = $newBranchContainer;
    $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($newBranchContainer);
    try {
      $process = new CloneProjectBranchContainerProcess($newBranchContainer, $sourceBranchContainer, $project);
      $process->setTimeout(static::DEFAULT_LXC_BUILD_TIMEOUT);
      $this->getProcessRunner()->run($process);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Creates the configuration file for the Rainmaker project Linux container.
   *
   * @param Container $container
   */
  public function configureProjectContainer(Container $container)
  {
    $this->container = $container;

    $this->getContainer()->setLxcUtsName($this->getLxcConfigurationSettingValue('lxc.utsname'));
    $this->getContainer()->setLxcHwAddr($this->getLxcConfigurationSettingValue('lxc.network.hwaddr'));
    $this->getContainer()->setLxcRootFs($this->getLxcConfigurationSettingValue('lxc.rootfs'));
    $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($this->getContainer());

    $this->writeProjectLxcConfigurationFile();
  }

  /**
   * Creates the configuration file for the Rainmaker project branch Linux container.
   *
   * @param Container $container
   */
  public function configureProjectBranchContainer(Container $container)
  {
    $this->container = $container;

    $this->getContainer()->setLxcUtsName($this->getLxcConfigurationSettingValue('lxc.utsname'));
    $this->getContainer()->setLxcHwAddr($this->getLxcConfigurationSettingValue('lxc.network.hwaddr'));
    $this->getContainer()->setLxcRootFs($this->getLxcConfigurationSettingValue('lxc.rootfs'));
    $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($this->getContainer());

    $this->writeProjectBranchLxcConfigurationFile();
  }

  /**
   * Starts a Linux container.
   *
   * @param Container $container
   */
  public function startContainer(Container $container) {
    $this->container = $container;

    if ($container->isProjectBranch()) {
      $this->startProjectBranchContainer($container);
    }

    $this->startProjectContainer($container);
  }

  /**
   * Starts a Rainmaker project Linux container.
   *
   * @param Container $container
   */
  public function startProjectContainer(Container $container)
  {
    try {
      $process = new GetContainerStatusProcess($container);
      if (stristr($this->getProcessRunner()->run($process), 'stopped') === FALSE) {
        return;
      }
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }

    try {
      $container->setState(Container::STATE_STARTING);
      $process = new StartProjectContainerProcess($container);
      $this->getProcessRunner()->run($process);
      $container->setState(Container::STATE_RUNNING);
      $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($container);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Starts a Rainmaker project branch Linux container.
   *
   * @param Container $container
   */
  public function startProjectBranchContainer(Container $container)
  {
    $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($container);

    try {
      $this->startProjectContainer($project);
      $process = new GetContainerStatusProcess($container, $project);
      if (stristr($this->getProcessRunner()->run($process), 'stopped') === FALSE) {
        return;
      }
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }

    try {
      $container->setState(Container::STATE_STARTING);
      $process = new StartProjectBranchContainerProcess($container, $project);
      $this->getProcessRunner()->run($process);
      $container->setState(Container::STATE_RUNNING);
      $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($container);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Stops a Linux container.
   *
   * @param Container $container
   */
  public function stopContainer(Container $container) {
    $this->container = $container;

    if ($container->isProjectBranch()) {
      $this->stopProjectBranchContainer($container);
    }

    $this->stopProjectContainer($container);
  }

  /**
   * Stops a Rainmaker project Linux container.
   *
   * @param Container $container
   */
  public function stopProjectContainer(Container $container)
  {
    try {
      $process = new GetContainerStatusProcess($container);
      if (stristr($this->getProcessRunner()->run($process), 'running') === FALSE) {
        return;
      }
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }

    try {
      $process = new StopProjectContainerProcess($container);
      $this->getProcessRunner()->run($process);
      $container->setState(Container::STATE_STOPPED);
      $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($container);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Stops a Rainmaker project branch Linux container.
   *
   * @param Container $container
   */
  public function stopProjectBranchContainer(Container $container)
  {
    $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($container);

    try {
      $process = new GetContainerStatusProcess($container, $project);
      if (stristr($this->getProcessRunner()->run($process), 'running') === FALSE) {
        return;
      }
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }

    try {
      $process = new StopProjectBranchContainerProcess($container, $project);
      $this->getProcessRunner()->run($process);
      $container->setState(Container::STATE_STOPPED);
      $this->getEntityManager()->getRepository('Rainmaker:Container')->saveContainer($container);
    } catch (ProcessFailedException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Extracts the value of setting from a Linux container configuration file.
   *
   * @param $setting
   * @return null
   */
  protected function getLxcConfigurationSettingValue($setting)
  {
    $file = '/var/lib/lxc/' . $this->getContainer()->getName() . '/config';
    if ($this->getContainer()->isProjectBranch()) {
      $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($this->getContainer());
      $file = '/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $this->getContainer()->getName() . '/config';
    }
    $contents = $this->getFilesystem()->getFileContents($file);
    $matches = array();
    if (preg_match('/\s*' . $setting . '\s*=\s*(.+)\s*/', $contents, $matches) !== 1) {
      return null;
    }

    return $matches[1];
  }

  /**
   * Writes the configuration file for the Rainmaker project Linux container to the filesystem.
   */
  protected function writeProjectLxcConfigurationFile()
  {
    $config = Template::render('lxc/project-config.twig', array(
      'lxc_root_fs'       => $this->getContainer()->getLxcRootFs(),
      'lxc_utsname'       => $this->getContainer()->getLxcUtsName(),
      'lxc_net_hwaddr'    => $this->getContainer()->getLxcHwAddr(),
    ));

    $file = '/var/lib/lxc/' . $this->getContainer()->getName() . '/config';
    $this->getFilesystem()->putFileContents($file, $config);
  }

  /**
   * Writes the configuration file for the Rainmaker project branch Linux container to the filesystem.
   */
  protected function writeProjectBranchLxcConfigurationFile()
  {
    $config = Template::render('lxc/project-branch-config.twig', array(
      'lxc_root_fs'       => $this->getContainer()->getLxcRootFs(),
      'lxc_utsname'       => $this->getContainer()->getLxcUtsName(),
      'lxc_net_hwaddr'    => $this->getContainer()->getLxcHwAddr(),
    ));

    $project = $this->getEntityManager()->getRepository('Rainmaker:Container')->getParentContainer($this->getContainer());

    $file = '/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $this->getContainer()->getName() . '/config';
    $this->getFilesystem()->putFileContents($file, $config);
  }

}
