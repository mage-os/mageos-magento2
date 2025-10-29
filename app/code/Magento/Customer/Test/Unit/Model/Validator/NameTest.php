<?php
/**
 * Copyright 2021 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\Validator;

use Magento\Customer\Model\Validator\Name;
use Magento\Customer\Model\Customer;
use Magento\Customer\Test\Unit\Helper\CustomerTestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Customer name validator tests
 */
class NameTest extends TestCase
{
    /**
     * @var Name
     */
    private Name $nameValidator;

    /**
     * @var CustomerTestHelper
     */
    private CustomerTestHelper $customerMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->nameValidator = new Name();
        $this->customerMock = new CustomerTestHelper();
    }

    /**
     * Test for allowed apostrophe and other punctuation characters in customer names
     *
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     * @param string $message
     * @return void
     */
    #[DataProvider('expectedPunctuationInNamesDataProvider')]
    public function testValidateCorrectPunctuationInNames(
        string $firstName,
        string $middleName,
        string $lastName,
        string $message
    ): void {
        $this->customerMock->setFirstname($firstName);
        $this->customerMock->setMiddlename($middleName);
        $this->customerMock->setLastname($lastName);

        $isValid = $this->nameValidator->isValid($this->customerMock);
        $this->assertTrue($isValid, $message);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function expectedPunctuationInNamesDataProvider(): array
    {
        return [
            [
                'firstName' => 'John',
                'middleName' => '',
                'lastName' => 'O’Doe',
                'message' => 'Inclined apostrophe must be allowed in names (iOS Smart Punctuation compatibility)'
            ],
            [
                'firstName' => 'John',
                'middleName' => '',
                'lastName' => 'O\'Doe',
                'message' => 'Legacy straight apostrophe must be allowed in names'
            ],
            [
                'firstName' => 'John',
                'middleName' => '',
                'lastName' => 'O`Doe',
                'message' => 'Grave accent back quote character must be allowed in names'
            ],
            [
                'firstName' => 'John & Smith',
                'middleName' => '',
                'lastName' => 'O`Doe',
                'message' => 'Special character ampersand(&) must be allowed in names'
            ]
        ];
    }
}
