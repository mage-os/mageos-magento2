<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Quote\Test\Fixture\GuestCart as GuestCartFixture;
use Magento\Quote\Test\Fixture\AddProductToCart as AddProductToCartFixture;
use Magento\Checkout\Test\Fixture\SetBillingAddress as SetBillingAddressFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterfaceFactory;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressInterfaceFactory;

/**
 * Test GuestShippingInformationManagement API validation.
 */
class GuestShippingInformationManagementValidationTest extends WebapiAbstract
{
    private const SERVICE_VERSION = 'V1';
    private const SERVICE_NAME = 'checkoutGuestShippingInformationManagementV1';
    private const RESOURCE_PATH = '/V1/guest-carts/%s/shipping-information';

    /**
     * @var ShippingInformationInterfaceFactory
     */
    private $shippingInformationFactory;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shippingInformationFactory = Bootstrap::getObjectManager()
            ->get(ShippingInformationInterfaceFactory::class);
        $this->addressFactory = Bootstrap::getObjectManager()->get(AddressInterfaceFactory::class);
        $this->quoteIdMaskFactory = Bootstrap::getObjectManager()->get(QuoteIdMaskFactory::class);
    }

    #[
        DataFixture(ProductFixture::class, as: 'p1'),
        DataFixture(GuestCartFixture::class, as: 'cart'),
        DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$p1.id$']),
        DataFixture(SetBillingAddressFixture::class, ['cart_id' => '$cart.id$']),
    ]
    /**
     * Test successful validation with valid address data
     */
    public function testSaveAddressInformationWithValidData()
    {
        $cartId = $this->getMaskedCartId('test01');
        $shippingAddress = $this->addressFactory->create();
        $shippingAddress->setData([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'country_id' => 'US',
            'region_id' => 12,
            'region' => 'California',
            'region_code' => 'CA',
            'street' => ['123 Test Street'],
            'city' => 'Test City',
            'postcode' => '90210',
            'telephone' => '1234567890',
        ]);
        $billingAddress = $this->addressFactory->create();
        $billingAddress->setData([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'country_id' => 'US',
            'region_id' => 12,
            'region' => 'California',
            'region_code' => 'CA',
            'street' => ['123 Test Street'],
            'city' => 'Test City',
            'postcode' => '90210',
            'telephone' => '1234567890',
        ]);
        $shippingInformation = $this->shippingInformationFactory->create();
        $shippingInformation->setShippingAddress($shippingAddress);
        $shippingInformation->setBillingAddress($billingAddress);
        $shippingInformation->setShippingMethodCode('flatrate');
        $shippingInformation->setShippingCarrierCode('flatrate');
        $result = $this->callSaveAddressInformation($cartId, $shippingInformation);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('payment_methods', $result);
        $this->assertArrayHasKey('totals', $result);
    }

    /**
     * Get masked cart ID for the given quote
     *
     * @param string $reservedOrderId
     * @return string
     */
    private function getMaskedCartId(string $reservedOrderId): string
    {
        $quote = Bootstrap::getObjectManager()->create(Quote::class);
        $quote->load($reservedOrderId, 'reserved_order_id');
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $quoteIdMask->load($quote->getId(), 'quote_id');
        return $quoteIdMask->getMaskedId();
    }

    /**
     * Call the saveAddressInformation API
     *
     * @param string $cartId
     * @param ShippingInformationInterface $shippingInformation
     * @return array
     */
    private function callSaveAddressInformation(
        string $cartId,
        ShippingInformationInterface $shippingInformation
    ): array {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => sprintf(self::RESOURCE_PATH, $cartId),
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'saveAddressInformation',
                'serviceVersion' => self::SERVICE_VERSION,
            ],
        ];
        $requestData = [
            'cart_id' => $cartId,
            'addressInformation' => [
                'shipping_address' => $this->addressToArray($shippingInformation->getShippingAddress()),
                'billing_address' => $this->addressToArray($shippingInformation->getBillingAddress()),
                'shipping_method_code' => $shippingInformation->getShippingMethodCode(),
                'shipping_carrier_code' => $shippingInformation->getShippingCarrierCode(),
            ],
        ];

        return $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * Convert address object to array for API call
     *
     * @param AddressInterface $address
     * @return array
     */
    private function addressToArray(AddressInterface $address): array
    {
        return [
            'firstname' => $address->getFirstname(),
            'lastname' => $address->getLastname(),
            'email' => $address->getEmail(),
            'country_id' => $address->getCountryId(),
            'region_id' => $address->getRegionId(),
            'region' => $address->getRegion(),
            'region_code' => $address->getRegionCode(),
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
            'postcode' => $address->getPostcode(),
            'telephone' => $address->getTelephone(),
        ];
    }
}
