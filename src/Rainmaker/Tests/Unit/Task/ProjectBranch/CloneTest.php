<?php

namespace Rainmaker\Tests\Unit\Task\ProjectBranch;

use Rainmaker\Tests\AbstractUnitTest;
use Rainmaker\Entity\Container;
use Rainmaker\Task\ProjectBranch\CreateClone;
use Rainmaker\Tests\Unit\Mock\EntityManagerMock;
use Rainmaker\Tests\Unit\Mock\FilesystemMock;
use Rainmaker\Tests\Unit\Mock\ProcessRunnerMock;
use Rainmaker\Logger\TaskLogger;

/**
 * Unit tests \Rainmaker\Task\Project\Clone
 *
 * @package Rainmaker\Tests\Unit\Task\ProjectBranch
 */
class CreateTest extends AbstractUnitTest
{

  /**
   * Tests the successful creation of a new Rainmaker project Linux container.
   */
  public function testCloneProjectBranch()
  {
    $pathToTestAcceptanceFiles = $this->getPathToTestAcceptanceFilesDirectory() . '/cloneProjectBranch';

    $project = $this->createDummyProject();
    $sourceBranch = $this->createDummyProjectBranch();
    $cloneBranch = $this->createDummyProjectBranchClone()
      ->setCloneSource($sourceBranch);

    $task = new CreateClone();
    $task->setContainer($cloneBranch);

    $entityManagerMock = $this->createEntityManagerMock(array($project), array($sourceBranch, $cloneBranch), $project);
    $task->setEntityManager($entityManagerMock);

    $processRunnerMock = $this->createProcessRunnerMock();
    $task->setProcessRunner($processRunnerMock);

    $filesystemMock = $this->createFilesystemMock();
    $task->setFilesystem($filesystemMock);

    $logger = $this->createLogger();
    $task->setLogger($logger);

    $task->performTask();

    // Check container configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/lxc_config'), $filesystemMock->getFileContents('/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $cloneBranch->getName() . '/config'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/hostname'), $filesystemMock->getFileContents('/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $cloneBranch->getName() . '/rootfs/etc/hostname'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/hosts'), $filesystemMock->getFileContents('/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $cloneBranch->getName() . '/rootfs/etc/hosts'));

    ;
  }


  // Utility methods


  protected function getPathToTestAcceptanceFilesDirectory()
  {
    return $this->getPathToTestAcceptanceFilesBaseDirectory() . '/unit/projectBranch';
  }

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
      ->setState(Container::STATE_STOPPED);
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
      ->setState(Container::STATE_STOPPED)
      ->setParentId(1);
    return $container;
  }

  protected function createDummyProjectBranchClone()
  {
    $container = new Container();
    $container
      ->setName('test.dev')
      ->setFriendlyName('Test [Dev]')
      ->setHostname('develop.test.localdev')
      ->setDomain('test.localdev')
      ->setDnsZoneSerial('2015070501')
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
