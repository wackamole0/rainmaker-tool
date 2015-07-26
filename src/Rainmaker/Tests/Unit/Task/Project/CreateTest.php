<?php

namespace Rainmaker\Tests\Unit\Task\Project;

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
class CreateTest extends \PHPUnit_Framework_TestCase
{

  protected function setUp()
  {
    error_reporting(E_ALL);
  }

  /**
   * Tests the successful creation of a new Rainmaker project Linux container
   */
  public function testCreate()
  {
    $project = $this->createDummyProject();
    $task = new Create();
    $task->setContainer($project);

    $entityManagerMock = $this->createEntityManagerMock(array($project));
    $task->setEntityManager($entityManagerMock);

    $processRunnerMock = $this->createProcessRunnerMock();
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

    $pathToTestCheckFiles = dirname(__FILE__) . '/../../../..';
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/lxc/lxc_config'), $filesystemMock->getFileContents('/var/lib/lxc/' . $task->getContainer()->getName() . '/config'));
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/lxc/hostname'), $filesystemMock->getFileContents('/var/lib/lxc/' . $task->getContainer()->getName() . '/rootfs/etc/hostname'));
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/lxc/hosts'), $filesystemMock->getFileContents('/var/lib/lxc/' . $task->getContainer()->getName() . '/rootfs/etc/hosts'));

    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/dhcp/host_localdev.test.cluster.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf.d/localdev.test.cluster.conf'));
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/dhcp/dhcpd.host.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.host.conf'));
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/dhcp/class_localdev.test.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf.d/localdev.test.conf'));
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/dhcp/dhcpd.class.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.class.conf'));
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/dhcp/subnet_10.100.0.0.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/dhcp/dhcpd.subnet.conf.d/10.100.0.0.conf'));

    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/bind/db.test.localdev'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.test.localdev'));
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/bind/db.10.100.1'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/db.rainmaker/db.10.100.1'));
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/bind/named.test.conf'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/named.conf.rainmaker/test.conf'));
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/bind/named.conf.local'), $filesystemMock->getFileContents('/var/lib/lxc/services/rootfs/etc/bind/named.conf.local'));

    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/fstab'), $filesystemMock->getFileContents('/etc/fstab'));
  }

  protected function createDummyProject()
  {
    $container = new Container();
    $container
      ->setName('test')
      ->setFriendlyName('Test')
      ->setHostname('cluster.test.localdev')
      ->setDomain('test.localdev')
      ->setDnsZoneSerial('2015070501');
    return $container;
  }

  protected function createEntityManagerMock(array $containers)
  {
    $em = new EntityManagerMock();
    $repository = $em->getRepository('Rainmaker:Container');
    $repository->allParentContainers = $containers;
    $repository->allContainersOrderedForHostsInclude = $containers;
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
