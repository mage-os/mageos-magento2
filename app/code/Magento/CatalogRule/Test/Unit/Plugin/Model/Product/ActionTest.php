<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);


namespace Magento\CatalogRule\Test\Unit\Plugin\Model\Product;

use Magento\CatalogRule\Model\Indexer\Product\ProductRuleProcessor;
use Magento\CatalogRule\Plugin\Model\Product\Action;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    /** @var Action */
    protected $action;

    /** @var ProductRuleProcessor|MockObject */
    protected $productRuleProcessor;

    protected function setUp(): void
    {
        $this->productRuleProcessor = $this->getMockBuilder(
            ProductRuleProcessor::class
        )->disableOriginalConstructor()
            ->onlyMethods(['reindexList'])
            ->getMock();

        $this->action = new Action($this->productRuleProcessor);
    }

    public function testAfterUpdateAttributes()
    {
        $subject = $this->getMockBuilder(\Magento\Catalog\Model\Product\Action::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Create anonymous class extending Product\Action with dynamic methods
        $result = new class extends \Magento\Catalog\Model\Product\Action {
            private $attributesData = [];
            private $productIds = [];

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getAttributesData()
            {
                return $this->attributesData;
            }

            public function setAttributesData($value)
            {
                $this->attributesData = $value;
                return $this;
            }

            public function getProductIds()
            {
                return $this->productIds;
            }

            public function setProductIds($value)
            {
                $this->productIds = $value;
                return $this;
            }
        };

        $result->setAttributesData([]);

        $this->productRuleProcessor->expects($this->never())
            ->method('reindexList');

        $this->action->afterUpdateAttributes($subject, $result);
    }

    public function testAfterUpdateAttributesWithPrice()
    {
        $productIds = [1, 2, 3];
        $subject = $this->getMockBuilder(\Magento\Catalog\Model\Product\Action::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Create anonymous class extending Product\Action with dynamic methods
        $result = new class extends \Magento\Catalog\Model\Product\Action {
            private $attributesData = [];
            private $productIds = [];

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getAttributesData()
            {
                return $this->attributesData;
            }

            public function setAttributesData($value)
            {
                $this->attributesData = $value;
                return $this;
            }

            public function getProductIds()
            {
                return $this->productIds;
            }

            public function setProductIds($value)
            {
                $this->productIds = $value;
                return $this;
            }
        };

        $result->setAttributesData(['price' => 100]);
        $result->setProductIds($productIds);

        $this->productRuleProcessor->expects($this->once())
            ->method('reindexList')
            ->with($productIds);

        $this->action->afterUpdateAttributes($subject, $result);
    }
}
