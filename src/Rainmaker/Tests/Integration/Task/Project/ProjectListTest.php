<?php

namespace Rainmaker\Tests\Integration\Task\Project;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Rainmaker\Entity\Container;
use Rainmaker\Task\Project\ProjectList;
use Rainmaker\Tests\Unit\Mock\FilesystemMock;
use Rainmaker\Tests\Unit\Mock\ProcessRunnerMock;
use Rainmaker\Logger\TaskLogger;

/**
 * Unit tests \Rainmaker\Task\Project\List
 *
 * @package Rainmaker\Tests\Integration\Task\Project
 */
class ListTest extends WebTestCase
{

  protected $em;
  protected $contRepo;

  protected function setUp()
  {
    error_reporting(E_ALL);
    static::bootKernel();
    $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    $this->contRepo = $this->em->getRepository('Rainmaker:Container');
  }

  public function testList()
  {
    $task = new ProjectList();
    $task->setLogger($this->createLogger())
      ->setEntityManager($this->em)
      ->setProcessRunner($this->createProcessRunnerMock())
      ->setFilesystem($this->createFilesystemMock());
    $task->performTask();

    $pathToTestCheckFiles = dirname(__FILE__) . '/../../../..';
    $this->assertEquals(file_get_contents($pathToTestCheckFiles . '/Resources/tests/project-list.txt'), $task->getList());
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
