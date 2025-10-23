<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\Catalog\Model\Layer\Filter\DataProvider\Price;

/**
 * Mock class for Price DataProvider with additional methods
 */
class PriceDataProviderTestHelper extends Price
{
    /**
     * @var mixed
     */
    private $priceId = null;
    /**
     * @var mixed
     */
    private $price = null;

    /**
     * Mock method for setPriceId
     *
     * @param mixed $priceId
     * @return $this
     */
    public function setPriceId($priceId)
    {
        $this->priceId = $priceId;
        return $this;
    }

    /**
     * Mock method for getPrice
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the price value
     *
     * @param mixed $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Required method from Price
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}
