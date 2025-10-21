<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);


namespace Magento\CatalogRule\Test\Unit\Plugin\Model\Product;

use Magento\CatalogRule\Model\Indexer\Product\ProductRuleProcessor;
use Magento\CatalogRule\Plugin\Model\Product\Action;
use Magento\Catalog\Test\Unit\Helper\ProductActionTestHelper;
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
        $this->productRuleProcessor = $this->createPartialMock(
            ProductRuleProcessor::class,
            ['reindexList']
        );

        $this->action = new Action($this->productRuleProcessor);
    }

    public function testAfterUpdateAttributes()
    {
        $subject = $this->createMock(\Magento\Catalog\Model\Product\Action::class);

        $result = new ProductActionTestHelper();

        $result->setAttributesData([]);

        $this->productRuleProcessor->expects($this->never())
            ->method('reindexList');

        $this->action->afterUpdateAttributes($subject, $result);
    }

    public function testAfterUpdateAttributesWithPrice()
    {
        $productIds = [1, 2, 3];
        $subject = $this->createMock(\Magento\Catalog\Model\Product\Action::class);

        $result = new ProductActionTestHelper();

        $result->setAttributesData(['price' => 100]);
        $result->setProductIds($productIds);

        $this->productRuleProcessor->expects($this->once())
            ->method('reindexList')
            ->with($productIds);

        $this->action->afterUpdateAttributes($subject, $result);
    }
}
