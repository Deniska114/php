<?php
/**
 * Мінімальна заглушка Guzzle Client для тестування
 * Для повної роботи потрібно встановити через: composer install
 */

namespace GuzzleHttp;

class Client
{
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function get($url, array $options = [])
    {
        // Мінімальна реалізація для тестування
        // У реальному випадку тут буде HTTP запит
        return new Response();
    }

    public function post($url, array $options = [])
    {
        return new Response();
    }
}

class Response
{
    public function getStatusCode()
    {
        return 200;
    }

    public function getBody()
    {
        return new Stream();
    }
}

class Stream
{
    public function getContents()
    {
        return '{}';
    }
}


