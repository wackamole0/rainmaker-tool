<?php

namespace Rainmaker\Tests\Unit\Task\ProjectBranch;

use Rainmaker\Tests\AbstractUnitTest;
use Rainmaker\Entity\Container;
use Rainmaker\Task\ProjectBranch\Stop;
use Rainmaker\Tests\Unit\Mock\EntityManagerMock;
use Rainmaker\Tests\Unit\Mock\FilesystemMock;
use Rainmaker\Tests\Unit\Mock\ProcessRunnerMock;
use Rainmaker\Logger\TaskLogger;

/**
 * Unit tests \Rainmaker\Task\ProjectBranch\Stop
 *
 * @package Rainmaker\Tests\Unit\Task\ProjectBranch
 */
class StopTest extends AbstractUnitTest
{

  /**
   * Tests the stopping of a Rainmaker project branch Linux container.
   */
  public function testStopProjectBranch()
  {
    $project = $this->createDummyProject();
    $branch = $this->createDummyProjectBranch();

    $task = new Stop();
    $task->setContainer($branch);

    $entityManagerMock = $this->createEntityManagerMock(array($project), array($branch), $project);
    $task->setEntityManager($entityManagerMock);

    $processRunnerMock = $this->createProcessRunnerMock();
    $processRunnerMock->addProcessOutput('Rainmaker\Process\Lxc\GetContainerStatusProcess', 'running');
    $task->setProcessRunner($processRunnerMock);

    $filesystemMock = $this->createFilesystemMock();
    $task->setFilesystem($filesystemMock);

    $logger = $this->createLogger();
    $task->setLogger($logger);

    $task->performTask();
  }


  // Utility methods


  protected function createDummyProject()
  {
    $container = new Container();
    $container
      ->setName('test')
      ->setFriendlyName('Test')
      ->setHostname('cluster.test.localdev')
      ->setDomain('test.localdev')
      ->setDnsZoneSerial('2015070501')
      ->setLxcUtsName('test')
      ->setLxcHwAddr('00:16:3e:e0:5c:c3')
      ->setLxcRootFs('/var/lib/lxc/test/rootfs')
      ->setNetworkAddress('10.100.1.0')
      ->setIPAddress('10.100.1.1')
      ->setDnsZoneTtl(604800)
      ->setDnsZonePriMasterNs('ns.rainmaker.localdev')
      ->setDnsZoneAdminEmail('hostmaster.rainmaker.localdev')
      ->setDnsZoneRefresh(604800)
      ->setDnsZoneRetry(86400)
      ->setDnsZoneExpire(2419200)
      ->setDnsZoneNegCacheTtl(604800)
      ->setState(Container::STATE_RUNNING)
      ->setProfileName('rainmaker/default-project');
    return $container;
  }

  protected function createDummyProjectBranch()
  {
    $container = new Container();
    $container
      ->setName('test.prod')
      ->setFriendlyName('Test [Prod]')
      ->setHostname('test.localdev')
      ->setDomain('test.localdev')
      ->setDnsZoneSerial('2015070501')
      ->setLxcUtsName('test.prod')
      ->setLxcHwAddr('00:16:3e:e0:5c:c4')
      ->setLxcRootFs('/var/lib/lxc/test.prod/rootfs')
      ->setNetworkAddress('10.100.1.0')
      ->setIPAddress('10.100.1.2')
      ->setDnsZoneTtl(604800)
      ->setDnsZonePriMasterNs('ns.rainmaker.localdev')
      ->setDnsZoneAdminEmail('hostmaster.rainmaker.localdev')
      ->setDnsZoneRefresh(604800)
      ->setDnsZoneRetry(86400)
      ->setDnsZoneExpire(2419200)
      ->setDnsZoneNegCacheTtl(604800)
      ->setState(Container::STATE_RUNNING)
      ->setProfileName('rainmaker/default-branch')
      ->setParentId(1);
    return $container;
  }

  protected function createEntityManagerMock(array $projects = array(), array $branches = array(), Container $parent = null)
  {
    $em = new EntityManagerMock();
    $repository = $em->getRepository('Rainmaker:Container');
    $repository->projectContainers = $projects;
    $repository->branchContainers = $branches;
    $repository->allBranchContainers = $branches;
    $repository->allContainersOrderedForHostsInclude = array_merge($projects, $branches);
    $repository->parentContainer = $parent;
    return $em;
  }

  protected function createProcessRunnerMock()
  {
    return new ProcessRunnerMock();
  }

  protected function createFilesystemMock()
  {
    $fs = new FilesystemMock();
    $fs->copyFromFileSystem(__DIR__ . '/../../../fsMocks');

    return $fs;
  }

  protected function createLogger()
  {
    return new TaskLogger('testLogger');
  }

}
