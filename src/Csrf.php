<?php declare(strict_types=1);

namespace Zycus;

final class Csrf
{
    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['csrf'];
    }

    public static function verify(string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return hash_equals((string) ($_SESSION['csrf'] ?? ''), $token);
    }

    /**
     * Rotate after any successful state-changing POST so a captured token
     * cannot be replayed within the same session.
     */
    public static function rotate(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
}
