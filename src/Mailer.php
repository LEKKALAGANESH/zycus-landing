<?php declare(strict_types=1);

namespace Zycus;

use PHPMailer\PHPMailer\PHPMailer;

final class Mailer
{
    public static function notifyNewSubmission(array $clean): void
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = Config::require('MAIL_HOST');
        $mail->Port       = (int) Config::require('MAIL_PORT');
        $mail->SMTPAuth   = true;
        $mail->Username   = Config::require('MAIL_USER');
        $mail->Password   = Config::require('MAIL_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->CharSet    = 'UTF-8';

        $fromEmail = Config::require('MAIL_FROM_EMAIL');
        $fromName  = (string) Config::get('MAIL_FROM_NAME', 'Zycus');
        $toEmail   = Config::require('MAIL_TO_EMAIL');

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail);

        $replyName = trim(($clean['first_name'] ?? '') . ' ' . ($clean['last_name'] ?? ''));
        $mail->addReplyTo((string) ($clean['email'] ?? $fromEmail), $replyName);

        $companyName = (string) ($clean['company_name'] ?? '');
        $mail->Subject = sprintf('New Demo Request — %s', $companyName);

        $body = "New demo request received:\n\n";
        foreach ($clean as $key => $value) {
            $body .= sprintf("%-15s: %s\n", $key, (string) $value);
        }

        $mail->isHTML(false);
        $mail->Body = $body;

        $mail->send();
    }
}
