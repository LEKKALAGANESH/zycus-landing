<?php declare(strict_types=1);

namespace Zycus\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Zycus\RateLimiter;

/**
 * Tests for Zycus\RateLimiter.
 *
 * NOTE ON TESTABILITY:
 * The production RateLimiter::allow(string $ip): bool calls the singleton
 * Database::pdo() directly. Real unit-testability requires one of:
 *
 *   (a) Refactoring RateLimiter to accept an injected PDO, e.g.
 *         public static function allow(string $ip, ?\PDO $pdo = null): bool
 *       and falling back to Database::pdo() when $pdo is null.
 *   (b) Reflection-swapping a private static $pdo on Database — no seam today.
 *
 * The tests below assume path (a). Until the refactor lands, every test
 * skips with a clear message rather than faking green. When the DI
 * refactor ships, delete the assertInjectableOrSkip() guard.
 */
final class RateLimiterTest extends TestCase
{
    private function assertInjectableOrSkip(): void
    {
        $reflection = new \ReflectionMethod(RateLimiter::class, 'allow');
        if ($reflection->getNumberOfParameters() < 2) {
            $this->markTestSkipped(
                'RateLimiter::allow is hard-coupled to Database::pdo() singleton. '
                . 'Refactor signature to `allow(string $ip, ?PDO $pdo = null)` to enable unit testing.'
            );
        }
    }

    /**
     * Build a mock PDO whose SELECT COUNT(*) returns $count and whose INSERT
     * records whether execute() was called.
     *
     * @return array{0: \PDO, 1: \PDOStatement, 2: \PDOStatement}
     */
    private function buildPdoMock(int $count): array
    {
        $selectStmt = $this->createMock(\PDOStatement::class);
        $selectStmt->method('fetchColumn')->willReturn($count);

        $insertStmt = $this->createMock(\PDOStatement::class);

        $pdo = $this->createMock(\PDO::class);
        $pdo->method('prepare')->willReturnCallback(
            function (string $sql) use ($selectStmt, $insertStmt) {
                if (stripos($sql, 'SELECT') === 0) return $selectStmt;
                if (stripos($sql, 'INSERT') === 0) return $insertStmt;
                return $this->createMock(\PDOStatement::class);
            }
        );

        return [$pdo, $selectStmt, $insertStmt];
    }

    #[Test]
    public function allow_returns_true_when_below_threshold(): void
    {
        $this->assertInjectableOrSkip();
        [$pdo, $selectStmt, $insertStmt] = $this->buildPdoMock(count: 3);
        $selectStmt->expects($this->once())->method('execute');
        $insertStmt->expects($this->once())->method('execute');

        $result = RateLimiter::allow('192.0.2.10', $pdo);

        $this->assertTrue($result);
    }

    #[Test]
    public function allow_returns_false_when_at_or_above_threshold(): void
    {
        $this->assertInjectableOrSkip();
        [$pdo, $selectStmt, $insertStmt] = $this->buildPdoMock(count: 100);
        $selectStmt->expects($this->once())->method('execute');
        $insertStmt->expects($this->never())->method('execute');

        $result = RateLimiter::allow('192.0.2.10', $pdo);

        $this->assertFalse($result);
    }

    #[Test]
    public function allow_inserts_a_new_row_when_below_threshold(): void
    {
        $this->assertInjectableOrSkip();
        [$pdo, , $insertStmt] = $this->buildPdoMock(count: 0);
        $insertStmt->expects($this->once())->method('execute');

        $result = RateLimiter::allow('192.0.2.77', $pdo);

        $this->assertTrue($result);
    }

    #[Test]
    public function allow_scopes_by_ip(): void
    {
        $this->assertInjectableOrSkip();
        $ip = '203.0.113.42';

        $selectStmt = $this->createMock(\PDOStatement::class);
        $selectStmt->method('fetchColumn')->willReturn(0);
        $selectStmt->expects($this->once())
            ->method('execute')
            ->with($this->callback(static fn($p) => in_array($ip, (array) $p, true)));

        $insertStmt = $this->createMock(\PDOStatement::class);
        $insertStmt->expects($this->once())
            ->method('execute')
            ->with($this->callback(static fn($p) => in_array($ip, (array) $p, true)));

        $pdo = $this->createMock(\PDO::class);
        $pdo->method('prepare')->willReturnCallback(
            fn(string $sql) => stripos($sql, 'SELECT') === 0 ? $selectStmt : $insertStmt
        );

        $result = RateLimiter::allow($ip, $pdo);

        $this->assertTrue($result);
    }
}
