<?php

namespace Rainmaker\ComponentManager;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Rainmaker\Process\RainmakerProfileManager\GetLatestProfileVersion;
use Rainmaker\Process\RainmakerProfileManager\GetProfileMetadata;
use Rainmaker\Process\RainmakerProfileManager\DownloadProfileVersionRootFs;
use Rainmaker\Entity\Container;

/**
 * A class for managing the Rainmaker Profiles via the Rainmaker Profile Manager (rprofmgr) tool.
 *
 * @package Rainmaker\ComponentManager
 */
class RainmakerProfileManager extends ComponentManager
{

    public function configureProjectProfileSettings(Container $container)
    {
        $this->container = $container;
        $this->setContainerProfileVersion();
        $this->setContainerProfileMetadata();
        $this->downloadProfileRootFs();
    }

    public function configureProjectBranchProfileSettings(Container $container)
    {
        $this->container = $container;
        $this->setContainerProfileVersion();
        $this->setContainerProfileMetadata();
        $this->downloadProfileRootFs();
    }

    public function configureProjectBranchCloneProfileSettings(Container $container)
    {
        $this->container = $container;
        $this->container->setProfileName($this->container->getCloneSource()->getProfileName());
        $this->container->setProfileVersion($this->container->getCloneSource()->getProfileVersion());
        $this->container->setProfileMetadata($this->container->getCloneSource()->getProfileMetadata());
        $this->downloadProfileRootFs();
    }

    public function setContainerProfileVersion()
    {
        if (empty($this->container->getProfileVersion())) {
            $this->container->setProfileVersion($this->getLatestVersionOfProfile($this->container->getProfileName()));
        }
    }

    public function setContainerProfileMetadata()
    {
        if (empty($this->container->getProfileMetadata())) {
            $this->container->setProfileMetadata($this->getProfileMetadata(
                $this->container->getProfileName(),
                $this->container->getProfileVersion()
            ));
        }
    }

    public function downloadProfileRootFs()
    {
        try {
            $process = new DownloadProfileVersionRootFs(
                $this->container->getProfileName(),
                $this->container->getProfileVersion(),
                $this->container->isSetDownloadHost() ? $this->container->getDownloadHostFullyQualified() : null
            );
            $this->getProcessRunner()->run($process);
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }
    }

    public function getLatestVersionOfProfile($profileName)
    {
        try {
            $process = new GetLatestProfileVersion($profileName);
            $this->getProcessRunner()->run($process);
            return trim($this->processRunner->getProcessOutput($process));
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }
    }

    public function getProfileMetadata($profileName, $profileVersion)
    {
        try {
            $process = new GetProfileMetadata($profileName, $profileVersion);
            $this->getProcessRunner()->run($process);
            return trim($this->processRunner->getProcessOutput($process));
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }
    }

}
