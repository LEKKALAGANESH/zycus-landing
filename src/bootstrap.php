<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

\Zycus\Config::boot(__DIR__ . '/..');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
