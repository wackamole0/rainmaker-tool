<?php

namespace Rainmaker\Util;

/**
 * A wrapper class for interacting with the filesystem. This subclasses the Symfony 2 Filesystem class and adds
 * support for reading and writing files.
 *
 * @package Rainmaker\Util
 */
class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{

    /**
     * @param string $file
     * @return string
     */
    public function getFileContents($file)
    {
        return file_get_contents($file);
    }

    /**
     * @param string $file
     * @param string $contents
     * @return int The function returns the number of bytes that were written to the file, or false on failure.
     */
    public function putFileContents($file, $contents)
    {
        return file_put_contents($file, $contents);
    }

}
