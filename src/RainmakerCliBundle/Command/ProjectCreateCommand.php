<?php

namespace RainmakerCliBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rainmaker\Entity\ContainerRepository;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
                'The unique name of the new project'
            )
            ->addOption(
                'fname',
                null,
                InputOption::VALUE_REQUIRED,
                'The friendly name of the new project'
            )
            ->addOption(
                'domain',
                null,
                InputOption::VALUE_REQUIRED,
                'The domain name of the new project'
            )
            ->addOption(
                'hostname',
                null,
                InputOption::VALUE_REQUIRED,
                'The host name of the new project'
            )
            ->addOption(
                'newbranch',
                null,
                InputOption::VALUE_NONE,
                'If this option is present an initial Rainmaker branch container will be created inside the new Rainmaker project container'
            )
            ->addOption(
                'branch-name',
                null,
                InputOption::VALUE_REQUIRED,
                'The unique name of the new project branch'
            )
            ->addOption(
                'branch-fname',
                null,
                InputOption::VALUE_REQUIRED,
                'The friendly name of the new project branch'
            )
            ->addOption(
                'branch-hostname',
                null,
                InputOption::VALUE_REQUIRED,
                'The host name of the new project branch'
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

        /** @var ContainerRepository $repository */
        $repository = $this->getEntityManager()->getRepository('Rainmaker:Container');

        $uniqueName = $input->getArgument('name');
        $friendlyName = $input->getOption('fname');
        if (empty($uniqueName)) {
            if ($input->isInteractive()) {

                if (empty($friendlyName)) {
                    $friendlyName = $this->askForProjectFriendlyName($input, $output);
                }

                $uniqueName = $this->askForProjectUniqueName($input, $output,
                    $repository->friendlyNameToContainerName($friendlyName));
            } else {
                $output->writeln("<error>You must specify a unique name for the project.</error>");
                return 1;
            }
        }

        if (empty($friendlyName)) {
            if ($input->isInteractive()) {
                $friendlyName = $this->askForProjectFriendlyName($input, $output);
            } else {
                $output->writeln("<error>You must specify a friendly name for the project.</error>");
                return 1;
            }
        }

        if ($repository->containerExists($uniqueName)) {
            $output->writeln("<error>A project already exists with the unique name you have specified.</error>");
            return 1;
        }

        //

        $domain = $input->getOption('domain');
        if (empty($domain)) {
            if ($input->isInteractive()) {
                $defaultDomain = $uniqueName . '.localdev';
                $domain = $this->askForProjectDomainName($input, $output, $defaultDomain);
            } else {
                $output->writeln("<error>You must specify a domain name for the project.</error>");
                return 1;
            }
        }

        $hostname = $input->getOption('hostname');
        if (empty($hostname)) {
            if ($input->isInteractive()) {
                $defaultHostname = 'cluster.' . $domain;
                $hostname = $this->askForProjectHostName($input, $output, $defaultHostname);
            } else {
                $output->writeln("<error>You must specify a host name for the project.</error>");
                return 1;
            }
        }

        $createBranch = $input->getOption('newbranch');
        if (empty($createBranch)) {
            if ($input->isInteractive()) {
                $createBranch = $this->askWhetherToCreateBranch($input, $output);
            }
        }

        $branchName = $input->getOption('branch-name');
        $branchFriendlyName = $input->getOption('branch-fname');
        $branchHostname = $input->getOption('branch-hostname');

        if ($createBranch && empty($branchName)) {
            if ($input->isInteractive()) {
                $branchName = $this->askForProjectBranchUniqueName($input, $output, $uniqueName . '.prod');
            } else {
                $output->writeln("<error>You must specify a unique name for the project branch.</error>");
                return 1;
            }
        }

        if ($createBranch && empty($branchFriendlyName)) {
            if ($input->isInteractive()) {
                $branchFriendlyName = $this->askForProjectBranchFriendlyName($input, $output);
            } else {
                $output->writeln("<error>You must specify a friendly name for the project branch.</error>");
                return 1;
            }
        }

        if ($createBranch && empty($branchHostname)) {
            if ($input->isInteractive()) {
                $defaultHostname = $domain;
                $branchHostname = $this->askForProjectHostName($input, $output, $defaultHostname);
            } else {
                $output->writeln("<error>You must specify a host name for the project branch.</error>");
                return 1;
            }
        }

        $project = $repository->createContainer($uniqueName, $friendlyName, false)
            ->setDomain($domain)
            ->setHostname($hostname);
        $repository->saveContainer($project);
        $project->setProfileName($this->getContainer()->getParameter('rainmaker_default_project_profile'));
        $project->setDownloadHost($this->getContainer()->getParameter('rainmaker_download_host'));
        $this->setContainerEntity($project);

        $this->getConfiguredTask()->setContainer($project);

        if ($createBranch) {
            $branch = $repository->createContainer($branchName, $branchFriendlyName, false)
                ->setDomain($domain)
                ->setHostname($branchHostname)
                ->setParentId($project->getId());
            $repository->saveContainer($branch);
            $branch->setProfileName($this->getContainer()->getParameter('rainmaker_default_project_branch_profile'));
            $branch->setDownloadHost($this->getContainer()->getParameter('rainmaker_download_host'));
            $this->getConfiguredTask()->setBranchContainer($branch);
        }

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

    protected function askWhetherToCreateBranch(InputInterface $input, OutputInterface $output)
    {
        return $this->getHelper('question')->ask($input, $output,
            new ConfirmationQuestion('Would you like to create a new initial branch in this project?', false));
    }

    protected function askForProjectBranchUniqueName(InputInterface $input, OutputInterface $output, $defaultName = NULL)
    {
        $text = 'Enter the unique container name for this project branch [' . $defaultName . ']:';
        return $this->getHelper('question')->ask($input, $output, new Question($text, $defaultName));
    }

    protected function askForProjectBranchFriendlyName(InputInterface $input, OutputInterface $output)
    {
        $text = 'Enter the human friendly name for this project branch:';
        return $this->getHelper('question')->ask($input, $output, new Question($text));
    }

}
