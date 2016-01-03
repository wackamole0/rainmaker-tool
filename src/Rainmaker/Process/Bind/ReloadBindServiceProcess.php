<?php

namespace Rainmaker\Process\Bind;

use Symfony\Component\Process\Process;

/**
 * Process for reloading the Bind service.
 *
 * @package Rainmaker\Process\Bind
 * @return void
 */
class ReloadBindServiceProcess extends Process
{

    public function __construct()
    {
        parent::__construct('lxc-attach -n services -- service bind9 reload');
    }

}
