<?php

namespace Rainmaker\Process\RainmakerProfileManager;

use Symfony\Component\Process\Process;

/**
 * Process for getting the latest version of a profile from rprofmgr.
 *
 * @package Rainmaker\Process\RainmakerProfileManager
 * @return void
 */
class GetProfileMetadata extends Process
{

    public function __construct($profileName, $profileVersion)
    {
        parent::__construct('rprofmgr profile:version-metadata --profile-version=' . escapeshellarg($profileVersion) . '  ' . escapeshellarg($profileName));
    }

}
