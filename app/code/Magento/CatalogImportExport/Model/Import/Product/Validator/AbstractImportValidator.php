<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogImportExport\Model\Import\Product\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;

abstract class AbstractImportValidator extends AbstractValidator implements RowValidatorInterface
{
    /**
     * @var string|null
     */
    protected ?string $fieldName = null;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product
     */
    protected $context;

    /**
     * @param \Magento\CatalogImportExport\Model\Import\Product $context
     * @return $this
     */
    public function init($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }
}
