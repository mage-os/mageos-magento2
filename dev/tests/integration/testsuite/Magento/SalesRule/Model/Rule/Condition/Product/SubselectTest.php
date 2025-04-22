<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Rule\Condition\Product;

use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\SalesRule\Model\Rule\Condition\Product as SalesRuleProduct;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for Subselect::validate() method returning true.
 *
 * @magentoAppArea frontend
 * @magentoDbIsolation enabled
 */
class SubselectTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Subselect
     */
    private $subselectCondition;

    /**
     * @var SalesRuleProduct
     */
    private $productCondition;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->productCondition = $this->objectManager->create(SalesRuleProduct::class);
        $this->subselectCondition = $this->objectManager->create(
            Subselect::class,
            [
                'context' => $this->objectManager->get(\Magento\Rule\Model\Condition\Context::class),
                'ruleConditionProduct' => $this->productCondition
            ]
        );
    }

    /**
     * Test validate() method returning true for total quantity >= 2 with single shipping.
     *
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testValidateReturnsTrueForSufficientQuantity()
    {
        $this->subselectCondition->setData([
            'attribute' => 'qty',
            'operator' => '>=',
            'value' => 2,
            'aggregator' => 'all',
        ]);
        $productCondition = $this->objectManager->create(SalesRuleProduct::class);
        $productCondition->setData([
            'attribute' => 'sku',
            'operator' => '==',
            'value' => 'simple',
        ]);
        $this->subselectCondition->setConditions([$productCondition]);
        $productRepository = $this->objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $product = $productRepository->get('simple');
        $product->setPrice(100.50);
        $this->assertNotNull($product->getId());
        $quote = $this->objectManager->create(Quote::class);
        $quote->setStoreId(1)
            ->setIsActive(true)
            ->setIsMultiShipping(false)
            ->setReservedOrderId('test_quote')
            ->addProduct($product, 2);
        $quote->collectTotals()->save();
        $this->assertNotNull($quote->getId());
        $this->assertNotEmpty($quote->getAllVisibleItems());
        $quoteItem = $quote->getAllVisibleItems()[0];
        $this->assertNotNull($quoteItem);
        $this->assertNotNull($quoteItem->getProduct());
        $this->assertEquals(2, $quoteItem->getQty());
        $this->assertNotNull($quoteItem->getQuote());
        $result = $this->subselectCondition->validate($quoteItem);
        $this->assertTrue($result);
    }
}
