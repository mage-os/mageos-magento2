<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Model\Import\Product\Validator;

use Magento\CatalogImportExport\Model\Import\Product;
use Magento\CatalogImportExport\Model\Import\Product\Validator\Name;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    /**
     * @param $expected
     * @param $value
     * @return void
     * @throws Exception
     * @dataProvider getRowData
     */
    public function testIsValid($expected, $value): void
    {
        $nameValidator = new Name();
        $context = $this->createMock(Product::class);
        $context->expects($this->any())->method('getEmptyAttributeValueConstant')->willReturn('|');
        $context->expects($this->any())->method('retrieveMessageTemplate')->willReturn('%s error %s');
        $nameValidator->init($context);
        $this->assertSame($expected, $nameValidator->isValid($value));
    }

    /**
     * @return array[]
     */
    public static function getRowData(): array
    {
        return [
            [
                false,
                ['name' => null]
            ],
            [
                true,
                ['name' => 'anything goes here']
            ]
        ];
    }
}
