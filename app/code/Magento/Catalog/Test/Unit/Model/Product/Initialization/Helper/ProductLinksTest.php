<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Initialization\Helper;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class ProductLinksTest extends TestCase
{
    /**
     * @var ProductLinks
     */
    private $model;

    public function testInitializeLinks()
    {
        $links = ['related' => ['data'], 'upsell' => ['data'], 'crosssell' => ['data']];
        $this->assertInstanceOf(
            Product::class,
            $this->model->initializeLinks($this->getMockedProduct(), $links)
        );
    }

    protected function setUp(): void
    {
        $helper = new ObjectManager($this);
        $this->model = $helper->getObject(ProductLinks::class);
    }

    /**
     * @return Product
     */
    private function getMockedProduct()
    {
        $mock = new class extends Product {
            private $relatedReadonly = false;
            private $upsellReadonly = false;
            private $crosssellReadonly = false;
            private $crossSellLinkData = null;
            private $upSellLinkData = null;
            private $relatedLinkData = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getRelatedReadonly()
            {
                return $this->relatedReadonly;
            }
            
            public function setRelatedReadonly($value)
            {
                $this->relatedReadonly = $value;
                return $this;
            }
            
            public function getUpsellReadonly()
            {
                return $this->upsellReadonly;
            }
            
            public function setUpsellReadonly($value)
            {
                $this->upsellReadonly = $value;
                return $this;
            }
            
            public function getCrosssellReadonly()
            {
                return $this->crosssellReadonly;
            }
            
            public function setCrosssellReadonly($value)
            {
                $this->crosssellReadonly = $value;
                return $this;
            }
            
            public function setCrossSellLinkData($data)
            {
                $this->crossSellLinkData = $data;
                return $this;
            }
            
            public function getCrossSellLinkData()
            {
                return $this->crossSellLinkData;
            }
            
            public function setUpSellLinkData($data)
            {
                $this->upSellLinkData = $data;
                return $this;
            }
            
            public function getUpSellLinkData()
            {
                return $this->upSellLinkData;
            }
            
            public function setRelatedLinkData($data)
            {
                $this->relatedLinkData = $data;
                return $this;
            }
            
            public function getRelatedLinkData()
            {
                return $this->relatedLinkData;
            }
        };

        return $mock;
    }
}
