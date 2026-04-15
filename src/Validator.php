<?php declare(strict_types=1);

namespace Zycus;

final class Validator
{
    public static function validate(array $input): array
    {
        $errors = [];
        $clean  = [];

        if (!empty($input['website_url'])) {
            $errors['_honeypot'] = 'Spam detected.';
        }

        $email = trim((string) ($input['email'] ?? ''));
        // Match free-email providers across all TLDs (gmail.com, gmail.co.uk,
        // yahoo.co.in, outlook.jp, etc.) by keying off the domain label, not
        // the full ".com" suffix.
        $freeMailDomains = ['gmail', 'yahoo', 'hotmail', 'outlook', 'live', 'icloud', 'aol', 'protonmail', 'proton', 'gmx', 'mail', 'yandex', 'zoho', 'rediffmail'];
        $freeMailRegex = '/@(' . implode('|', $freeMailDomains) . ')\.[a-z][a-z0-9.\-]*[a-z]$/i';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (preg_match($freeMailRegex, $email)) {
            $errors['email'] = 'Please use your work email.';
        } else {
            $clean['email'] = $email;
        }

        $labels = [
            'first_name'   => 'First name',
            'last_name'    => 'Last name',
            'company_name' => 'Company name',
        ];
        foreach ($labels as $field => $label) {
            $value = trim((string) ($input[$field] ?? ''));
            if ($value === '') {
                $errors[$field] = $label . ' is required.';
            } elseif (mb_strlen($value) > 255) {
                $errors[$field] = $label . ' must be under 255 characters.';
            } else {
                $clean[$field] = $value;
            }
        }

        $companySize = (string) ($input['company_size'] ?? '');
        if (!in_array($companySize, ['small', 'mid', 'enterprise', 'large_enterprise'], true)) {
            $errors['company_size'] = 'Please select your company size.';
        } else {
            $clean['company_size'] = $companySize;
        }

        $role = (string) ($input['role'] ?? '');
        if (!in_array($role, ['procurement_leader', 'finance_leader', 'it', 'procurement_team', 'other'], true)) {
            $errors['role'] = 'Please select your role.';
        } else {
            $clean['role'] = $role;
        }

        $useCase = (string) ($input['use_case'] ?? '');
        if (!in_array($useCase, ['s2c', 'ap', 'supplier_mgmt', 's2p'], true)) {
            $errors['use_case'] = 'Please select a primary use case.';
        } else {
            $clean['use_case'] = $useCase;
        }

        $notes = trim((string) ($input['notes'] ?? ''));
        if ($notes !== '') {
            if (mb_strlen($notes) > 500) {
                $errors['notes'] = 'Max 500 characters.';
            } else {
                $clean['notes'] = $notes;
            }
        } else {
            $clean['notes'] = '';
        }

        return [$errors, $clean];
    }
}
