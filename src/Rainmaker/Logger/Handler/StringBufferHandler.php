<?php

namespace Rainmaker\Logger\Handler;

use Monolog\Handler\AbstractProcessingHandler;

/**
 * Buffers log records to a string.
 */
class StringBufferHandler extends AbstractProcessingHandler
{
    protected $buffer = '';

    public function bufferContents()
    {
        return $this->buffer;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $this->buffer .= (string) $record['formatted'];
    }
}
