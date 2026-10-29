<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Rule\Condition\Product;

use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
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

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->subselect = $this->objectManager->create(Subselect::class);
    }

    /**
     * A matching item followed by a non-matching item must still satisfy the subselect.
     *
     * Before the fix the multishipping result depended on the last item's condition flag, so the
     * trailing non-matching line dropped free shipping (ACP2E-3986). Only the matching line may be
     * counted: the matching line has qty 2 and the non-matching line qty 1, so counting both gives 3.
     *
     * @magentoDataFixture Magento/SalesRule/_files/multishipping_quote_matching_then_nonmatching.php
     */
    public function testMultishippingMatchesWhenNonMatchingItemFollowsMatchingItem(): void
    {
        $this->subselect->setData([
            'attribute' => 'qty',
            'operator' => '==',
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

        $quote = $this->loadMultishippingQuote('multishipping_subselect_ordering');

        $this->assertTrue($this->subselect->validate($this->getShippingAddress($quote)));
    }

    /**
     * A dynamic bundle's child rows must not be added on top of the visible parent row.
     *
     * The bundle selects simple1 (special price 5.99) and simple2 (special price 15.99); its visible
     * row total is 21.98 while the two child address rows carry 5.99 and 15.99. Counting only visible
     * items yields 21.98; the pre-fix getAllItems() path yielded 43.96.
     *
     * @magentoDataFixture Magento/SalesRule/_files/multishipping_quote_with_dynamic_bundle.php
     */
    public function testMultishippingDoesNotDoubleCountBundleChildRows(): void
    {
        $quote = $this->loadMultishippingQuote('multishipping_subselect_bundle');

        $this->assertBundleRowTotalCountedOnce($this->getShippingAddress($quote));
    }

    /**
     * Same as above, for address items that have not been saved yet.
     *
     * Multi-address checkout rebuilds the shipping addresses and collects totals before saving the
     * quote (Multishipping::setShippingItemsInformation()). At that point the child address items
     * are linked to their parent in memory, but parent_item_id is not assigned yet.
     *
     * @magentoDataFixture Magento/SalesRule/_files/multishipping_quote_with_dynamic_bundle.php
     */
    public function testMultishippingDoesNotDoubleCountUnsavedBundleChildRows(): void
    {
        $quote = $this->loadMultishippingQuote('multishipping_subselect_bundle');
        $quote->removeAddress($this->getShippingAddress($quote)->getId());

        /** @var Address $address */
        $address = $this->objectManager->create(Address::class);
        $address->setFirstname('John')
            ->setLastname('Doe')
            ->setTelephone('0333-233-221')
            ->setStreet(['Main Division 1'])
            ->setCity('Culver City')
            ->setRegion('CA')
            ->setPostcode('90800')
            ->setCountryId('US');
        $quote->addShippingAddress($address);
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            $address->addItem($quoteItem);
        }
        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();

        $childItems = array_filter(
            $address->getAllItems(),
            static fn ($item) => $item->getParentItem() !== null
        );
        $this->assertCount(2, $childItems, 'The unsaved address must carry both bundle child rows.');
        foreach ($childItems as $childItem) {
            $this->assertEmpty($childItem->getParentItemId(), 'Unsaved child rows must not have parent_item_id.');
        }

        $this->assertBundleRowTotalCountedOnce($address);
    }

    /**
     * Assert the subselect sees the visible bundle row total (21.98) once, without its child rows.
     *
     * @param Address $address
     * @return void
     */
    private function assertBundleRowTotalCountedOnce(Address $address): void
    {
        // Exactly the visible bundle total (21.98) must match, and anything above it must not — which
        // only holds if the child rows (5.99 + 15.99) are excluded rather than added to make 43.96.
        $this->subselect->setData([
            'attribute' => 'base_row_total',
            'operator' => '==',
            'value' => 21.98,
            'aggregator' => 'all',
        ]);
        $this->subselect->setConditions([]);
        $this->assertTrue(
            $this->subselect->validate($address),
            'Visible bundle row total should be counted once (21.98).'
        );

        $this->subselect->setData([
            'attribute' => 'base_row_total',
            'operator' => '>',
            'value' => 21.98,
            'aggregator' => 'all',
        ]);
        $this->subselect->setConditions([]);
        $this->assertFalse(
            $this->subselect->validate($address),
            'Bundle child rows (5.99 + 15.99) must not be double counted on top of the parent.'
        );
    }

    /**
     * Load a persisted multishipping quote by reserved order id.
     *
     * @param string $reservedOrderId
     * @return Quote
     */
    private function loadMultishippingQuote(string $reservedOrderId): Quote
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(QuoteFactory::class)->create();
        $this->objectManager->get(QuoteResource::class)->load($quote, $reservedOrderId, 'reserved_order_id');
        $this->assertTrue((bool) $quote->getIsMultiShipping(), 'Fixture quote must be multi-address.');

        return $quote;
    }

    /**
     * Get the quote's first shipping address.
     *
     * @param Quote $quote
     * @return Address
     */
    private function getShippingAddress(Quote $quote): Address
    {
        $addresses = $quote->getAllShippingAddresses();
        $shippingAddress = reset($addresses);
        $this->assertNotFalse($shippingAddress, 'Fixture quote must have a shipping address.');

        return $shippingAddress;
    }
}
