<?php

namespace RainmakerCliBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectListCommand extends RainmakerCommand
{

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('project:list')
            ->setDescription('Show a list of configured projects');
    }

    public function task()
    {
        return new \Rainmaker\Task\Project\ProjectList();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $task = $this->getConfiguredTask();
        parent::execute($input, $output);
        $output->write("\n" . $task->getList() . "\n", true);
    }

}
