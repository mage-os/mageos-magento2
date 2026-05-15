<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Model\Import\Product\Validator;

use Magento\CatalogImportExport\Model\Import\Product;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;
use Magento\CatalogImportExport\Model\Import\Product\Validator\Price;
use Magento\ImportExport\Model\Import;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    /**
     * @var Price
     */
    private Price $price;

    protected function setUp(): void
    {
        $this->price = new Price();
        $contextStub = $this->createMock(Product::class);
        $contextStub->method('getEmptyAttributeValueConstant')
            ->willReturn(Import::DEFAULT_EMPTY_ATTRIBUTE_VALUE_CONSTANT);
        $contextStub->method('retrieveMessageTemplate')->willReturn('some template');
        $this->price->init($contextStub);
    }

    /**
     * @param bool $expectedResult
     * @param array<string, mixed> $value
     */
    #[DataProvider('isValidDataProvider')]
    public function testIsValid(bool $expectedResult, array $value): void
    {
        $this->assertSame($expectedResult, $this->price->isValid($value));
    }

    /**
     * @return array<int, array{0: bool, 1: array<string, mixed>}>
     */
    public static function isValidDataProvider(): array
    {
        $empty = Import::DEFAULT_EMPTY_ATTRIBUTE_VALUE_CONSTANT;

        return [
            'positive_price_only' => [true, ['price' => '10.5']],
            'zero_price' => [true, ['price' => '0']],
            'zero_price_int' => [true, ['price' => 0]],
            'price_unset_other_keys' => [true, ['sku' => 'test']],
            'empty_string_price_skipped' => [true, ['price' => '']],
            'null_price_skipped' => [true, ['price' => null]],
            'empty_constant_price_skipped' => [true, ['price' => $empty]],
            'negative_price' => [false, ['price' => '-1']],
            'negative_price_float' => [false, ['price' => -150]],
            'negative_special_price_with_valid_price' => [false, ['price' => '10', 'special_price' => '-0.01']],
            'negative_cost_with_valid_price' => [false, ['price' => '10', 'cost' => -5]],
            'negative_map_price' => [false, ['map_price' => '-2']],
            'negative_msrp_price' => [false, ['msrp_price' => '-3']],
            'negative_msrp' => [false, ['msrp' => '-4']],
            'negative_minimal_price' => [false, ['minimal_price' => '-1']],
            'non_numeric_price' => [false, ['price' => 'abc']],
            'non_numeric_special_price' => [false, ['price' => '1', 'special_price' => 'not-a-number']],
        ];
    }

    public function testGetFailedFieldIsNullWhenValid(): void
    {
        $this->assertTrue($this->price->isValid(['price' => '99']));
        $this->assertNull($this->price->getFailedField());
        $this->assertSame([], $this->price->getMessages());
    }

    public function testGetFailedFieldAndMessageWhenNegativePrice(): void
    {
        $this->assertFalse($this->price->isValid(['price' => '-10']));
        $this->assertSame('price', $this->price->getFailedField());
        $this->assertSame([RowValidatorInterface::ERROR_NEGATIVE_PRICE_VALUE], $this->price->getMessages());
    }

    public function testGetFailedFieldWhenFirstFailingFieldIsSpecialPrice(): void
    {
        $this->assertFalse($this->price->isValid(['price' => '5', 'special_price' => '-1']));
        $this->assertSame('special_price', $this->price->getFailedField());
    }

    public function testFailedFieldClearedOnSuccessfulSecondCall(): void
    {
        $this->assertFalse($this->price->isValid(['price' => '-1']));
        $this->assertSame('price', $this->price->getFailedField());
        $this->assertTrue($this->price->isValid(['price' => '10']));
        $this->assertNull($this->price->getFailedField());
    }
}
