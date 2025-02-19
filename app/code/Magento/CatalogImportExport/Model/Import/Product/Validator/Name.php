<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model\Import\Product\Validator;

use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;
use Magento\CatalogImportExport\Model\Import\Product\SkuStorage;
use Magento\CatalogImportExport\Model\Import\Product;

class Name extends AbstractImportValidator implements RowValidatorInterface
{
    /**
     * @var SkuStorage
     */
    private SkuStorage $skuStorage;

    /**
     * @param SkuStorage $skuStorage
     */
    public function __construct(SkuStorage $skuStorage)
    {
        $this->skuStorage = $skuStorage;
    }

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        $this->_clearMessages();
        if (!$this->hasNameValue($value) &&
            !$this->hasCustomOptions($value) &&
            !$this->skuStorage->has($value[Product::COL_SKU])
        ) {
            $this->_addMessages(
                [
                    sprintf(
                        $this->context->retrieveMessageTemplate(self::ERROR_INVALID_ATTRIBUTE_TYPE),
                        'name',
                        'not empty'
                    )
                ]
            );
            return false;
        }
        return true;
    }

    /**
     * Check if row data contains name value
     *
     * @param array $rowData
     * @return bool
     */
    private function hasNameValue(array $rowData): bool
    {
        return array_key_exists(Product::COL_NAME, $rowData) &&
            !empty($rowData[Product::COL_NAME]) &&
            $rowData[Product::COL_NAME] !== $this->context->getEmptyAttributeValueConstant();
    }

    /**
     * Check if import data contains custom options
     *
     * @param array $rowData
     * @return bool
     */
    private function hasCustomOptions(array $rowData): bool
    {
        return array_key_exists('custom_options', $rowData) &&
            !empty($rowData['custom_options']) &&
            $rowData['custom_options'] !== $this->context->getEmptyAttributeValueConstant();
    }
}
