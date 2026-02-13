<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Validator;

use MageOS\Installer\Model\Validator\PasswordValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for PasswordValidator
 */
class PasswordValidatorTest extends TestCase
{
    /** @var PasswordValidator */
    private PasswordValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new PasswordValidator();
    }

    /**
     * @dataProvider validPasswordProvider
     */
    public function testAcceptsValidPasswords(string $password): void
    {
        $result = $this->validator->validate($password);

        $this->assertNull($result, "Password '{$password}' should be valid");
    }

    /**
     * @dataProvider invalidPasswordProvider
     */
    public function testRejectsInvalidPasswords(string $password, string $expectedError): void
    {
        $result = $this->validator->validate($password);

        $this->assertNotNull($result);
        $this->assertEquals($expectedError, $result);
    }

    public function testRejectsEmptyPassword(): void
    {
        $result = $this->validator->validate('');

        $this->assertEquals('Password cannot be empty', $result);
    }

    public function testRejectsPasswordTooShort(): void
    {
        $result = $this->validator->validate('abc123');

        $this->assertEquals('Password must be at least 7 characters long', $result);
    }

    public function testRejectsPasswordWithoutLetters(): void
    {
        $result = $this->validator->validate('1234567');

        $this->assertEquals(
            'Password must include both alphabetic and numeric characters (required by Magento)',
            $result
        );
    }

    public function testRejectsPasswordWithoutNumbers(): void
    {
        $result = $this->validator->validate('abcdefg');

        $this->assertEquals(
            'Password must include both alphabetic and numeric characters (required by Magento)',
            $result
        );
    }

    public function testGetStrengthFeedbackForWeakPassword(): void
    {
        $feedback = $this->validator->getStrengthFeedback('abc1234');

        $this->assertEquals(
            'Consider using both uppercase and lowercase letters for better security.',
            $feedback
        );
    }

    public function testGetStrengthFeedbackForMediumPassword(): void
    {
        $feedback = $this->validator->getStrengthFeedback('Abc1234');

        $this->assertEquals(
            'Good password. Consider adding special characters for even better security.',
            $feedback
        );
    }

    public function testGetStrengthFeedbackForStrongPassword(): void
    {
        $feedback = $this->validator->getStrengthFeedback('Abc123!@#');

        $this->assertEquals('✓ Strong password detected!', $feedback);
    }

    public function testGetRequirementsHint(): void
    {
        $hint = $this->validator->getRequirementsHint();

        $this->assertEquals('Must be 7+ characters with both letters and numbers', $hint);
    }

    public static function validPasswordProvider(): array
    {
        return [
            'minimum valid' => ['abc1234'],
            'with uppercase' => ['Abc1234'],
            'with special chars' => ['Abc123!'],
            'long password' => ['abcdefg1234567890'],
            'all character types' => ['Abc123!@#$%'],
            'mixed case numbers' => ['AbC123DeF'],
            'numbers at end' => ['password123'],
            'numbers at start' => ['123password'],
        ];
    }

    public static function invalidPasswordProvider(): array
    {
        return [
            'empty' => ['', 'Password cannot be empty'],
            'too short' => ['ab12', 'Password must be at least 7 characters long'],
            'only letters' => [
                'abcdefg',
                'Password must include both alphabetic and numeric characters (required by Magento)',
            ],
            'only numbers' => [
                '1234567',
                'Password must include both alphabetic and numeric characters (required by Magento)',
            ],
            'only special chars' => [
                '!@#$%^&',
                'Password must include both alphabetic and numeric characters (required by Magento)',
            ],
            '6 chars with alpha+num' => ['abc123', 'Password must be at least 7 characters long'],
        ];
    }
}
