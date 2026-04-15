<?php declare(strict_types=1);

require __DIR__ . '/../../src/bootstrap.php';

use Zycus\{Csrf, Validator, RateLimiter, Submission, Mailer};

header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// X-Forwarded-For is only honoured behind a trusted proxy/CDN. Accepting it
// unconditionally lets any client spoof their IP and bypass rate-limiting.
$trustProxy = strtolower((string) \Zycus\Config::get('TRUSTED_PROXY', 'false')) === 'true';
if ($trustProxy) {
    $forwarded = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
    $ip = $forwarded !== ''
        ? trim(explode(',', $forwarded)[0])
        : (string) ($_SERVER['REMOTE_ADDR'] ?? '');
} else {
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
}
if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP)) {
    $ip = '0.0.0.0';
}

// 1. CSRF (cheap, no DB) — reject hostile / stale submissions first.
if (!Csrf::verify((string) ($_POST['csrf'] ?? ''))) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Invalid session. Please reload the page.']);
    exit;
}

// 2. Validation (cheap, no DB) — give the user per-field feedback before we
//    touch the database. Keeps the form responsive even if DB is unreachable.
[$errors, $clean] = Validator::validate($_POST);
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'errors' => $errors]);
    exit;
}

// 3. Rate-limit (requires DB). If the DB is unreachable, surface the
//    apology modal copy instead of a generic 500.
try {
    if (!RateLimiter::allow($ip)) {
        http_response_code(429);
        echo json_encode(['ok' => false, 'error' => 'Too many requests. Try again in an hour.']);
        exit;
    }
} catch (\PDOException $e) {
    error_log('RateLimiter DB failure: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode([
        'ok' => false,
        'errorType' => 'database',
        'errorHeadline' => 'Connection Interrupted',
        'errorBody' => 'Our database is currently unable to process your request. Please wait a few moments and try submitting again. If the issue persists, email our team directly at sales@zycus.com to schedule your demo.',
    ]);
    exit;
}

try {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $sourceUrl = $_SERVER['HTTP_REFERER'] ?? null;
    $id = Submission::store($clean, $ip, $userAgent !== null ? (string) $userAgent : null, $sourceUrl !== null ? (string) $sourceUrl : null);
} catch (\PDOException $e) {
    error_log('Submission DB failure: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode([
        'ok' => false,
        'errorType' => 'database',
        'errorHeadline' => 'We sincerely apologize, but we are experiencing a temporary connection issue.',
        'errorBody' => 'Our database is currently unable to process your request due to a server timeout. Please wait a few seconds and try submitting again. If the issue persists, you can bypass this form and directly email our team at sales@zycus.com to schedule your demo.',
    ]);
    exit;
} catch (\Throwable $e) {
    error_log('Submission failure: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Something went wrong. Please try again or email sales@zycus.com directly.',
    ]);
    exit;
}

$_SESSION['zycus_lead'] = [
    'first_name' => $clean['first_name'] ?? '',
    'email'      => $clean['email']      ?? '',
    'submitted_at' => time(),
];

// Rotate CSRF token so a captured nonce cannot be replayed in-session.
Csrf::rotate();

$redirect = match ($clean['company_size']) {
    'enterprise', 'large_enterprise' => (string) \Zycus\Config::get('APP_CALENDLY_URL', 'https://calendly.com/zycus-enterprise-ae'),
    'small'                          => '/thank-you.php?form=zycus_demo&tier=small',
    default                          => '/thank-you.php?form=zycus_demo',
};

echo json_encode(['ok' => true, 'redirect' => $redirect, 'id' => $id]);

// Flush the response to the client before firing the notification email —
// the user shouldn't wait on SMTP. Under FPM we use fastcgi_finish_request;
// fall back to a shutdown callback on the built-in server (dev).
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
    try {
        Mailer::notifyNewSubmission($clean);
    } catch (\Throwable $e) {
        error_log('Mailer failure after fastcgi flush: ' . $e->getMessage());
    }
} else {
    register_shutdown_function(static function () use ($clean) {
        try {
            Mailer::notifyNewSubmission($clean);
        } catch (\Throwable $e) {
            error_log('Mailer failure in shutdown handler: ' . $e->getMessage());
        }
    });
}
