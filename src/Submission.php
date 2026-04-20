<?php declare(strict_types=1);

namespace Zycus;

final class Submission
{
    public static function store(array $clean, string $ip, ?string $userAgent, ?string $sourceUrl): int
    {
        $pdo = Database::pdo();

        $sql = 'INSERT INTO submissions
            (first_name, last_name, email, company_name, company_size, role, use_case, notes, ip_address, user_agent, source_url, created_at)
            VALUES
            (:first_name, :last_name, :email, :company_name, :company_size, :role, :use_case, :notes, :ip_address, :user_agent, :source_url, NOW())';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':first_name'   => $clean['first_name']   ?? '',
            ':last_name'    => $clean['last_name']    ?? '',
            ':email'        => $clean['email']        ?? '',
            ':company_name' => $clean['company_name'] ?? '',
            ':company_size' => $clean['company_size'] ?? '',
            ':role'         => $clean['role']         ?? '',
            ':use_case'     => $clean['use_case']     ?? '',
            ':notes'        => $clean['notes']        ?? '',
            ':ip_address'   => $ip,
            ':user_agent'   => $userAgent,
            ':source_url'   => $sourceUrl,
        ]);

        return (int) $pdo->lastInsertId();
    }
}
