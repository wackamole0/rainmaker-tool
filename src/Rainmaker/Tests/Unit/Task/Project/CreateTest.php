<?php

namespace Rainmaker\Tests\Unit\Task\Project;

use Rainmaker\Tests\AbstractUnitTest;
use Rainmaker\Entity\Container;
use Rainmaker\Task\Project\Create;
use Rainmaker\Tests\Unit\Mock\EntityManagerMock;
use Rainmaker\Tests\Unit\Mock\FilesystemMock;
use Rainmaker\Tests\Unit\Mock\ProcessRunnerMock;
use Rainmaker\Logger\TaskLogger;

/**
 * Unit tests \Rainmaker\Task\Project\Create
 *
 * @package Rainmaker\Tests\Unit\Task\Project
 */
class CreateTest extends AbstractUnitTest
{

  /**
   * Tests the successful creation of a new Rainmaker project Linux container.
   *
   * @group mytest
   */
  public function testCreateProject()
  {
    $pathToTestAcceptanceFiles = $this->getPathToTestAcceptanceFilesDirectory() . '/createProject';

    $project = $this->createDummyProject();
    $task = new Create();
    $task->setContainer($project);

    $entityManagerMock = $this->createEntityManagerMock(array($project), array(), $project);
    $task->setEntityManager($entityManagerMock);

    $processRunnerMock = $this->createProcessRunnerMock();
    $processRunnerMock->addProcessOutput('Rainmaker\Process\RainmakerProfileManager\GetLatestProfileVersion', '1.0');
    $processRunnerMock->addProcessOutput('Rainmaker\Process\RainmakerProfileManager\GetProfileMetadata', '{"mounts":[{"source":"\/var\/cache\/lxc\/rainmaker","target":"{{container_rootfs}}\/var\/cache\/lxc\/rainmaker","group":"bind"},{"source":"\/srv\/saltstack","target":"{{container_rootfs}}\/srv\/saltstack","group":"bind"}],"exports":[]}');
    $task->setProcessRunner($processRunnerMock);

    $filesystemMock = $this->createFilesystemMock();
    $task->setFilesystem($filesystemMock);

    $logger = $this->createLogger();
    $task->setLogger($logger);

    $task->performTask();

    //var_dump($logger->getLogBufferContents());

    $this->assertEquals('test', $task->getContainer()->getName());
    $this->assertEquals('Test', $task->getContainer()->getFriendlyName());
    $this->assertEquals('test', $task->getContainer()->getLxcUtsName());
    $this->assertEquals('00:16:3e:e0:5c:c3', $task->getContainer()->getLxcHwAddr());
    $this->assertEquals('/var/lib/lxc/test/rootfs', $task->getContainer()->getLxcRootFs());

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/lxc_config'), $filesystemMock->getFileContents('/var/lib/lxc/' . $task->getContainer()->getName() . '/config'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/hostname'), $filesystemMock->getFileContents('/var/lib/lxc/' . $task->getContainer()->getName() . '/rootfs/etc/hostname'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/hosts'), $filesystemMock->getFileContents('/var/lib/lxc/' . $task->getContainer()->getName() . '/rootfs/etc/hosts'));

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/host_localdev.test.cluster.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf.d/localdev.test.cluster.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/dhcpd.host.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/class_localdev.test.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf.d/localdev.test.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/dhcpd.class.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/subnet_10.100.0.0.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.subnet.conf.d/10.100.0.0.conf'));

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/db.test.localdev'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.test.localdev'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/db.10.100.1'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.10.100.1'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/named.test.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/named.conf.rainmaker/test.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/named.conf.local'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/named.conf.local'));

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/fstab'), $filesystemMock->getFileContents('/etc/fstab'));

    $this->assertEquals(Container::STATE_STOPPED, $project->getState());
  }

  /**
   * Tests the successful creation of a new Rainmaker project Linux container with branch container.
   *
   * @group mytest
   */
  public function testCreateProjectAndBranch()
  {
    $pathToTestAcceptanceFiles = $this->getPathToTestAcceptanceFilesDirectory() . '/createProjectAndBranch';

    $project = $this->createDummyProject();
    $branch = $this->createDummyProjectBranch();

    $task = new Create();
    $task->setContainer($project);
    $task->setBranchContainer($branch);

    $entityManagerMock = $this->createEntityManagerMock(array($project), array($branch), $project);
    $task->setEntityManager($entityManagerMock);

    $processRunnerMock = $this->createProcessRunnerMock();
    $processRunnerMock->addProcessOutput('Rainmaker\Process\RainmakerProfileManager\GetLatestProfileVersion', '1.0');
    $processRunnerMock->addProcessOutput('Rainmaker\Process\RainmakerProfileManager\GetProfileMetadata', '{"mounts":[{"source":"\/var\/cache\/lxc\/rainmaker","target":"{{container_rootfs}}\/var\/cache\/lxc\/rainmaker","group":"bind"},{"source":"\/srv\/saltstack","target":"{{container_rootfs}}\/srv\/saltstack","group":"bind"}],"exports":[]}');
    $task->setProcessRunner($processRunnerMock);

    $filesystemMock = $this->createFilesystemMock();
    $task->setFilesystem($filesystemMock);

    $logger = $this->createLogger();
    $task->setLogger($logger);

    $task->performTask();

    // Check container configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/project_lxc_config'), $filesystemMock->getFileContents('/var/lib/lxc/' . $project->getName() . '/config'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/project_hostname'), $filesystemMock->getFileContents('/var/lib/lxc/' . $task->getContainer()->getName() . '/rootfs/etc/hostname'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/project_hosts'), $filesystemMock->getFileContents('/var/lib/lxc/' . $task->getContainer()->getName() . '/rootfs/etc/hosts'));

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/branch_lxc_config'), $filesystemMock->getFileContents('/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $branch->getName() . '/config'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/branch_hostname'), $filesystemMock->getFileContents('/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $branch->getName() . '/rootfs/etc/hostname'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/lxc/branch_hosts'), $filesystemMock->getFileContents('/var/lib/lxc/' . $project->getName() . '/rootfs/var/lib/lxc/' . $branch->getName() . '/rootfs/etc/hosts'));


    // Check DHCP configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/host_localdev.test.cluster.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf.d/localdev.test.cluster.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/host_localdev.test.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf.d/localdev.test.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/dhcpd.host.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/class_localdev.test.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf.d/localdev.test.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/dhcpd.class.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/dhcp/subnet_10.100.0.0.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.subnet.conf.d/10.100.0.0.conf'));

    // Check DNS configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/db.test.localdev'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.test.localdev'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/db.10.100.1'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.10.100.1'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/named.test.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/named.conf.rainmaker/test.conf'));
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/bind/named.conf.local'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/named.conf.local'));

    // Check Fstab configuration

    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/fstab'), $filesystemMock->getFileContents('/etc/fstab'));
    $this->assertTrue($filesystemMock->exists('/export/rainmaker/test.prod'));

    // Check NFS configuration
    $this->assertEquals(file_get_contents($pathToTestAcceptanceFiles . '/exports'), $filesystemMock->getFileContents('/etc/exports'));

    $this->assertEquals(Container::STATE_STOPPED, $project->getState());
    $this->assertEquals(Container::STATE_STOPPED, $branch->getState());
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
      ->setState(Container::STATE_PENDING_PROVISIONING)
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

    //$container->setProfileMetadata($json);
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
      ->setState(Container::STATE_PENDING_PROVISIONING)
      ->setProfileName('rainmaker/default-branch')
      ->setParentId(1);

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

    $container->setProfileMetadata($json);
    return $container;
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
