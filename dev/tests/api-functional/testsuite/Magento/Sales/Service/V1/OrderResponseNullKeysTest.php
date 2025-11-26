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
 * @magentoDbIsolation disabled
 * @magentoAppIsolation enabled
 * @magentoAppArea webapi_rest
 */
class OrderResponseNullKeysTest extends WebapiAbstract
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->_markTestAsRestOnly();
    }

    #[
        ConfigFixture('payment/checkmo/active', '1'),
        ConfigFixture('carriers/flatrate/active', '1'),

        DataFixture(ProductFixture::class, [
            'type_id' => 'simple',
            'attribute_set_id' => 4,
            'sku' => 'order-null-keys-simple',
            'name' => 'Order Null Keys Simple',
            'price' => 10.00,
            'status' => 1,
            'visibility' => 4,
            'weight' => 1,
            'website_ids' => [1],
            'quantity_and_stock_status' => ['qty' => 100, 'is_in_stock' => true],
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
        $this->assertSame($orderId, (int)$result['entity_id']);
        $this->assertArrayHasKey('state', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertNull($result['state']);
        $this->assertNull($result['status']);
    }

    private function nullifyOrderStateStatus(int $orderId): void
    {
        $om = Bootstrap::getObjectManager();
        $resource = $om->get(ResourceConnection::class);
        $connection = $resource->getConnection();
        $table = $resource->getTableName('sales_order');

        $connection->update(
            $table,
            ['state' => new Expression('NULL'), 'status' => new Expression('NULL')],
            ['entity_id = ?' => $orderId]
        );
    }
}
