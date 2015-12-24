<?php

namespace Rainmaker\Process\RainmakerProfileManager;

use Symfony\Component\Process\Process;

/**
 * Process for getting the latest version of a profile from rprofmgr.
 *
 * @package Rainmaker\Process\RainmakerProfileManager
 * @return void
 */
class GetLatestProfileVersion extends Process
{

    public function __construct($profileName)
    {
        parent::__construct('rprofmgr profile:latest-version ' . escapeshellarg($profileName));
    }

}
