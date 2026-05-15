<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model\Import\Product\Validator;

use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;

/**
 * Rejects negative or non-numeric values for product price-type fields during import
 */
class Price extends AbstractImportValidator implements RowValidatorInterface
{
    /**
     * Error code for negative / non-numeric price-type values
     */
    public const ERROR_NEGATIVE_PRICE_VALUE = 'invalidNegativePriceValue';

    /**
     * Last price column that failed validation for the current import row
     *
     * @var string|null
     */
    private static ?string $importFailedPriceField = null;

    /**
     * Price-type fields that must not be negative
     */
    private const array PRICE_FIELDS = [
        'price',
        'special_price',
        'cost',
        'map_price',
        'minimal_price',
        'msrp_price',
        'msrp',
    ];

    /**
     * @var string|null
     */
    private ?string $failedField = null;

    /**
     * Attribute code / column that failed negative-price validation on the last {@see isValid} call.
     */
    public function getFailedField(): ?string
    {
        return $this->failedField;
    }

    /**
     * Column that failed price validation on the last import row
     */
    public static function getImportFailedPriceField(): ?string
    {
        return self::$importFailedPriceField;
    }

    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        $this->_clearMessages();
        $this->failedField = null;
        self::$importFailedPriceField = null;
        $emptyConstant = $this->context->getEmptyAttributeValueConstant();

        foreach (self::PRICE_FIELDS as $field) {
            if (!isset($value[$field])) {
                continue;
            }
            $fieldValue = $value[$field];
            if ($fieldValue === '' || $fieldValue === null || $fieldValue === $emptyConstant) {
                continue;
            }
            if (!is_numeric($fieldValue) || (float)$fieldValue < 0) {
                $this->failedField = $field;
                self::$importFailedPriceField = $field;
                $this->_addMessages([self::ERROR_NEGATIVE_PRICE_VALUE]);
                return false;
            }
        }

        return true;
    }
}
