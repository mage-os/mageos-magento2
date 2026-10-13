<?php
/**
 * Copyright 2025 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Rule\Condition\Product;

use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\SalesRule\Model\Rule\Condition\Product as SalesRuleProduct;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for Subselect::validate() on the multi-address checkout path.
 *
 * These cover the two scenarios called out in Mage-OS issue #355 that the unit tests, which
 * stub the item list and totals, cannot exercise: the ACP2E-3986 ordering regression and the
 * bundle child-row double count. They build real multi-address quotes and validate the real
 * shipping address, so the assertions depend on genuine quote totals.
 *
 * @magentoAppArea frontend
 * @magentoDbIsolation disabled
 */
class MultishippingSubselectTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Subselect
     */
    private $subselect;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->subselect = $this->objectManager->create(Subselect::class);
    }

    /**
     * A matching item followed by a non-matching item must still satisfy the subselect.
     *
     * Before the fix the multishipping result depended on the last item's condition flag, so the
     * trailing non-matching line dropped free shipping (ACP2E-3986). Summing visible items once and
     * checking the total must keep the rule matching regardless of item order.
     *
     * @magentoDataFixture Magento/SalesRule/_files/multishipping_quote_matching_then_nonmatching.php
     */
    public function testMultishippingMatchesWhenNonMatchingItemFollowsMatchingItem(): void
    {
        $this->subselect->setData([
            'attribute' => 'qty',
            'operator' => '>=',
            'value' => 2,
            'aggregator' => 'all',
        ]);
        $productCondition = $this->objectManager->create(SalesRuleProduct::class);
        $productCondition->setData([
            'attribute' => 'sku',
            'operator' => '==',
            'value' => 'subselect_match',
        ]);
        $this->subselect->setConditions([$productCondition]);

        $shippingAddress = $this->getMultishippingAddress('multishipping_subselect_ordering');

        // The matching line carries qty 2, so the subselect (qty >= 2) must match even though the
        // non-matching line is evaluated afterwards.
        $this->assertTrue($this->subselect->validate($shippingAddress));
    }

    /**
     * A dynamic bundle's child rows must not be added on top of the visible parent row.
     *
     * The bundle selects simple1 (10) and simple2 (20); its visible row total is 30 while the two
     * child address rows carry 10 and 20. Counting only visible items yields 30; the pre-fix
     * getAllItems() path yielded 60.
     *
     * @magentoDataFixture Magento/SalesRule/_files/multishipping_quote_with_dynamic_bundle.php
     */
    public function testMultishippingDoesNotDoubleCountBundleChildRows(): void
    {
        $shippingAddress = $this->getMultishippingAddress('multishipping_subselect_bundle');

        // Exactly the visible bundle total (30) must match, and anything above it must not — which
        // only holds if the child rows (10 + 20) are excluded rather than added to make 60.
        $this->subselect->setData([
            'attribute' => 'base_row_total',
            'operator' => '==',
            'value' => 30,
            'aggregator' => 'all',
        ]);
        $this->subselect->setConditions([]);
        $this->assertTrue(
            $this->subselect->validate($shippingAddress),
            'Visible bundle row total should be counted once (30).'
        );

        $this->subselect->setData([
            'attribute' => 'base_row_total',
            'operator' => '==',
            'value' => 60,
            'aggregator' => 'all',
        ]);
        $this->subselect->setConditions([]);
        $this->assertFalse(
            $this->subselect->validate($shippingAddress),
            'Bundle child rows (10 + 20) must not be double counted on top of the parent.'
        );
    }

    /**
     * Load a persisted multishipping quote by reserved order id and return its shipping address.
     *
     * @param string $reservedOrderId
     * @return \Magento\Quote\Model\Quote\Address
     */
    private function getMultishippingAddress(string $reservedOrderId)
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(QuoteFactory::class)->create();
        $this->objectManager->get(QuoteResource::class)->load($quote, $reservedOrderId, 'reserved_order_id');
        $this->assertTrue((bool) $quote->getIsMultiShipping(), 'Fixture quote must be multi-address.');

        $addresses = $quote->getAllShippingAddresses();
        $shippingAddress = reset($addresses);
        $this->assertNotFalse($shippingAddress, 'Fixture quote must have a shipping address.');

        return $shippingAddress;
    }
}
