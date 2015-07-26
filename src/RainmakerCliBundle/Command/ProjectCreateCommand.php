<?php

namespace RainmakerCliBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rainmaker\Entity\ContainerRepository;
use Symfony\Component\Console\Question\Question;

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
      )
      ->addArgument(
        'uname',
        InputArgument::OPTIONAL,
        'The unique name of the new project'
      )
      ->addArgument(
        'domain',
        InputArgument::OPTIONAL,
        'The domain name of the new project'
      )
      ->addArgument(
        'hostname',
        InputArgument::OPTIONAL,
        'The host name of the new project'
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

    //lxc-create --template download --name "$GOLDPROJ_LXC_NAME" -- --dist ubuntu --release trusty --arch amd64
    //lxc-clone _golden-proj_ myproject

    $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');

    $friendlyName = $input->getArgument('name');
    if (empty($friendlyName)) {
      if ($input->isInteractive()) {
        $friendlyName = $this->askForProjectFriendlyName($input, $output);
      }
      else {
        $output->writeln("<error>You must specify a friendly name for the project.</error>");
        return 1;
      }
    }

    $uniqueName = $input->getArgument('uname');
    if (empty($uniqueName)) {
      if ($input->isInteractive()) {
        $uniqueName = $this->askForProjectUniqueName($input, $output,
          $repository->friendlyNameToContainerName($friendlyName));
      }
      else {
        $output->writeln("<error>You must specify a unique name for the project.</error>");
        return 1;
      }
    }

    if ($repository->containerExists($uniqueName)) {
      $output->writeln("<error>A project already exists with the unique name you have specified.</error>");
      return 1;
    }

    //

    $domain = $input->getArgument('domain');
    if (empty($domain)) {
      if ($input->isInteractive()) {
        $defaultDomain  = $uniqueName . '.localdev';
        $domain = $this->askForProjectDomainName($input, $output, $defaultDomain);
      }
      else {
        $output->writeln("<error>You must specify a domain name for the project.</error>");
        return 1;
      }
    }

    $hostname = $input->getArgument('hostname');
    if (empty($hostname)) {
      if ($input->isInteractive()) {
        $defaultHostname  = 'cluster.' . $domain;
        $hostname = $this->askForProjectHostName($input, $output, $defaultHostname);
      }
      else {
        $output->writeln("<error>You must specify a host name for the project.</error>");
        return 1;
      }
    }

    $this->setContainerEntity($repository->createContainer($uniqueName, $friendlyName, false));
    $this->getContainerEntity()
      ->setDomain($domain)
      ->setHostname($hostname);
    $repository->saveContainer($this->getContainerEntity());

    $this->getConfiguredTask()->setContainer($this->getContainerEntity());
    parent::execute($input, $output);
  }

  protected function askForProjectFriendlyName(InputInterface $input, OutputInterface $output)
  {
    $text = 'Enter the human friendly name for this project:';
    return $this->getHelper('question')->ask($input, $output, new Question($text));
  }

  protected function askForProjectUniqueName(InputInterface $input, OutputInterface $output, $defaultName = NULL)
  {
    $text = 'Enter the unique container name for this project [' . $defaultName . ']:';
    return $this->getHelper('question')->ask($input, $output, new Question($text, $defaultName));
  }

  protected function askForProjectDomainName(InputInterface $input, OutputInterface $output, $defaultDomainName = NULL)
  {
    $text = 'Enter the domain name for this project [' . $defaultDomainName . ']:';
    return $this->getHelper('question')->ask($input, $output, new Question($text, $defaultDomainName));
  }

  protected function askForProjectHostName(InputInterface $input, OutputInterface $output, $defaultHostname)
  {
    $text = 'Enter the host name for this project [' . $defaultHostname . ']:';
    return $this->getHelper('question')->ask($input, $output, new Question($text, $defaultHostname));
  }

}
