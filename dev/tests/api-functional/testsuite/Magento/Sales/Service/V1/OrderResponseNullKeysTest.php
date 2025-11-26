<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Service\V1;

use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Checkout\Test\Fixture\PlaceOrder as PlaceOrderFixture;
use Magento\Checkout\Test\Fixture\SetBillingAddress as SetBillingAddressFixture;
use Magento\Checkout\Test\Fixture\SetDeliveryMethod as SetDeliveryMethodFixture;
use Magento\Checkout\Test\Fixture\SetGuestEmail as SetGuestEmailFixture;
use Magento\Checkout\Test\Fixture\SetPaymentMethod as SetPaymentMethodFixture;
use Magento\Checkout\Test\Fixture\SetShippingAddress as SetShippingAddressFixture;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Sql\Expression;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Test\Fixture\AddProductToCart as AddProductToCartFixture;
use Magento\Quote\Test\Fixture\GuestCart as GuestCartFixture;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Verifies REST includes "state" and "status" keys as null when DB has NULLs
 * for responses affected by Magento\Sales\Plugin\Webapi\OrderResponseNullKeysPlugin.
 *
 * @magentoDbIsolation disabled
 * @magentoAppIsolation enabled
 * @magentoAppArea webapi_rest
 */
class OrderResponseNullKeysTest extends WebapiAbstract
{
    #[
        ConfigFixture('payment/checkmo/active', '1'),
        ConfigFixture('carriers/flatrate/active', '1'),

        DataFixture(ProductFixture::class, [
            'price' => 10.00,
            'quantity_and_stock_status' => ['qty' => 100, 'is_in_stock' => true]
        ], as: 'product'),

        DataFixture(GuestCartFixture::class, as: 'cart'),
        DataFixture(
            SetGuestEmailFixture::class,
            ['cart_id' => '$cart.id$', 'email' => 'guest@example.com']
        ),
        DataFixture(
            AddProductToCartFixture::class,
            ['cart_id' => '$cart.id$', 'product_id' => '$product.id$', 'qty' => 1]
        ),
        DataFixture(SetBillingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetShippingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(
            SetDeliveryMethodFixture::class,
            ['cart_id' => '$cart.id$', 'carrier_code' => 'flatrate', 'method_code' => 'flatrate']
        ),
        DataFixture(SetPaymentMethodFixture::class, ['cart_id' => '$cart.id$', 'method' => 'checkmo']),
        DataFixture(PlaceOrderFixture::class, ['cart_id' => '$cart.id$'], as: 'order')
    ]
    public function testGetOrderShowsNullKeys(): void
    {
        $order = DataFixtureStorageManager::getStorage()->get('order');
        $orderId = (int)$order->getEntityId();

        $this->nullifyOrderStateStatus($orderId);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/orders/' . $orderId,
                'httpMethod'   => Request::HTTP_METHOD_GET,
            ],
        ];
        $result = $this->_webApiCall($serviceInfo);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('entity_id', $result);
        $this->assertSame($orderId, (int)$result['entity_id']);
        $this->assertArrayHasKey('state', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertNull($result['state']);
        $this->assertNull($result['status']);
    }

    #[
        ConfigFixture('payment/checkmo/active', '1'),
        ConfigFixture('carriers/flatrate/active', '1'),

        DataFixture(ProductFixture::class, [
            'price' => 10.00,
            'quantity_and_stock_status' => ['qty' => 100, 'is_in_stock' => true]
        ], as: 'product'),

        DataFixture(GuestCartFixture::class, as: 'cart'),
        DataFixture(
            SetGuestEmailFixture::class,
            ['cart_id' => '$cart.id$', 'email' => 'guest@example.com']
        ),
        DataFixture(
            AddProductToCartFixture::class,
            ['cart_id' => '$cart.id$', 'product_id' => '$product.id$', 'qty' => 1]
        ),
        DataFixture(SetBillingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetShippingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(
            SetDeliveryMethodFixture::class,
            ['cart_id' => '$cart.id$', 'carrier_code' => 'flatrate', 'method_code' => 'flatrate']
        ),
        DataFixture(SetPaymentMethodFixture::class, ['cart_id' => '$cart.id$', 'method' => 'checkmo']),
        DataFixture(PlaceOrderFixture::class, ['cart_id' => '$cart.id$'], as: 'order')
    ]
    public function testGetListShowsNullKeysPerItem(): void
    {
        $order = DataFixtureStorageManager::getStorage()->get('order');
        $orderId = (int)$order->getEntityId();

        $this->nullifyOrderStateStatus($orderId);

        $query = sprintf(
            '/V1/orders?searchCriteria[filter_groups][0][filters][0][field]=entity_id'
            . '&searchCriteria[filter_groups][0][filters][0][value]=%d'
            . '&searchCriteria[filter_groups][0][filters][0][condition_type]=eq',
            $orderId
        );
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $query,
                'httpMethod'   => Request::HTTP_METHOD_GET,
            ],
        ];
        $result = $this->_webApiCall($serviceInfo);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertNotEmpty($result['items']);
        $item = $result['items'][0];

        $this->assertSame($orderId, (int)$item['entity_id']);
        $this->assertArrayHasKey('state', $item);
        $this->assertArrayHasKey('status', $item);
        $this->assertNull($item['state']);
        $this->assertNull($item['status']);
    }

    private function nullifyOrderStateStatus(int $orderId): void
    {
        $om = Bootstrap::getObjectManager();
        /** @var ResourceConnection $resource */
        $resource = $om->get(ResourceConnection::class);
        $connection = $resource->getConnection();
        $table = $resource->getTableName('sales_order');

        $connection->update(
            $table,
            [
                'state'  => new Expression('NULL'),
                'status' => new Expression('NULL'),
            ],
            ['entity_id = ?' => $orderId]
        );
    }
}
