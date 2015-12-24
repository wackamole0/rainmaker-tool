<?php

namespace Rainmaker\Tests\Unit\Task\Project;

use Rainmaker\Tests\AbstractUnitTest;
use Rainmaker\Entity\Container;
use Rainmaker\Task\Project\Destroy;
use Rainmaker\Tests\Unit\Mock\EntityManagerMock;
use Rainmaker\Tests\Unit\Mock\FilesystemMock;
use Rainmaker\Tests\Unit\Mock\ProcessRunnerMock;
use Rainmaker\Logger\TaskLogger;

/**
 * Unit tests \Rainmaker\Task\Project\Destroy
 *
 * @package Rainmaker\Tests\Unit\Task\Project
 */
class DestroyTest extends AbstractUnitTest
{

  /**
   * Tests the destroying of a Rainmaker project Linux container.
   */
  public function testDestroyProject()
  {
    $pathToTestAcceptanceFiles = $this->getPathToTestAcceptanceFilesDirectory() . '/destroyProject';

    $project = $this->createDummyProject();
    $branches = array();

    $task = new Destroy();
    $task->setContainer($project);

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
    $filesystemMock->touch('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf.d/localdev.test.conf');
    $filesystemMock->touch('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf.d/localdev.test.cluster.conf');
    $filesystemMock->putFileContents('/etc/fstab', '');

    $task->performTask();

    // Check Fstab configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/fstab'), $filesystemMock->getFileContents('/etc/fstab'));

    // Check DNS configuration

    $this->assertFalse($filesystemMock->exists('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.test.localdev'));
    $this->assertFalse($filesystemMock->exists('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.10.100.1'));

    // Check DHCP configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/subnet_10.100.0.0.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.subnet.conf.d/10.100.0.0.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/dhcpd.class.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf'));
    $this->assertFalse($filesystemMock->exists('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf.d/localdev.test.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/dhcpd.host.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf'));
    $this->assertFalse($filesystemMock->exists('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf.d/localdev.test.cluster.conf'));
  }

  /**
   * Tests the destroying of a Rainmaker project Linux container that is running.
   *
   * @expectedException \Rainmaker\RainmakerException
   */
  public function testDestroyRunningProject() {
    $project = $this->createDummyProject();
    $project->setState(Container::STATE_RUNNING);
    $branches = $this->createDummyProjectBranches();

    $task = new Destroy();
    $task->setContainer($project);

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

  /**
   * Tests the destroying of a Rainmaker project Linux container that has project branches configured.
   *
   * @expectedException \Rainmaker\RainmakerException
   */
  public function testDestroyProjectWithProjectBranches() {
    $project = $this->createDummyProject();
    $branches = $this->createDummyProjectBranches();

    $task = new Destroy();
    $task->setContainer($project);

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
    return $this->getPathToTestAcceptanceFilesBaseDirectory() . '/unit/project';
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
      ->setState(Container::STATE_STOPPED)
      ->setProfileName('rainmaker/default-project');

    $json = '
{
  "mounts": [
    {
      "source": "/var/cache/lxc/rainmaker",
      "target": "{{container_rootfs}}/var/cache/lxc/rainmaker",
      "group": "bind"
    },
    {
      "source": "/srv/saltstack",
      "target": "{{container_rootfs}}/srv/saltstack",
      "group": "bind"
    }
  ],
  "exports": []
}
';

    $container->setProfileMetadata($json);
    return $container;
  }

  protected function createDummyProjectBranches()
  {
    $json = '
{
  "mounts": [
    {
      "source": "{{container_rootfs}}/var/www/html",
      "target": "/export/rainmaker/{{container_name}}",
      "group": "nfs"
    },
    {
      "source": "/srv/saltstack",
      "target": "{{container_rootfs}}/srv/saltstack",
      "group": "bind"
    }
  ],
  "exports": [
    {
      "source": "/export/rainmaker/{{container_name}}"
    }
  ]
}
';

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
      ->setState(Container::STATE_STOPPED)
      ->setProfileName('rainmaker/default-branch')
      ->setProfileMetadata($json)
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
      ->setProfileName('rainmaker/default-branch')
      ->setProfileMetadata($json)
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
    $repository->allContainersOrderedByName = array_merge($projects, $branches);
    $repository->allContainersOrderedForHostsInclude = $repository->allContainersOrderedByName;
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
