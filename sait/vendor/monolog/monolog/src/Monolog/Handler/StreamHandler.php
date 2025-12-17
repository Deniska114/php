<?php
/**
 * Мінімальна заглушка StreamHandler для тестування
 */

namespace Monolog\Handler;

use Monolog\Logger;

class StreamHandler
{
    private $stream;
    private $level;

    public function __construct($stream, $level = Logger::DEBUG)
    {
        $this->stream = $stream;
        $this->level = $level;
    }

    public function handle($record)
    {
        // Мінімальна реалізація - запис у файл якщо можливо
        if (is_string($this->stream) && file_exists(dirname($this->stream))) {
            $message = date('Y-m-d H:i:s') . ' - ' . ($record['message'] ?? '') . PHP_EOL;
            @file_put_contents($this->stream, $message, FILE_APPEND);
        }
    }
}

