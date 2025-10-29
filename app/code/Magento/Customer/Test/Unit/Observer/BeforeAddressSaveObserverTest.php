<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Observer;

use Magento\Customer\Helper\Address as HelperAddress;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Customer;
use Magento\Customer\Observer\BeforeAddressSaveObserver;
use Magento\Customer\Test\Unit\Helper\AddressTestHelper;
use Magento\Customer\Test\Unit\Helper\ObserverTestHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BeforeAddressSaveObserverTest extends TestCase
{
    /**
     * @var BeforeAddressSaveObserver
     */
    protected $model;

    /**
     * @var MockObject&Registry
     */
    protected $registry;

    /**
     * @var Customer|MockObject
     */
    protected $customerMock;

    /**
     * @var MockObject&HelperAddress
     */
    protected $helperAddress;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(Registry::class);

        $this->helperAddress = $this->createMock(\Magento\Customer\Helper\Address::class);

        $this->model = new BeforeAddressSaveObserver(
            $this->helperAddress,
            $this->registry
        );
    }

    public function testBeforeAddressSaveWithCustomerAddressId(): void
    {
        $customerAddressId = 1;

        $address = $this->createMock(\Magento\Customer\Model\Address::class);
        $address->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($customerAddressId);

        $observer = new ObserverTestHelper();
        $observer->setCustomerAddress($address);

        $this->registry->expects($this->once())
            ->method('registry')
            ->with(BeforeAddressSaveObserver::VIV_CURRENTLY_SAVED_ADDRESS)
            ->willReturn(true);
        $this->registry->expects($this->once())
            ->method('unregister')
            ->with(BeforeAddressSaveObserver::VIV_CURRENTLY_SAVED_ADDRESS)
            ->willReturnSelf();
        $this->registry->expects($this->once())
            ->method('register')
            ->with(BeforeAddressSaveObserver::VIV_CURRENTLY_SAVED_ADDRESS, $customerAddressId)
            ->willReturnSelf();

        $this->model->execute($observer);
    }

    /**
     * @param string $configAddressType
     * @param bool $isDefaultBilling
     * @param bool $isDefaultShipping
     */
    #[DataProvider('dataProviderBeforeAddressSaveWithoutCustomerAddressId')]
    public function testBeforeAddressSaveWithoutCustomerAddressId(
        string $configAddressType,
        bool $isDefaultBilling,
        bool $isDefaultShipping
    ): void {
        $customerAddressId = null;

        $address = new AddressTestHelper();
        $address->setId($customerAddressId);
        $address->setIsDefaultBilling($isDefaultBilling);
        $address->setIsDefaultShipping($isDefaultShipping);

        $observer = new ObserverTestHelper();
        $observer->setCustomerAddress($address);

        $this->helperAddress->expects($this->once())
            ->method('getTaxCalculationAddressType')
            ->willReturn($configAddressType);

        $this->registry->expects($this->once())
            ->method('registry')
            ->with(BeforeAddressSaveObserver::VIV_CURRENTLY_SAVED_ADDRESS)
            ->willReturn(true);
        $this->registry->expects($this->once())
            ->method('unregister')
            ->with(BeforeAddressSaveObserver::VIV_CURRENTLY_SAVED_ADDRESS)
            ->willReturnSelf();
        $this->registry->expects($this->any())
            ->method('register')
            ->willReturnMap([
                [BeforeAddressSaveObserver::VIV_CURRENTLY_SAVED_ADDRESS, $customerAddressId, false, $this->registry],
                [BeforeAddressSaveObserver::VIV_CURRENTLY_SAVED_ADDRESS, 'new_address', false, $this->registry],
            ]);

        $this->model->execute($observer);
    }

    /**
     * @return array<int, array<string, string|bool>>
     */
    public static function dataProviderBeforeAddressSaveWithoutCustomerAddressId(): array
    {
        return [
            [
                'configAddressType' => AbstractAddress::TYPE_BILLING,
                'isDefaultBilling' => true,
                'isDefaultShipping' => false,
            ],
            [
                'configAddressType' => AbstractAddress::TYPE_SHIPPING,
                'isDefaultBilling' => false,
                'isDefaultShipping' => true,
            ],
        ];
    }
}
