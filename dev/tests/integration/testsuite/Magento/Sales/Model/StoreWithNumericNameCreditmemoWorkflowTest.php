<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Store\Test\Fixture\Website as WebsiteFixture;
use Magento\Store\Test\Fixture\Group as StoreGroupFixture;
use Magento\Store\Test\Fixture\Store as StoreFixture;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Customer\Test\Fixture\Customer as CustomerFixture;
use Magento\Checkout\Test\Fixture\SetBillingAddress as SetBillingAddressFixture;
use Magento\Checkout\Test\Fixture\SetShippingAddress as SetShippingAddressFixture;
use Magento\Checkout\Test\Fixture\SetDeliveryMethod as SetDeliveryMethodFixture;
use Magento\Checkout\Test\Fixture\SetPaymentMethod as SetPaymentMethodFixture;
use Magento\Checkout\Test\Fixture\PlaceOrder as PlaceOrderFixture;
use Magento\Sales\Test\Fixture\Invoice as InvoiceFixture;
use Magento\Sales\Test\Fixture\Creditmemo as CreditmemoFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;

/**
 * Integration test for complete workflow:
 * Create website, store, and store view with numeric names -> Place orders -> Create credit memo -> Verify grid display
 *
 * @magentoDbIsolation disabled
 * @magentoAppIsolation enabled
 * @magentoConfigFixture default/general/country/allow US
 * @magentoConfigFixture default/general/country/default US
 * @magentoConfigFixture default_store carriers/flatrate/active 1
 * @magentoConfigFixture default_store carriers/flatrate/price 5.00
 * @magentoConfigFixture default_store payment/free/active 1
 */
class StoreWithNumericNameCreditmemoWorkflowTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->creditmemoRepository = $this->objectManager->get(CreditmemoRepositoryInterface::class);
    }

    /**
     * Test complete workflow: Create store with numeric name -> Place order -> Create credit memo -> Verify grid
     *
     * @return void
     */
    #[
        DataFixture(WebsiteFixture::class, ['code' => 'test_website', 'name' => '123test Website'], 'test_website'),
        DataFixture(
            StoreGroupFixture::class,
            ['code' => 'test_group', 'name' => '123test Store Group', 'website_id' => '$test_website.id$'],
            'test_group'
        ),
        DataFixture(
            StoreFixture::class,
            [
                'code' => 'test_store',
                'name' => '123test Store View',
                'website_id' => '$test_website.id$',
                'group_id' => '$test_group.id$'
            ],
            'test_store'
        ),
        DataFixture(
            ProductFixture::class,
            ['sku' => 'simple', 'price' => 10, 'website_ids' => [1, '$test_website.id$']],
            'product'
        ),
        DataFixture(
            CustomerFixture::class,
            ['email' => 'customer@123test.com', 'website_id' => '$test_website.id$'],
            'customer'
        ),
    ]
    public function testCompleteWorkflowWithNumericStoreNames(): void
    {
        // Step 1: Get basic fixtures
        $fixtures = DataFixtureStorageManager::getStorage();
        /** @var StoreInterface $store */
        $store = $fixtures->get('test_store');
        $customer = $fixtures->get('customer');
        $product = $fixtures->get('product');

        // Step 2: Create cart manually with correct store ID (CustomerCartFixture doesn't support store_id)
        $cartManagement = $this->objectManager->get('Magento\Quote\Api\CartManagementInterface');
        $cartRepository = $this->objectManager->get('Magento\Quote\Api\CartRepositoryInterface');

        $cartId = $cartManagement->createEmptyCartForCustomer($customer->getId());
        $cart = $cartRepository->get($cartId);
        $cart->setStoreId($store->getId());
        $cartRepository->save($cart);

        // Add product to cart
        $cart->addProduct($product, 2);
        $cartRepository->save($cart);

        // Step 3: Use fixtures for checkout process
        $billingAddressFixture = $this->objectManager->create(SetBillingAddressFixture::class);
        $billingAddressFixture->apply(['cart_id' => $cart->getId()]);

        $shippingAddressFixture = $this->objectManager->create(SetShippingAddressFixture::class);
        $shippingAddressFixture->apply(['cart_id' => $cart->getId()]);

        $deliveryMethodFixture = $this->objectManager->create(SetDeliveryMethodFixture::class);
        $deliveryMethodFixture->apply(
            ['cart_id' => $cart->getId(), 'carrier_code' => 'flatrate', 'method_code' => 'flatrate']
        );

        $paymentMethodFixture = $this->objectManager->create(SetPaymentMethodFixture::class);
        $paymentMethodFixture->apply(['cart_id' => $cart->getId()]);

        $placeOrderFixture = $this->objectManager->create(PlaceOrderFixture::class);
        $order = $placeOrderFixture->apply(['cart_id' => $cart->getId()]);

        $invoiceFixture = $this->objectManager->create(InvoiceFixture::class);
        $invoiceFixture->apply(['order_id' => $order->getId()]);

        $creditmemoFixture = $this->objectManager->create(CreditmemoFixture::class);
        $creditmemo = $creditmemoFixture->apply([
            'order_id' => $order->getId(),
            'items' => [['qty' => 1, 'product_id' => $product->getId()]]
        ]);

        $this->assertEquals($store->getId(), $creditmemo->getStoreId(), 'Credit memo should be in test store');

        // Step 4: Verify credit memo displays in grid page
        $this->verifyCreditMemoGridDisplaysRecords($creditmemo, $order, $store);
    }

    /**
     * Verify credit memo grid displays records correctly
     *
     * @param CreditmemoInterface $creditmemo
     * @param OrderInterface $order
     * @param StoreInterface $store
     * @return void
     */
    private function verifyCreditMemoGridDisplaysRecords(
        CreditmemoInterface $creditmemo,
        OrderInterface $order,
        StoreInterface $store
    ): void {
        // Test credit memo retrieval by order ID
        $creditmemoByOrder = $this->getCreditmemosByFilter('order_id', $order->getId());
        $this->assertCount(1, $creditmemoByOrder);
        $foundCreditmemo = reset($creditmemoByOrder);

        //Assert that found credit memo matches expected values
        $this->assertEquals($creditmemo->getId(), $foundCreditmemo->getId());
        $this->assertEquals($creditmemo->getIncrementId(), $foundCreditmemo->getIncrementId());
        $this->assertEquals($order->getId(), $foundCreditmemo->getOrderId());
        $this->assertEquals($order->getStoreId(), $foundCreditmemo->getStoreId());

        // Test credit memo retrieval by creditmemo ID (more efficient than filtering by store_id and looping)
        $creditmemoById = $this->getCreditmemosByFilter('entity_id', $creditmemo->getId());
        $this->assertCount(1, $creditmemoById, 'Credit memo should be found when filtering by ID');

        $foundCreditmemoById = reset($creditmemoById);
        $this->assertEquals($creditmemo->getId(), $foundCreditmemoById->getId());
        $this->assertEquals($order->getStoreId(), $foundCreditmemoById->getStoreId());

        // Explicitly verify the credit memo is created in "123test Store View"
        $this->assertEquals(
            $store->getId(),
            $creditmemo->getStoreId(),
            'Credit memo should be created in the test store'
        );
        $this->assertEquals(
            '123test Store View',
            $store->getName(),
            'Test store should have numeric name'
        );

        // Verify credit memo is visible when filtering by test store
        $creditmemosByTestStore = $this->getCreditmemosByFilter('store_id', $store->getId());
        $this->assertGreaterThan(
            0,
            count($creditmemosByTestStore),
            'Credit memo should be visible when filtering by test store'
        );

        // Verify our specific credit memo is in the store-filtered results
        $foundInStoreFilter = false;
        foreach ($creditmemosByTestStore as $cm) {
            if ($cm->getId() === $creditmemo->getId()) {
                $foundInStoreFilter = true;
                break;
            }
        }
        $this->assertTrue(
            $foundInStoreFilter,
            'Credit memo should be found when filtering grid by "123test Store View"'
        );
    }

    /**
     * Get credit memos by filter field and value
     *
     * @param string $field
     * @param mixed $value
     * @return array
     */
    private function getCreditmemosByFilter(string $field, mixed $value): array
    {
        $searchCriteria = $this->objectManager->get('Magento\Framework\Api\SearchCriteriaBuilder')
            ->addFilter($field, $value)
            ->create();

        return $this->creditmemoRepository->getList($searchCriteria)->getItems();
    }
}
