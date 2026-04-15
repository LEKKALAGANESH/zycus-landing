<?php declare(strict_types=1);

namespace Zycus;

final class RateLimiter
{
    public const MAX_PER_HOUR = 10;

    public static function allow(string $ip): bool
    {
        $pdo = Database::pdo();

        $pdo->exec('DELETE FROM rate_limit WHERE hit_at < (NOW() - INTERVAL 1 HOUR)');

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM rate_limit WHERE ip_address = :ip AND hit_at >= (NOW() - INTERVAL 1 HOUR)');
        $stmt->execute([':ip' => $ip]);
        $count = (int) $stmt->fetchColumn();

        if ($count >= self::MAX_PER_HOUR) {
            return false;
        }

        $insert = $pdo->prepare('INSERT INTO rate_limit (ip_address, hit_at) VALUES (:ip, NOW())');
        $insert->execute([':ip' => $ip]);

        return true;
    }
}
