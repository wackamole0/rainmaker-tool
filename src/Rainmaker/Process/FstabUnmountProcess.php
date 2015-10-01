<?php

namespace Rainmaker\Process;

use Symfony\Component\Process\Process;

/**
 * Process for unmounting a mount from the Fstab.
 *
 * @package Rainmaker\Process
 * @return void
 */
class FstabUnmountProcess extends Process {

  public function __construct($target)
  {
    parent::__construct('umount ' . $target);
  }

}
