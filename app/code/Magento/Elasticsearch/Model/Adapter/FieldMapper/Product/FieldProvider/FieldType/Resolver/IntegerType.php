<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\Resolver;

use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeAdapter;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ConverterInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ResolverInterface;

/**
 * Integer type resolver.
 * @deprecated Elasticsearch is no longer supported by Adobe
 * @see this class will be responsible for ES only
 */
class IntegerType implements ResolverInterface
{
    /**
     * @var ConverterInterface
     */
    private $fieldTypeConverter;

    /**
     * @param ConverterInterface $fieldTypeConverter
     */
    public function __construct(ConverterInterface $fieldTypeConverter)
    {
        $this->fieldTypeConverter = $fieldTypeConverter;
    }

    /**
     * Get integer field type.
     *
     * @param AttributeAdapter $attribute
     * @return string
     */
    public function getFieldType(AttributeAdapter $attribute): ?string
    {
        if ($attribute->isIntegerType() || $attribute->isBooleanType()) {
            return $this->fieldTypeConverter->convert(ConverterInterface::INTERNAL_DATA_TYPE_INT);
        }

        return null;
    }
}
