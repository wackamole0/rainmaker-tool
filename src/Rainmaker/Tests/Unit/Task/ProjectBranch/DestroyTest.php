<?php

namespace Rainmaker\Tests\Unit\Task\ProjectBranch;

use Rainmaker\Tests\AbstractUnitTest;
use Rainmaker\Entity\Container;
use Rainmaker\Task\ProjectBranch\Destroy;
use Rainmaker\Tests\Unit\Mock\EntityManagerMock;
use Rainmaker\Tests\Unit\Mock\FilesystemMock;
use Rainmaker\Tests\Unit\Mock\ProcessRunnerMock;
use Rainmaker\Logger\TaskLogger;

/**
 * Unit tests \Rainmaker\Task\ProjectBranch\Destroy
 *
 * @package Rainmaker\Tests\Unit\Task\ProjectBranch
 */
class DestroyTest extends AbstractUnitTest
{

  /**
   * Tests the destroying of a Rainmaker project branch Linux container.
   */
  public function testDestroyProjectBranch()
  {
    $pathToTestAcceptanceFiles = $this->getPathToTestAcceptanceFilesDirectory() . '/destroyProjectBranch';

    $project = $this->createDummyProject();
    $branches = $this->createDummyProjectBranches();

    $task = new Destroy();
    $task->setContainer($branches[1]);

    $entityManagerMock = $this->createEntityManagerMock(array($project), $branches, $project);
    $task->setEntityManager($entityManagerMock);

    $processRunnerMock = $this->createProcessRunnerMock();
    $task->setProcessRunner($processRunnerMock);

    $filesystemMock = $this->createFilesystemMock();
    $task->setFilesystem($filesystemMock);

    $logger = $this->createLogger();
    $task->setLogger($logger);

    $filesystemMock->touch('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $project->getDomain());
    $filesystemMock->touch('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.' . $project->networkPrefix());
    foreach ($branches as $branch) {
      $filesystemMock->mkdir('/export/rainmaker/' . $branch->getName());
    }
    $filesystemMock->touch('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf.d/localdev.test.develop.conf');

    $task->performTask();

    // Check NFS configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/exports'), $filesystemMock->getFileContents('/etc/exports'));

    // Check Fstab configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/fstab'), $filesystemMock->getFileContents('/etc/fstab'));
    $this->assertTrue(!$filesystemMock->exists('/export/rainmaker/test.dev'));

    // Check DNS configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/db.test.localdev'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.test.localdev'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/db.10.100.1'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.10.100.1'));
    $this->assertTrue(!$filesystemMock->exists('/export/rainmaker/test.dev'));

    // Check DHCP configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/dhcpd.host.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf'));
    $this->assertFalse($filesystemMock->exists('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf.d/localdev.test.develop.conf'));
  }

  /**
   * Tests the destroying of a Rainmaker project branch Linux container that is running.
   *
   * @expectedException \Rainmaker\RainmakerException
   */
  public function testDestroyRunningProjectBranch() {
    $project = $this->createDummyProject();
    $branches = $this->createDummyProjectBranches();
    foreach ($branches as $branch) {
      if ($branch->getName() == 'test.dev') {
        $branch->setState(Container::STATE_RUNNING);
      }
    }

    $task = new Destroy();
    $task->setContainer($branches[1]);

    $entityManagerMock = $this->createEntityManagerMock(array($project), $branches, $project);
    $task->setEntityManager($entityManagerMock);

    $processRunnerMock = $this->createProcessRunnerMock();
    $task->setProcessRunner($processRunnerMock);

    $filesystemMock = $this->createFilesystemMock();
    $task->setFilesystem($filesystemMock);

    $logger = $this->createLogger();
    $task->setLogger($logger);

    $task->performTask();
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
      ->setState(Container::STATE_RUNNING);
    return $container;
  }

  protected function createDummyProjectBranches()
  {
    $containers = array();

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
      ->setParentId(1);
    $containers[] = $container;

    $container = new Container();
    $container
      ->setName('test.dev')
      ->setFriendlyName('Test [Dev]')
      ->setHostname('develop.test.localdev')
      ->setDomain('test.localdev')
      ->setDnsZoneSerial('2015070501')
      ->setLxcUtsName('test.dev')
      ->setLxcHwAddr('00:16:3e:e0:5c:c5')
      ->setLxcRootFs('/var/lib/lxc/test.dev/rootfs')
      ->setNetworkAddress('10.100.1.0')
      ->setIPAddress('10.100.1.3')
      ->setDnsZoneTtl(604800)
      ->setDnsZonePriMasterNs('ns.rainmaker.localdev')
      ->setDnsZoneAdminEmail('hostmaster.rainmaker.localdev')
      ->setDnsZoneRefresh(604800)
      ->setDnsZoneRetry(86400)
      ->setDnsZoneExpire(2419200)
      ->setDnsZoneNegCacheTtl(604800)
      ->setState(Container::STATE_STOPPED)
      ->setParentId(1);
    $containers[] = $container;

    return $containers;
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
