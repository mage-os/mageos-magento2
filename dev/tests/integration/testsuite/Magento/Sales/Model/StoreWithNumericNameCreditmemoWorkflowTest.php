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
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Store\Test\Fixture\Website as WebsiteFixture;
use Magento\Store\Test\Fixture\Group as StoreGroupFixture;
use Magento\Store\Test\Fixture\Store as StoreFixture;
use Magento\Customer\Test\Fixture\Customer as CustomerFixture;
use Magento\Quote\Test\Fixture\CustomerCart as CustomerCartFixture;
use Magento\Checkout\Test\Fixture\SetBillingAddress as SetBillingAddressFixture;
use Magento\Checkout\Test\Fixture\SetShippingAddress as SetShippingAddressFixture;
use Magento\Checkout\Test\Fixture\SetDeliveryMethod as SetDeliveryMethodFixture;
use Magento\Checkout\Test\Fixture\SetPaymentMethod as SetPaymentMethodFixture;
use Magento\Checkout\Test\Fixture\PlaceOrder as PlaceOrderFixture;
use Magento\Sales\Test\Fixture\Invoice as InvoiceFixture;
use Magento\Sales\Test\Fixture\Creditmemo as CreditmemoFixture;
use Magento\Quote\Test\Fixture\AddProductToCart as AddProductToCartFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;

/**
 * Integration test for complete workflow:
 * Create website, store, and store view with numeric names -> Place orders -> Create credit memo -> Verify grid display
 *
 * @magentoDbIsolation enabled
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
     * Test data constants
     */
    private const WEBSITE_NAME = '123test Website';
    private const STORE_GROUP_NAME = '123test Store Group';
    private const STORE_NAME = '123test Store View';

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
        DataFixture(ProductFixture::class, ['sku' => 'simple', 'price' => 10], 'product'),
        DataFixture(CustomerFixture::class, ['email' => 'customer@123test.com'], 'customer'),
        DataFixture(
            CustomerCartFixture::class,
            ['customer_id' => '$customer.id$', 'store_id' => '$test_store.id$'],
            'cart'
        ),
        DataFixture(SetBillingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetShippingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(
            AddProductToCartFixture::class,
            ['cart_id' => '$cart.id$', 'product_id' => '$product.id$', 'qty' => 2]
        ),
        DataFixture(
            SetDeliveryMethodFixture::class,
            ['cart_id' => '$cart.id$']
        ),
        DataFixture(SetPaymentMethodFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(PlaceOrderFixture::class, ['cart_id' => '$cart.id$'], 'order'),
        DataFixture(InvoiceFixture::class, ['order_id' => '$order.id$'], 'invoice'),
        DataFixture(
            CreditmemoFixture::class,
            ['order_id' => '$order.id$', 'items' => [['qty' => 1, 'product_id' => '$product.id$']]],
            'creditmemo'
        )
    ]
    public function testCompleteWorkflowWithNumericStoreNames(): void
    {
        // Step 1: Get fixtures
        $fixtures = DataFixtureStorageManager::getStorage();
        $store = $fixtures->get('test_store');
        $order = $fixtures->get('order');
        $creditmemo = $fixtures->get('creditmemo');

        // Step 2: Verify credit memo grid displays records
        $this->verifyCreditMemoGridDisplaysRecords($creditmemo, $order);

        // Step 3: Verify store name rendering in grid context (UI validation proves DB layer works)
        $this->verifyStoreNameRenderingInGrid($creditmemo, $store);
    }

    /**
     * Verify credit memo grid displays records correctly
     *
     * @param CreditmemoInterface $creditmemo
     * @param OrderInterface $order
     * @return void
     */
    private function verifyCreditMemoGridDisplaysRecords($creditmemo, $order): void
    {
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
    }

    /**
     * Get credit memos by filter field and value
     *
     * @param string $field
     * @param mixed $value
     * @return array
     */
    private function getCreditmemosByFilter(string $field, $value): array
    {
        $searchCriteria = $this->objectManager->get('Magento\Framework\Api\SearchCriteriaBuilder')
            ->addFilter($field, $value)
            ->create();

        return $this->creditmemoRepository->getList($searchCriteria)->getItems();
    }

    /**
     * Verify store name rendering for credit memo
     * Tests how store names with numeric prefixes are displayed
     *
     * @param CreditmemoInterface $creditmemo
     * @param StoreInterface $store
     * @return void
     */
    private function verifyStoreNameRenderingInGrid($creditmemo, $store): void
    {
        // Test store name rendering using the store renderer that would be used in grids
        $storeRenderer = $this->objectManager->create('Magento\Backend\Block\Widget\Grid\Column\Renderer\Store');

        // Create a mock grid column for the renderer
        $mockColumn = $this->objectManager->create('Magento\Framework\DataObject');
        $mockColumn->setData([
            'index' => 'store_id',
            'type' => 'store',
            'skipEmptyStoresLabel' => false,
            'skipAllStoresLabel' => false
        ]);

        $storeRenderer->setColumn($mockColumn);

        // Create a mock row data object representing a grid row
        $mockRow = $this->objectManager->create('Magento\Framework\DataObject');
        $mockRow->setData([
            'store_id' => $store->getId(),
            'entity_id' => $creditmemo->getId()
        ]);

        // Test rendering of store name
        $renderedOutput = $storeRenderer->render($mockRow);

        // Verify that the store name is properly rendered and includes our numeric store name
        $this->assertIsString($renderedOutput);
        $this->assertNotEmpty($renderedOutput);

        // The rendered output should contain the store hierarchy
        $this->assertStringContainsString(self::WEBSITE_NAME, $renderedOutput); // '123test Website'
        $this->assertStringContainsString(self::STORE_GROUP_NAME, $renderedOutput); // '123test Store Group'
        $this->assertStringContainsString(self::STORE_NAME, $renderedOutput); // '123test Store View'

        // Verify that numeric prefixes are properly handled (not truncated or misinterpreted)
        $this->assertStringContainsString('123test', $renderedOutput);

        // Verify store hierarchy rendering with numeric names
        $lines = explode('<br/>', $renderedOutput);
        $this->assertGreaterThan(0, count($lines));

        // Basic structure validation
        $flattenedOutput = strip_tags($renderedOutput);
        $this->assertNotEmpty(trim($flattenedOutput));

        // Test alternative rendering scenario - what happens with just store ID array
        $mockRowWithArray = $this->objectManager->create('Magento\Framework\DataObject');
        $mockRowWithArray->setData([
            'store_id' => [$store->getId()], // Array format
            'entity_id' => $creditmemo->getId()
        ]);

        $arrayRenderedOutput = $storeRenderer->render($mockRowWithArray);
        $this->assertStringContainsString('123test', $arrayRenderedOutput);

        // Verify grid display integrity - no XSS or formatting issues with numeric store names
        $this->assertStringNotContainsString('<script', strtolower($renderedOutput));
        $cleanOutput = strip_tags($renderedOutput);
        $this->assertStringNotContainsString('<', $cleanOutput);
    }
}
