<?php

namespace Rainmaker\Logger\Formatter;

use Exception;
use Monolog\Formatter\NormalizerFormatter;

/**
 *
 */
class TaskLogFormatter extends NormalizerFormatter
{
    const NEWLINE_INDENT_LENGTH = 25;

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $vars = parent::format($record);

        $output = '';

        $time = 0;
        if (!empty($vars['datetime'])) {
            $time = strtotime($vars['datetime']);
        }
        $output .= sprintf('[%10s]', round($time - 1436772795, 3));

        $output .= ' ' . sprintf('(%-9s)', !empty($vars['level_name']) ? $vars['level_name'] : '');
        $output .= ' ' . (!empty($vars['message']) ? $this->stringify($vars['message']) : '');

//        foreach ($vars['extra'] as $var => $val) {
//            if (false !== strpos($output, '%extra.'.$var.'%')) {
//                $output = str_replace('%extra.'.$var.'%', $this->stringify($val), $output);
//                unset($vars['extra'][$var]);
//            }
//        }
//
//        foreach ($vars as $var => $val) {
//            if (false !== strpos($output, '%'.$var.'%')) {
//                $output = str_replace('%'.$var.'%', $this->stringify($val), $output);
//            }
//        }

        return $output;
    }

    public function formatBatch(array $records)
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    public function stringify($value)
    {
        return $this->indentNewlines($this->convertToString($value));
    }

    protected function normalizeException(Exception $e)
    {
        $previousText = '';
        if ($previous = $e->getPrevious()) {
            do {
                $previousText .= ",\n" . get_class($previous) .
                  '(code: '.$previous->getCode() . '): ' .
                  $previous->getMessage() .
                  ' at ' . $previous->getFile() .
                  ':' . $previous->getLine();
            } while ($previous = $previous->getPrevious());
        }

        $str = '[object] ('.get_class($e) .
          '(code: ' . $e->getCode() . '): ' .
          $e->getMessage() .
          ' at ' . $e->getFile() .
          ':' . $e->getLine() .
          "'\n" . $previousText . ')';
        $str .= "\n[stacktrace]\n".$e->getTraceAsString();

        $str = $this->indentNewlines($str);

        return $str;
    }

    protected function convertToString($data)
    {
        if (null === $data || is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string) $data;
        }

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return $this->toJson($data, true);
        }

        return str_replace('\\/', '/', @json_encode($data, JSON_PRETTY_PRINT));
    }

    protected function indentNewlines($str)
    {
        return strtr($str, array(
          "\r\n"    => str_repeat(' ', static::NEWLINE_INDENT_LENGTH),
          "\r"      => str_repeat(' ', static::NEWLINE_INDENT_LENGTH),
          "\n"      => str_repeat(' ', static::NEWLINE_INDENT_LENGTH)
        ));
    }
}
