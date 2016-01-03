<?php

namespace Rainmaker\Tests\Unit\Task\Project;

use Rainmaker\Tests\AbstractUnitTest;
use Rainmaker\Entity\Container;
use Rainmaker\Task\Project\Start;
use Rainmaker\Tests\Unit\Mock\EntityManagerMock;
use Rainmaker\Tests\Unit\Mock\FilesystemMock;
use Rainmaker\Tests\Unit\Mock\ProcessRunnerMock;
use Rainmaker\Logger\TaskLogger;

/**
 * Unit tests \Rainmaker\Task\Project\Start
 *
 * @package Rainmaker\Tests\Unit\Task\Project
 */
class StartTest extends AbstractUnitTest
{

    /**
     * Tests the starting of a Rainmaker project Linux container.
     */
    public function testStartProject()
    {
        $project = $this->createDummyProject();

        $task = new Start();
        $task->setContainer($project);

        $entityManagerMock = $this->createEntityManagerMock(array($project), array(), $project);
        $task->setEntityManager($entityManagerMock);

        $processRunnerMock = $this->createProcessRunnerMock();
        $processRunnerMock->addProcessOutput('Rainmaker\Process\Lxc\GetContainerStatusProcess', 'stopped');
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
