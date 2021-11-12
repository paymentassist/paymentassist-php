<?php

$autoloader = dirname(__DIR__, 2) . '/vendor/autoload.php';

if (!file_exists($autoloader)) {
    /** @noinspection PhpUnhandledExceptionInspection */
    throw new Exception('Composer autoload not found, run composer install first');
}

require $autoloader;
