<?php

namespace Rainmaker\Tests\Task\Project;

use Rainmaker\Entity\Container;
use Rainmaker\Task\Project\Create;
use Rainmaker\Tests\Mock\FilesystemMock;
use Rainmaker\Tests\Mock\ProcessRunnerMock;

/**
 * Unit tests \Rainmaker\Task\Project\Create
 *
 * @package Rainmaker\Tests\Task\Project
 */
class CreateTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Tests the successful creation of a new Rainmaker project Linux container
   */
  public function testCreate()
  {
    $project = $this->createDummyProject();
    $task = new Create();
    $task->setContainer($project);

    $processRunnerMock = $this->createProcessRunnerMock();
    $task->setProcessRunner($processRunnerMock);

    $filesystemMock = $this->createFilesystemMock();
    $task->setFilesystem($filesystemMock);

    $task->performTask();

    $this->assertEquals('test', $task->getContainer()->getName());
    $this->assertEquals('Test', $task->getContainer()->getFriendlyName());
    $this->assertEquals('test', $task->getContainer()->getLxcUtsName());
    $this->assertEquals('00:16:3e:e0:5c:c3', $task->getContainer()->getLxcHwAddr());
    $this->assertEquals('/var/lib/lxc/test/rootfs', $task->getContainer()->getLxcRootFs());

    $this->assertEquals(file_get_contents(dirname(__FILE__) . '/../../../Resources/tests/test_lxc_config'), $filesystemMock->getFileContents('/var/lib/lxc/' . $task->getContainer()->getName() . '/config'));
  }

  protected function createDummyProject()
  {
    $container = new Container();
    $container
      ->setName('test')
      ->setFriendlyName('Test');
    return $container;
  }

  protected function createProcessRunnerMock()
  {
    return new ProcessRunnerMock();
  }

  protected function createFilesystemMock()
  {
    $fs = new FilesystemMock();
    $fs->copyFromFileSystem(__DIR__ . '/../../fsMocks');

    return $fs;
  }

}
