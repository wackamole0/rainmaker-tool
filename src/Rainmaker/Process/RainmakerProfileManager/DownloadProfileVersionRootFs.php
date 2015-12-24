<?php

namespace Rainmaker\Process\RainmakerProfileManager;

use Symfony\Component\Process\Process;

/**
 * Process for getting the latest version of a profile from rprofmgr.
 *
 * @package Rainmaker\Process\RainmakerProfileManager
 * @return void
 */
class DownloadProfileVersionRootFs extends Process
{

    public function __construct($profileName, $profileVersion, $downloadHost = null)
    {
        $cmd = 'rprofmgr profile:download-version --profile-version=' . escapeshellarg($profileVersion);
        if (!empty($downloadHost)) {
            $cmd .= ' --download-host=' . escapeshellarg($downloadHost);
        }
        $cmd .= ' ' . escapeshellarg($profileName);
        parent::__construct($cmd);
    }

}
