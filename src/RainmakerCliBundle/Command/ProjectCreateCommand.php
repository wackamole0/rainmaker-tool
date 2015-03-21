<?php

namespace RainmakerCliBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rainmaker\Entity\ContainerRepository;

class ProjectCreateCommand extends RainmakerCommand
{

  protected function configure()
  {
    parent::configure();

    $this
      ->setName('project:create')
      ->setDescription('Create a new project')
      ->addArgument(
        'name',
        InputArgument::OPTIONAL,
        'The friendly name of the new project'
      );
  }

  public function task()
  {
    return new \Rainmaker\Task\Project\Create();
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    //$output->writeln('project:create');
    //print_r($this->getContainer());

    /*
     * The code below is a good example of presenting questions to the user when required command parameters are
     * missing and running interactively
     */
//    $projectId = $input->getArgument('id');
//    if (empty($projectId)) {
//      if ($input->isInteractive() && ($projects = $this->getProjects(true))) {
//        $projectId = $this->offerProjectChoice($projects, $input, $output);
//      }
//      else {
//        $output->writeln("<error>You must specify a project.</error>");
//        return 1;
//      }
//    }
//    $project = $this->getProject($projectId);
//    if (!$project) {
//      $output->writeln("<error>Project not found: $projectId</error>");
//      return 1;
//    }

    $friendlyName = $input->getArgument('name');
    if (!empty($friendlyName)) {
      // Use console helper to ask for friendly name
    }

    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');
    $name = $repository->friendlyNameToContainerName($friendlyName);
    $this->setContainerEntity($repository->createContainer($name, $friendlyName));
    print_r($this->getContainerEntity());
//    $this->getTask()->setContainerEntity($this->getContainerEntity());
//    parent::execute($input, $output);
  }

}
