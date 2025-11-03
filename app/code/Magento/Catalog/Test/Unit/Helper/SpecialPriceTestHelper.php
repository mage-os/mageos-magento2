<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Price\SpecialPrice;

class SpecialPriceTestHelper extends SpecialPrice
{
    /**
     * @var mixed
     */
    private $entityLinkField = null;

    /**
     * @var mixed
     */
    private $getResult = null;

    /**
     * @var mixed
     */
    private $updateResult = null;

    /**
     * @var mixed
     */
    private $deleteResult = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getEntityLinkField()
    {
        return $this->entityLinkField;
    }

    /**
     * @param mixed $field
     * @return $this
     */
    public function setEntityLinkField($field)
    {
        $this->entityLinkField = $field;
        return $this;
    }

    /**
     * @param array $skus
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get(array $skus)
    {
        return $this->getResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setGetResult($result)
    {
        $this->getResult = $result;
        return $this;
    }

    /**
     * @param array $prices
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function update(array $prices)
    {
        return $this->updateResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setUpdateResult($result)
    {
        $this->updateResult = $result;
        return $this;
    }

    /**
     * @param array $prices
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete(array $prices)
    {
        return $this->deleteResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setDeleteResult($result)
    {
        $this->deleteResult = $result;
        return $this;
    }
}

