<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Indexer\Test\Fixture\Indexer;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Test\Fixture\AddProductToCart as AddProductToCartFixture;
use Magento\Quote\Test\Fixture\CustomerCart;
use Magento\Quote\Test\Fixture\QuoteIdMask;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\CacheCleaner;
use Magento\TestFramework\TestCase\GraphQl\ResponseContainsErrorsException;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class PlaceOrderTest extends GraphQlAbstract
{
    /**
     * @var CustomerTokenServiceInterface
     */
    private CustomerTokenServiceInterface $customerTokenService;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->customerTokenService = $objectManager->get(CustomerTokenServiceInterface::class);

        parent::setUp();
    }

    /**
     * Test translated error message in non default store
     *
     * @magentoApiDataFixture Magento/Store/_files/second_store.php
     * @magentoApiDataFixture Magento/Translation/_files/place_order_message_translate.php
     * @magentoConfigFixture fixture_second_store_store general/locale/code nl_NL
     */
    #[
        DataFixture(ProductFixture::class, as: 'product'),
        DataFixture(Indexer::class, as: 'indexer'),
        DataFixture(Customer::class, ['email' => 'customer@example.com'], as: 'customer'),
        DataFixture(
            CustomerCart::class,
            [
                'customer_id' => '$customer.id$',
                'reserved_order_id' => 'test_quote'
            ],
            'cart'
        ),
        DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$product.id$']),
        DataFixture(QuoteIdMask::class, ['cart_id' => '$cart.id$'], 'quoteIdMask'),
    ]
    public function testPlaceOrderErrorTranslation()
    {
        CacheCleaner::clean(['translate', 'config']);
        $storeCode = "fixture_second_store";
        $maskedQuoteId = DataFixtureStorageManager::getStorage()->get('quoteIdMask')->getMaskedId();
        $query = $this->placeOrderQuery($maskedQuoteId);
        try {
            $headers = ['Store' => $storeCode];
            $this->graphQlMutation($query, [], '', array_merge($headers, $this->getHeaderMap()));
        } catch (ResponseContainsErrorsException $exception) {
            $exceptionData = $exception->getResponseData();
            self::assertEquals(1, count($exceptionData['errors']));
            self::assertEquals(
                'Kan geen bestelling plaatsen: Sommige adressen kunnen niet worden gebruikt' .
                ' vanwege de configuraties van specifieke landen.',
                $exceptionData['errors'][0]['message']
            );
        }
    }

    /**
     * Get place order mutation
     *
     * @param string $maskedQuoteId
     * @return string
     */
    private function placeOrderQuery(string $maskedQuoteId): string
    {
        return <<<QUERY
mutation {
  placeOrder(input: {cart_id: "{$maskedQuoteId}"}) {
    order {
      order_number
    }
  }
}
QUERY;
    }

    /**
     * Get bearer authorization header
     *
     * @param string $username
     * @param string $password
     * @return array
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    private function getHeaderMap(string $username = 'customer@example.com', string $password = 'password'): array
    {
        $customerToken = $this->customerTokenService->createCustomerAccessToken($username, $password);
        $headerMap = ['Authorization' => 'Bearer ' . $customerToken];
        return $headerMap;
    }
}
