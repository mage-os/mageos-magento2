<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\Validator;

use Magento\Customer\Model\Validator\Telephone;
use Magento\Customer\Model\Customer;
use Magento\Customer\Test\Unit\Helper\CustomerTestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Customer telephone validator tests
 */
class TelephoneTest extends TestCase
{
    /**
     * @var Telephone
     */
    private Telephone $nameValidator;

    /**
     * @var CustomerTestHelper
     */
    private CustomerTestHelper $customerMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->nameValidator = new Telephone();
        $this->customerMock = new CustomerTestHelper();
    }

    /**
     * Test for allowed apostrophe and other punctuation characters in customer names
     *
     * @param string $telephone
     * @param string $message
     * @return void
     */
    #[DataProvider('expectedPunctuationInNamesDataProvider')]
    public function testValidateCorrectPunctuationInNames(
        string $telephone,
        string $message
    ): void {
        $this->customerMock->setTelephone($telephone);

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
                'telephone' => '(1)99887766',
                'message' => 'parentheses must be allowed in telephone'
            ],
            [
                'telephone' => '+6255554444',
                'message' => 'plus sign be allowed in telephone'
            ],
            [
                'telephone' => '555-555-555',
                'message' => 'hyphen must be allowed in telephone'
            ],
            [
                'telephone' => '123456789',
                'message' => 'Digits (numbers) must be allowed in telephone'
            ]
        ];
    }
}
