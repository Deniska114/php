<?php
/**
 * Мінімальна заглушка Monolog Logger для тестування
 * Для повної роботи потрібно встановити через: composer install
 */

namespace Monolog;

class Logger
{
    // Константи рівнів логування
    const DEBUG = 100;
    const INFO = 200;
    const NOTICE = 250;
    const WARNING = 300;
    const ERROR = 400;
    const CRITICAL = 500;
    const ALERT = 550;
    const EMERGENCY = 600;
    
    private $name;
    private $handlers = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function pushHandler($handler)
    {
        $this->handlers[] = $handler;
        return $this;
    }

    public function info($message, array $context = [])
    {
        // Мінімальна реалізація для тестування
        foreach ($this->handlers as $handler) {
            if (method_exists($handler, 'handle')) {
                $handler->handle(['message' => $message, 'context' => $context]);
            }
        }
    }

    public function warning($message, array $context = [])
    {
        $this->info($message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->info($message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->info($message, $context);
    }
}

