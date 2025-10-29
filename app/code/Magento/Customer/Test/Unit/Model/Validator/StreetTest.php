<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\Validator;

use Magento\Customer\Model\Validator\Street;
use Magento\Customer\Model\Customer;
use Magento\Customer\Test\Unit\Helper\CustomerTestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Customer street validator tests
 */
class StreetTest extends TestCase
{
    /**
     * @var Street
     */
    private Street $nameValidator;

    /**
     * @var CustomerTestHelper
     */
    private CustomerTestHelper $customerMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->nameValidator = new Street();
        $this->customerMock = new CustomerTestHelper();
    }

    /**
     * Test for allowed apostrophe and other punctuation characters in customer names
     *
     * @param array<int, string> $street
     * @param string $message
     * @return void
     */
    #[DataProvider('expectedPunctuationInNamesDataProvider')]
    public function testValidateCorrectPunctuationInNames(
        array $street,
        string $message
    ): void {
        $this->customerMock->setStreet($street);

        $isValid = $this->nameValidator->isValid($this->customerMock);
        $this->assertTrue($isValid, $message);
    }

    /**
     * @return array<int, array<string, array<int, string>|string>>
     */
    public static function expectedPunctuationInNamesDataProvider(): array
    {
        return [
            [
                'street' => [
                    "123 Rue de l'Étoile",
                    "Ville d'Ölives, Çôte d'Azur",
                    "Çôte d'Azur"
                ],
                'message' => 'Unicode marks and Unicode letters must be allowed in street'
            ],
            [
                'street' => [
                    '876 Elm Way, Redwood Lodge',
                    '456 Pine Street, Serenity Cottage',
                    '321 Birch Boulevard, Willow Retreat'
                ],
                'message' => 'Comma must be allowed in street'
            ],
            [
                'street' => [
                    '321 Birch Boulevard-Retreat',
                    '234 Spruce Place-Residence',
                    '456 Pine Street-Haven'
                ],
                'message' => 'Hyphen must be allowed in street'
            ],
            [
                'street' => [
                    '1234 Elm St.',
                    'Main. Street',
                    '1234 Elm St'
                ],
                'message' => 'Period must be allowed in street'
            ],
            [
                'street' => [
                    'O\'Connell Street',
                    'O`Connell Street',
                    '321 Birch Boulevard ’Willow Retreat’'
                ],
                'message' => 'quotes must be allowed in street'
            ],
            [
                'street' => [
                    '123 Main Street & Elm Avenue',
                    '456 Pine Street & Maple Avenue',
                    '789 Oak Lane & Cedar Road'
                ],
                'message' => 'Ampersand must be allowed in street'
            ],
            [
                'street' => [
                    'Oak Lane Space',
                    'Birch Boulevard Space',
                    'Spruce Place'
                ],
                'message' => 'Whitespace must be allowed in street'
            ],
            [
                'street' => [
                    '234 Spruce Place',
                    '321 Birch Boulevard',
                    '876 Elm Way'
                ],
                'message' => 'Digits must be allowed in street'
            ]
        ];
    }
}
