<?php

namespace RainmakerCliBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ProjectDestroyCommand extends RainmakerCommand
{

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('project:destroy')
            ->setDescription('Destroy a Rainmaker project container')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'The unique name of the new project'
            );
    }

    public function task()
    {
        return new \Rainmaker\Task\Project\Destroy();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');

        $uniqueName = $input->getArgument('name');
        if (empty($uniqueName)) {
            if ($input->isInteractive()) {
                $uniqueName = $this->askForProjectUniqueName($input, $output);
            } else {
                $output->writeln("<error>You must specify the unique name of project.</error>");
                return 1;
            }
        }

        if (!$repository->projectContainerExists($uniqueName)) {
            $output->writeln("<error>No container with the unique name specified exists.</error>");
            return 1;
        }

        $this->setContainerEntity($repository->findOneByName($uniqueName));
        $this->getConfiguredTask()->setContainer($this->getContainerEntity());

        parent::execute($input, $output);
    }

    protected function askForProjectUniqueName(InputInterface $input, OutputInterface $output)
    {
        $text = 'Enter the unique container name of a project:';
        return $this->getHelper('question')->ask($input, $output, new Question($text));
    }

}
