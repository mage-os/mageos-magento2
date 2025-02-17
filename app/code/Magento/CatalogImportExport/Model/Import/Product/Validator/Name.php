<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model\Import\Product\Validator;

use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;

class Name extends AbstractImportValidator implements RowValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        $this->_clearMessages();
        if (!$this->hasNameValue($value) &&
            !$this->hasCustomOptions($value) &&
            (!isset($value['store_view_code']) || $value['store_view_code'] != 'default')
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
        return array_key_exists('name', $rowData) &&
            !empty($rowData['name']) &&
            $rowData['name'] !== $this->context->getEmptyAttributeValueConstant();
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
