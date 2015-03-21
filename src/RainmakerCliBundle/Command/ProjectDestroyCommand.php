<?php

namespace RainmakerCliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectDestroyCommand extends RainmakerCommand
{

  protected function configure()
  {
    $this
      ->setName('project:destroy')
      ->setDescription('Destroy a existing project');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln('project:destroy');
  }

}
