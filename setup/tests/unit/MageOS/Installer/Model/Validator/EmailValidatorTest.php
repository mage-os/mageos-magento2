<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Validator;

use MageOS\Installer\Model\Validator\EmailValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for EmailValidator
 */
class EmailValidatorTest extends TestCase
{
    private EmailValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new EmailValidator();
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function test_accepts_valid_emails(string $email): void
    {
        $result = $this->validator->validate($email);

        $this->assertTrue($result['valid'], "Email '{$email}' should be valid");
        $this->assertNull($result['error']);
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function test_rejects_invalid_emails(string $email, string $expectedError): void
    {
        $result = $this->validator->validate($email);

        $this->assertFalse($result['valid']);
        $this->assertEquals($expectedError, $result['error']);
    }

    public function test_rejects_empty_email(): void
    {
        $result = $this->validator->validate('');

        $this->assertFalse($result['valid']);
        $this->assertEquals('Email address cannot be empty', $result['error']);
    }

    public function test_rejects_email_without_at_sign(): void
    {
        $result = $this->validator->validate('invalidemail.com');

        $this->assertFalse($result['valid']);
        $this->assertEquals('Invalid email address format', $result['error']);
    }

    public function test_rejects_email_with_multiple_at_signs(): void
    {
        $result = $this->validator->validate('user@@example.com');

        $this->assertFalse($result['valid']);
        $this->assertEquals('Invalid email address format', $result['error']);
    }

    public static function validEmailProvider(): array
    {
        return [
            'standard email' => ['user@example.com'],
            'subdomain' => ['admin@mail.example.com'],
            'with plus' => ['user+tag@example.com'],
            'with dots' => ['first.last@example.com'],
            'with numbers' => ['user123@example.com'],
            'short domain' => ['a@b.co'],
            'with hyphen' => ['user@my-domain.com'],
            'uppercase' => ['User@Example.COM'],
        ];
    }

    public static function invalidEmailProvider(): array
    {
        return [
            'empty' => ['', 'Email address cannot be empty'],
            'no at' => ['invalidemail.com', 'Invalid email address format'],
            'no domain' => ['user@', 'Invalid email address format'],
            'no user' => ['@example.com', 'Invalid email address format'],
            'spaces' => ['user @example.com', 'Invalid email address format'],
            'missing tld' => ['user@example', 'Invalid email address format'],
        ];
    }
}
