<?php

namespace Rainmaker\Tests\Integration\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContainerRepositoryTest extends WebTestCase
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

  public function testGetProjectParentContainers()
  {
    $this->assertCount(4, $this->contRepo->getProjectParentContainers());
  }

  public function testGetProjectBranchContainers()
  {
    $container = $this->contRepo->findOneByName('test4');
    $this->assertCount(3, $this->contRepo->getProjectBranchContainers($container));
  }

  public function testGetAllParentContainers()
  {
    $this->assertCount(4, $this->contRepo->getAllParentContainers());
  }

  public function testGetAllContainersOrderedForHostsInclude()
  {
    $containers = $this->contRepo->getAllContainersOrderedForHostsInclude();
    $this->assertCount(10, $containers);
    $lastContainer = '';
    foreach ($containers as $container) {
      $this->assertLessThanOrEqual($container->getName(), $lastContainer);
      $lastContainer = $container->getName();
    }
  }

  public function testGetNetworkHostAddrRangeMin()
  {
    $project = $this->contRepo->findOneByName('test4');
    $this->assertEquals('10.100.4.1', $this->contRepo->getNetworkHostAddrRangeMin($project));
  }

  public function testGetNetworkHostAddrRangeMax()
  {
    $project = $this->contRepo->findOneByName('test4');
    $this->assertEquals('10.100.4.254', $this->contRepo->getNetworkHostAddrRangeMax($project));
  }

  public function testGetPrimaryNameServers()
  {
    $project = $this->contRepo->findOneByName('test4');
    $this->assertEquals(
      array(
        'ns.rainmaker.localdev',
        'ns.test4.localdev'
      ),
      $this->contRepo->getPrimaryNameServers($project)
    );
  }

  public function testGetNameServerRecords()
  {
    $project = $this->contRepo->findOneByName('test3');
    $this->assertEquals(
      array(
        array(
          'hostname'  => 'ns.rainmaker.localdev.',
          'ipAddress' => '10.100.0.2',
        ),
        array(
          'hostname'  => 'ns',
          'ipAddress' => '10.100.0.2',
        )
      ),
      $this->contRepo->getNameServerRecords($project)
    );
  }

  public function testGetDnsRecordsForProjectContainer()
  {
    $project = $this->contRepo->findOneByName('test4');
    $this->assertEquals(
      array(
        array(
          'hostname'  => 'cluster',
          'ipAddress' => '10.100.4.1',
        ),
        array(
          'hostname'  => '@',
          'ipAddress' => '10.100.4.2',
        ),
        array(
          'hostname'  => 'dev',
          'ipAddress' => '10.100.4.3',
        ),
        array(
          'hostname'  => 'fet001',
          'ipAddress' => '10.100.4.4',
        )
      ),
      $this->contRepo->getDnsRecordsForProjectContainer($project)
    );
  }

  public function testGetDnsPtrRecordsForProjectContainer()
  {
    $project = $this->contRepo->findOneByName('test4');
    $this->assertEquals(
      array(
        array(
          'hostname'  => 'cluster.test4.localdev.',
          'ipAddress' => '1',
        ),
        array(
          'hostname'  => 'test4.localdev.',
          'ipAddress' => '2',
        ),
        array(
          'hostname'  => 'dev.test4.localdev.',
          'ipAddress' => '3',
        ),
        array(
          'hostname'  => 'fet001.test4.localdev.',
          'ipAddress' => '4',
        )
      ),
      $this->contRepo->getDnsPtrRecordsForProjectContainer($project)
    );
  }

}
