<?php declare(strict_types=1);

namespace Zycus;

use Dotenv\Dotenv;
use RuntimeException;

final class Config
{
    private static bool $booted = false;

    public static function boot(string $rootDir): void
    {
        if (self::$booted) {
            return;
        }

        Dotenv::createImmutable($rootDir)->load();
        self::$booted = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return $value;
    }

    public static function require(string $key): string
    {
        $value = self::get($key);

        if ($value === null || $value === '') {
            throw new RuntimeException(sprintf('Missing required environment variable: %s', $key));
        }

        return (string) $value;
    }
}
