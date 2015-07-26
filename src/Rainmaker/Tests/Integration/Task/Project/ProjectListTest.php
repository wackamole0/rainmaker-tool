<?php

namespace Rainmaker\Tests\Integration\Task\Project;

use Rainmaker\Tests\AbstractIntegrationTest;
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
class ListTest extends AbstractIntegrationTest
{

  public function testList()
  {
    $task = new ProjectList();
    $task->setLogger($this->createLogger())
      ->setEntityManager($this->em)
      ->setProcessRunner($this->createProcessRunnerMock())
      ->setFilesystem($this->createFilesystemMock());
    $task->performTask();

    $this->assertEquals(file_get_contents($this->getPathToTestAcceptanceFilesDirectory() . '/project-list.txt'),
      $task->getList());
  }


  // Utility methods


  protected function getPathToTestAcceptanceFilesDirectory()
  {
    return $this->getPathToTestAcceptanceFilesBaseDirectory() . '/integration/project/list';
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
