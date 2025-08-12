<?php

/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Sales;

use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Checkout\Test\Fixture\PlaceOrder as PlaceOrderFixture;
use Magento\Checkout\Test\Fixture\SetBillingAddress;
use Magento\Checkout\Test\Fixture\SetDeliveryMethod as SetDeliveryMethodFixture;
use Magento\Checkout\Test\Fixture\SetPaymentMethod as SetPaymentMethodFixture;
use Magento\Checkout\Test\Fixture\SetShippingAddress;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Test\Fixture\AddProductToCart;
use Magento\Quote\Test\Fixture\CustomerCart;
use Magento\Store\Test\Fixture\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;

class OrderDateFormattingTest extends GraphQlAbstract
{
    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    /**
     * @var LocaleResolver
     */
    private $localeResolver;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->customerTokenService = $objectManager->get(CustomerTokenServiceInterface::class);
        $this->localeResolver = $objectManager->get(LocaleResolver::class);
    }

    /**
     * Test order_date with null/empty created_at
     */
    #[
        DataFixture(Store::class),
        DataFixture(ProductFixture::class, as: 'product1'),
        DataFixture(Customer::class, as: 'customer'),
        DataFixture(CustomerCart::class, ['customer_id' => '$customer.id$'], as: 'quote'),
        DataFixture(AddProductToCart::class, ['cart_id' => '$quote.id$', 'product_id' => '$product1.id$', 'qty' => 2]),
        DataFixture(SetBillingAddress::class, ['cart_id' => '$quote.id$']),
        DataFixture(SetShippingAddress::class, ['cart_id' => '$quote.id$']),
        DataFixture(SetDeliveryMethodFixture::class, ['cart_id' => '$quote.id$']),
        DataFixture(SetPaymentMethodFixture::class, ['cart_id' => '$quote.id$']),
        DataFixture(PlaceOrderFixture::class, ['cart_id' => '$quote.id$'], 'order'),
    ]
    public function testOrderDateWithInvalidCreatedAt()
    {
        $customer = DataFixtureStorageManager::getStorage()->get('customer');
        $customerEmail = $customer->getEmail();
        $password = 'password';
        $customerToken = $this->customerTokenService
            ->createCustomerAccessToken($customerEmail, $password);

        $query = <<<QUERY
{
    customer {
        orders {
            items {
                order_date
                created_at
            }
        }
    }
}
QUERY;

        $response = $this->graphQlQuery($query, [], '', ['Authorization' => 'Bearer ' . $customerToken]);

        $this->assertArrayHasKey('customer', $response);
        $this->assertArrayHasKey('orders', $response['customer']);
        $expectedFormat = 'd/m/Y H:i:s';
        $dateString = $response['customer']['orders']['items'][0]['order_date'];
        $date = \DateTime::createFromFormat($expectedFormat, $dateString);
        $isValid = $date && $date->format($expectedFormat) === $dateString;
        $this->assertTrue($isValid, "Date format is not valid: $dateString");

    }

    /**
     * Test order_date formatting performance with multiple locales
     */
    #[
        DataFixture(Store::class),
        DataFixture(ProductFixture::class, as: 'product1'),
        DataFixture(Customer::class, as: 'customer'),
        DataFixture(CustomerCart::class, ['customer_id' => '$customer.id$'], as: 'quote'),
        DataFixture(AddProductToCart::class, ['cart_id' => '$quote.id$', 'product_id' => '$product1.id$', 'qty' => 2]),
        DataFixture(SetBillingAddress::class, ['cart_id' => '$quote.id$']),
        DataFixture(SetShippingAddress::class, ['cart_id' => '$quote.id$']),
        DataFixture(SetDeliveryMethodFixture::class, ['cart_id' => '$quote.id$']),
        DataFixture(SetPaymentMethodFixture::class, ['cart_id' => '$quote.id$']),
        DataFixture(PlaceOrderFixture::class, ['cart_id' => '$quote.id$'], 'order'),
    ]
    public function testOrderDateFormattingPerformance()
    {
        $locales = ['en_US', 'fr_FR', 'de_DE', 'es_ES', 'it_IT'];

        $customer = DataFixtureStorageManager::getStorage()->get('customer');
        $customerEmail = $customer->getEmail();
        $password = 'password';
        $customerToken = $this->customerTokenService->createCustomerAccessToken($customerEmail, $password);

        $query = <<<QUERY
{
    customer {
        orders {
            items {
                order_date
            }
        }
    }
}
QUERY;

        foreach ($locales as $locale) {
            $this->localeResolver->setLocale($locale);
            $response = $this->graphQlQuery($query, [], '', ['Authorization' => 'Bearer ' . $customerToken]);
            $this->assertArrayHasKey('customer', $response);
            $this->assertArrayHasKey('orders', $response['customer']);
            $expectedFormat = 'd/m/Y H:i:s';
            $dateString = $response['customer']['orders']['items'][0]['order_date'];
            $date = \DateTime::createFromFormat($expectedFormat, $dateString);
            $isValid = $date && $date->format($expectedFormat) === $dateString;
            $this->assertTrue($isValid, "Date format is not valid: $dateString");
        }
    }
}
