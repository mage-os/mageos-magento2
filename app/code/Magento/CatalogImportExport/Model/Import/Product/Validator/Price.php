<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model\Import\Product\Validator;

use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;

/**
 * Rejects negative values for product price-type fields during import
 */
class Price extends AbstractImportValidator implements RowValidatorInterface
{
    /**
     * Error code for negative price-type values
     */
    public const ERROR_NEGATIVE_PRICE_VALUE = 'invalidNegativePriceValue';

    /**
     * Price-type fields that must not be negative
     */
    private const PRICE_FIELDS = [
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
     * @inheritdoc
     */
    public function isValid($value)
    {
        $this->_clearMessages();
        $this->failedField = null;
        $emptyConstant = $this->context->getEmptyAttributeValueConstant();

        foreach (self::PRICE_FIELDS as $field) {
            if (!isset($value[$field])) {
                continue;
            }
            $fieldValue = $value[$field];
            if (is_string($fieldValue)) {
                $fieldValue = trim($fieldValue);
            }
            if ($fieldValue === '' || $fieldValue === null || $fieldValue === $emptyConstant) {
                continue;
            }
            if (is_numeric($fieldValue) && (float)$fieldValue < 0) {
                $this->failedField = $field;
                $this->_addMessages([self::ERROR_NEGATIVE_PRICE_VALUE]);
                return false;
            }
        }

        return true;
    }
}
