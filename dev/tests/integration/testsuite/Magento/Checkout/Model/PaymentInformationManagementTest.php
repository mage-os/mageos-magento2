<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Model;

use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Quote\Model\GetQuoteByReservedOrderId;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for billing address ownership validation
 * during payment information save.
 *
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class PaymentInformationManagementTest extends TestCase
{
    /**
     * @var PaymentInformationManagementInterface
     */
    private $paymentManagement;

    /**
     * @var GetQuoteByReservedOrderId
     */
    private $getQuoteByReservedOrderId;

    /**
     * @var PaymentInterface
     */
    private $payment;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->paymentManagement = $objectManager->get(
            PaymentInformationManagementInterface::class
        );
        $this->getQuoteByReservedOrderId = $objectManager->get(
            GetQuoteByReservedOrderId::class
        );
        $this->payment = $objectManager->get(PaymentInterface::class);
        $this->addressFactory = $objectManager->get(
            AddressInterfaceFactory::class
        );
        $this->customerRepository = $objectManager->get(
            CustomerRepositoryInterface::class
        );
        $this->addressRepository = $objectManager->get(
            AddressRepositoryInterface::class
        );
    }

    /**
     * Verify that order placement is rejected when billing
     * address has a customerAddressId that does not belong
     * to the quote's customer.
     *
     * @magentoDataFixture Magento/Sales/_files/quote_with_customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_with_addresses.php
     * @magentoDbIsolation disabled
     */
    public function testRejectsInvalidCustomerAddressId(): void
    {
        $quote = $this->getQuoteByReservedOrderId->execute('test01');
        $quote->getShippingAddress()
            ->setShippingMethod('flatrate_flatrate')
            ->setCollectShippingRates(true);
        $this->payment->setMethod('checkmo');
        $quote->save();

        // Get an address that belongs to a different customer
        $otherCustomer = $this->customerRepository->get(
            'customer_with_addresses@test.com'
        );
        $otherCustomerAddresses = $otherCustomer->getAddresses();
        $otherAddress = reset($otherCustomerAddresses);

        // Build billing address with the other customer's
        // address ID
        $billingAddress = $this->addressFactory->create();
        $billingAddress->setFirstname('John');
        $billingAddress->setLastname('Smith');
        $billingAddress->setCity('CityM');
        $billingAddress->setCountryId('US');
        $billingAddress->setPostcode('75477');
        $billingAddress->setTelephone('3468676');
        $billingAddress->setStreet(['Green str, 67']);
        $billingAddress->setRegionId(1);
        $billingAddress->setCustomerAddressId(
            $otherAddress->getId()
        );

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage(
            'Invalid customer address id'
        );

        $this->paymentManagement
            ->savePaymentInformationAndPlaceOrder(
                $quote->getId(),
                $this->payment,
                $billingAddress
            );
    }
}
