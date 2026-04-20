<?php

declare(strict_types=1);

namespace Zycus\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Zycus\Validator;

final class ValidatorTest extends TestCase
{
    /**
     * Known-good 8-field input. Individual tests override only the field under test.
     *
     * @return array<string, string>
     */
    private function minimumValidPayload(): array
    {
        return [
            'first_name'   => 'Alice',
            'last_name'    => 'Anderson',
            'email'        => 'alice@acme.com',
            'company_name' => 'Acme Corp',
            'company_size' => 'mid',
            'role'         => 'procurement_leader',
            'use_case'     => 's2c',
            'notes'        => '',
            'website_url'  => '',
        ];
    }

    #[Test]
    public function empty_input_surfaces_all_required_errors(): void
    {
        $input = [];

        [$errors, $clean] = Validator::validate($input);

        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('first_name', $errors);
        $this->assertArrayHasKey('last_name', $errors);
        $this->assertArrayHasKey('company_name', $errors);
        $this->assertArrayHasKey('company_size', $errors);
        $this->assertArrayHasKey('role', $errors);
        $this->assertArrayHasKey('use_case', $errors);
        $this->assertSame('', $clean['notes']);
    }

    #[Test]
    public function valid_work_email_passes(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = 'alice@acme.com';

        [$errors, $clean] = Validator::validate($input);

        $this->assertArrayNotHasKey('email', $errors);
        $this->assertSame('alice@acme.com', $clean['email']);
    }

    #[Test]
    public function gmail_com_is_rejected_as_free_mail(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = 'alice@gmail.com';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('email', $errors);
        $this->assertSame('Please use your work email.', $errors['email']);
    }

    #[Test]
    public function gmail_co_uk_is_also_rejected(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = 'alice@gmail.co.uk';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('email', $errors);
        $this->assertSame('Please use your work email.', $errors['email']);
    }

    #[Test]
    public function yahoo_co_in_is_also_rejected(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = 'bob@yahoo.co.in';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('email', $errors);
        $this->assertSame('Please use your work email.', $errors['email']);
    }

    #[Test]
    public function protonmail_com_is_rejected(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = 'x@protonmail.com';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('email', $errors);
        $this->assertSame('Please use your work email.', $errors['email']);
    }

    #[Test]
    public function acme_corp_com_passes(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = 'eve@acme-corp.com';

        [$errors, $clean] = Validator::validate($input);

        $this->assertArrayNotHasKey('email', $errors);
        $this->assertSame('eve@acme-corp.com', $clean['email']);
    }

    #[Test]
    public function malformed_email_gives_format_error_not_free_mail_error(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = 'not-an-email';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('email', $errors);
        $this->assertSame('Please enter a valid email address.', $errors['email']);
    }

    #[Test]
    public function empty_email_gives_format_error(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = '';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('email', $errors);
        $this->assertSame('Please enter a valid email address.', $errors['email']);
    }

    #[Test]
    public function email_is_trimmed(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = '  alice@acme.com  ';

        [$errors, $clean] = Validator::validate($input);

        $this->assertArrayNotHasKey('email', $errors);
        $this->assertSame('alice@acme.com', $clean['email']);
    }

    #[Test]
    public function empty_first_name_error_message_uses_label(): void
    {
        $input = $this->minimumValidPayload();
        $input['first_name'] = '';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('first_name', $errors);
        $this->assertSame('First name is required.', $errors['first_name']);
    }

    #[Test]
    public function empty_last_name_error_message_uses_label(): void
    {
        $input = $this->minimumValidPayload();
        $input['last_name'] = '';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('last_name', $errors);
        $this->assertSame('Last name is required.', $errors['last_name']);
    }

    #[Test]
    public function empty_company_name_error_message_uses_label(): void
    {
        $input = $this->minimumValidPayload();
        $input['company_name'] = '';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('company_name', $errors);
        $this->assertSame('Company name is required.', $errors['company_name']);
    }

    #[Test]
    public function first_name_over_255_chars_is_rejected(): void
    {
        $input = $this->minimumValidPayload();
        $input['first_name'] = str_repeat('a', 256);

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('first_name', $errors);
        $this->assertSame('First name must be under 255 characters.', $errors['first_name']);
    }

    #[Test]
    public function invalid_company_size_is_rejected(): void
    {
        $input = $this->minimumValidPayload();
        $input['company_size'] = 'mega';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('company_size', $errors);
        $this->assertSame('Please select your company size.', $errors['company_size']);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function validCompanySizesProvider(): array
    {
        return [
            'small'            => ['small'],
            'mid'              => ['mid'],
            'enterprise'       => ['enterprise'],
            'large_enterprise' => ['large_enterprise'],
        ];
    }

    #[Test]
    #[DataProvider('validCompanySizesProvider')]
    public function all_four_valid_company_sizes_pass(string $size): void
    {
        $input = $this->minimumValidPayload();
        $input['company_size'] = $size;

        [$errors, $clean] = Validator::validate($input);

        $this->assertArrayNotHasKey('company_size', $errors);
        $this->assertSame($size, $clean['company_size']);
    }

    #[Test]
    public function invalid_role_is_rejected(): void
    {
        $input = $this->minimumValidPayload();
        $input['role'] = 'ceo';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('role', $errors);
        $this->assertSame('Please select your role.', $errors['role']);
    }

    #[Test]
    public function invalid_use_case_is_rejected(): void
    {
        $input = $this->minimumValidPayload();
        $input['use_case'] = 'weather_forecasting';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('use_case', $errors);
        $this->assertSame('Please select a primary use case.', $errors['use_case']);
    }

    #[Test]
    public function notes_over_500_chars_is_rejected(): void
    {
        $input = $this->minimumValidPayload();
        $input['notes'] = str_repeat('a', 501);

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('notes', $errors);
    }

    #[Test]
    public function empty_notes_is_allowed_and_cleaned_to_empty_string(): void
    {
        $input = $this->minimumValidPayload();
        unset($input['notes']);

        [$errors, $clean] = Validator::validate($input);

        $this->assertArrayNotHasKey('notes', $errors);
        $this->assertSame('', $clean['notes']);
    }

    #[Test]
    public function honeypot_filled_surfaces_spam_error(): void
    {
        $input = $this->minimumValidPayload();
        $input['website_url'] = 'http://spam.com';

        [$errors, ] = Validator::validate($input);

        $this->assertArrayHasKey('_honeypot', $errors);
        $this->assertSame('Spam detected.', $errors['_honeypot']);
    }

    #[Test]
    public function valid_full_submission_passes_with_all_fields_clean(): void
    {
        $input = $this->minimumValidPayload();
        $input['notes'] = 'We are evaluating source-to-contract vendors.';

        [$errors, $clean] = Validator::validate($input);

        $this->assertSame([], $errors);
        $this->assertArrayHasKey('first_name', $clean);
        $this->assertArrayHasKey('last_name', $clean);
        $this->assertArrayHasKey('email', $clean);
        $this->assertArrayHasKey('company_name', $clean);
        $this->assertArrayHasKey('company_size', $clean);
        $this->assertArrayHasKey('role', $clean);
        $this->assertArrayHasKey('use_case', $clean);
        $this->assertArrayHasKey('notes', $clean);
        $this->assertCount(8, $clean);
    }

    #[Test]
    public function clean_and_errors_are_mutually_exclusive_per_field(): void
    {
        $input = $this->minimumValidPayload();
        $input['email'] = 'alice@gmail.com';

        [$errors, $clean] = Validator::validate($input);

        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayNotHasKey('email', $clean);
    }
}
