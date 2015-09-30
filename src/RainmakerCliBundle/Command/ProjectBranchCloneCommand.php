<?php

namespace RainmakerCliBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rainmaker\Entity\ContainerRepository;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class ProjectBranchCloneCommand extends RainmakerCommand
{

  protected function configure()
  {
    parent::configure();

    $this
      ->setName('project:branch:clone')
      ->setDescription('Clone an existing project branch')
      ->addArgument(
        'name',
        InputArgument::OPTIONAL,
        'The unique name of the existing project branch'
      )
      ->addArgument(
        'newname',
        InputArgument::OPTIONAL,
        'The unique name of the new project branch'
      )
      ->addOption(
        'fname',
        null,
        InputOption::VALUE_REQUIRED,
        'The friendly name of the new project branch'
      )
      ->addOption(
        'hostname',
        null,
        InputOption::VALUE_REQUIRED,
        'The host name of the new project branch'
      );
  }

  public function task()
  {
    return new \Rainmaker\Task\ProjectBranch\CreateClone();
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');

    $uniqueName = $input->getArgument('name');
    if (empty($uniqueName)) {
      if ($input->isInteractive()) {
        $uniqueName = $this->askForProjectBranchUniqueName($input, $output);
      }
      else {
        $output->writeln("<error>You must specify the unique name for the project branch you wish to clone.</error>");
        return 1;
      }
    }

    if (!$repository->projectBranchContainerExists($uniqueName)) {
      $output->writeln("<error>No project branch container with the unique name specified exists.</error>");
      return 1;
    }

    $newUniqueName = $input->getArgument('newname');
    $newFriendlyName = $input->getOption('fname');
    if (empty($newUniqueName)) {
      if ($input->isInteractive()) {

        if (empty($newFriendlyName)) {
          $newFriendlyName = $this->askForNewProjectBranchFriendlyName($input, $output);
        }

        $newUniqueName = $this->askForNewProjectBranchUniqueName($input, $output,
          $repository->friendlyNameToContainerName($newFriendlyName));
      }
      else {
        $output->writeln("<error>You must specify a unique name for the new project branch.</error>");
        return 1;
      }
    }

    if ($repository->containerExists($newUniqueName)) {
      $output->writeln("<error>A project branch already exists with the unique name you have specified.</error>");
      return 1;
    }

    if (empty($newFriendlyName)) {
      if ($input->isInteractive()) {
        $newFriendlyName = $this->askForNewProjectBranchFriendlyName($input, $output);
      }
      else {
        $output->writeln("<error>You must specify a friendly name for the new project branch.</error>");
        return 1;
      }
    }

    $currentBranch = $repository->findOneByName($uniqueName);

    $newHostname = $input->getOption('hostname');
    if (empty($newHostname)) {
      if ($input->isInteractive()) {
        $defaultHostname  = $newUniqueName . '.' . $currentBranch->getDomain();
        $newHostname = $this->askForNewProjectBranchHostName($input, $output, $defaultHostname);
      }
      else {
        $output->writeln("<error>You must specify a host name for the new project branch.</error>");
        return 1;
      }
    }

    $project = $repository->getParentContainer($currentBranch);
    $newBranch = $repository->createContainer($newUniqueName, $newFriendlyName, false)
      ->setDomain($project->getDomain())
      ->setHostname($newHostname);
    $repository->saveContainer($project);
    $newBranch->setCloneSource($currentBranch);
    $newBranch->setDownloadHost($this->getContainer()->getParameter('rainmaker_download_host'));
    $this->setContainerEntity($newBranch);

    $this->getConfiguredTask()->setContainer($project);

    parent::execute($input, $output);
  }

  protected function askForProjectBranchUniqueName(InputInterface $input, OutputInterface $output, $defaultName = NULL)
  {
    $text = 'Enter the unique container name of a project branch to clone [' . $defaultName . ']:';
    return $this->getHelper('question')->ask($input, $output, new Question($text, $defaultName));
  }

  protected function askForNewProjectBranchUniqueName(InputInterface $input, OutputInterface $output, $defaultName = NULL)
  {
    $text = 'Enter the unique container name of the new project branch [' . $defaultName . ']:';
    return $this->getHelper('question')->ask($input, $output, new Question($text, $defaultName));
  }

  protected function askForNewProjectBranchFriendlyName(InputInterface $input, OutputInterface $output)
  {
    $text = 'Enter the human friendly name for this new project branch:';
    return $this->getHelper('question')->ask($input, $output, new Question($text));
  }

  protected function askForNewProjectBranchHostName(InputInterface $input, OutputInterface $output, $defaultHostname)
  {
    $text = 'Enter the host name for this new project branch [' . $defaultHostname . ']:';
    return $this->getHelper('question')->ask($input, $output, new Question($text, $defaultHostname));
  }

}
