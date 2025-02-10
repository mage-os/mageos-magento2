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
        if (
            array_key_exists('name', $value) &&
            empty($value['name']) &&
            $value['name'] !== $this->context->getEmptyAttributeValueConstant()
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
}
