<?php

namespace Rainmaker\Process;

use Symfony\Component\Process\Process;

/**
 * Process for mounting a mount from the Fstab.
 *
 * @package Rainmaker\Process
 * @return void
 */
class FstabMountProcess extends Process
{

    public function __construct($target)
    {
        parent::__construct('mount ' . $target);
    }

}
