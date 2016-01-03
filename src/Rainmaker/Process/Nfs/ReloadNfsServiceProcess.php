<?php

namespace Rainmaker\Process\Nfs;

use Symfony\Component\Process\Process;

/**
 * Process for reloading the NFS service.
 *
 * @package Rainmaker\Process\Nfs
 * @return void
 */
class ReloadNfsServiceProcess extends Process
{

    public function __construct()
    {
        parent::__construct('service nfs-kernel-server reload');
    }

}
