<?php declare(strict_types=1);

namespace Zycus\Tests;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Zycus\Csrf;

/**
 * Tests for Zycus\Csrf.
 *
 * Sessions carry state across tests, which would make results order-dependent.
 * Each test runs in a fresh PHP process so $_SESSION + session_start() side
 * effects start from a clean slate.
 */
#[RunTestsInSeparateProcesses]
final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        unset($_SESSION['csrf']);
        $_SESSION = [];
    }

    #[Test]
    public function token_returns_hex_string_of_expected_length(): void
    {
        $token = Csrf::token();

        $this->assertSame(64, strlen($token));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
    }

    #[Test]
    public function token_is_deterministic_within_session(): void
    {
        $first  = Csrf::token();
        $second = Csrf::token();

        $this->assertSame($first, $second);
    }

    #[Test]
    public function verify_accepts_the_correct_token(): void
    {
        $token  = Csrf::token();
        $result = Csrf::verify($token);

        $this->assertTrue($result);
    }

    #[Test]
    public function verify_rejects_an_incorrect_token(): void
    {
        Csrf::token();
        $bogus = str_repeat('0', 64);

        $result = Csrf::verify($bogus);

        $this->assertFalse($result);
    }

    #[Test]
    public function verify_rejects_an_empty_string(): void
    {
        Csrf::token();

        $result = Csrf::verify('');

        $this->assertFalse($result);
    }

    #[Test]
    public function rotate_changes_the_token(): void
    {
        $original = Csrf::token();

        Csrf::rotate();
        $rotated = Csrf::token();

        $this->assertNotSame($original, $rotated);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $rotated);
    }

    #[Test]
    public function verify_with_old_token_fails_after_rotate(): void
    {
        $original = Csrf::token();

        Csrf::rotate();
        $result = Csrf::verify($original);

        $this->assertFalse($result);
    }
}
