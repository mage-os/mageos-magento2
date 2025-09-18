<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Checkout\Model;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Framework\App\Backpressure\ContextInterface;
use Magento\Framework\App\Backpressure\IdentityProviderInterface;
use Magento\Framework\App\Backpressure\SlidingWindow\LimitConfigManagerInterface;
use Magento\Framework\Webapi\Backpressure\BackpressureContextFactory;
use Magento\Quote\Model\Backpressure\OrderLimitConfigManager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BackpressureTest extends TestCase
{
    /**
     * @var BackpressureContextFactory
     */
    private $webapiContextFactory;

    /**
     * @var LimitConfigManagerInterface
     */
    private $limitConfigManager;

    /**
     * @var IdentityProviderInterface|MockObject
     */
    private $identityProvider;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->identityProvider = $this->createMock(IdentityProviderInterface::class);
        $this->webapiContextFactory = Bootstrap::getObjectManager()->create(
            BackpressureContextFactory::class,
            ['identityProvider' => $this->identityProvider]
        );
        $this->limitConfigManager = Bootstrap::getObjectManager()->get(LimitConfigManagerInterface::class);
    }

    /**
     * Configured cases.
     *
     * @return array
     */
    public static function getConfiguredCases(): array
    {
        return [
            'guest' => [
                ContextInterface::IDENTITY_TYPE_IP,
                '127.0.0.1',
                GuestPaymentInformationManagementInterface::class,
                'savePaymentInformationAndPlaceOrder',
                '/V1/guest-carts/:cartId/payment-information',
                50
            ],
            'customer' => [
                ContextInterface::IDENTITY_TYPE_CUSTOMER,
                '42',
                PaymentInformationManagementInterface::class,
                'savePaymentInformationAndPlaceOrder',
                '/V1/carts/mine/payment-information',
                100
            ]
        ];
    }

    /**
     * Verify that backpressure is configured for guests.
     *
     * @param int $identityType
     * @param string $identity
     * @param string $service
     * @param string $method
     * @param string $endpoint
     * @param int $expectedLimit
     * @return void
     * @dataProvider getConfiguredCases
     * @magentoConfigFixture current_store sales/backpressure/enabled 1
     * @magentoConfigFixture current_store sales/backpressure/limit 100
     * @magentoConfigFixture current_store sales/backpressure/guest_limit 50
     * @magentoConfigFixture current_store sales/backpressure/period 60
     */
    public function testConfigured(
        int $identityType,
        string $identity,
        string $service,
        string $method,
        string $endpoint,
        int $expectedLimit
    ): void {
        $this->identityProvider->method('fetchIdentityType')->willReturn($identityType);
        $this->identityProvider->method('fetchIdentity')->willReturn($identity);

        $context = $this->webapiContextFactory->create(
            $service,
            $method,
            $endpoint
        );
        $this->assertEquals(OrderLimitConfigManager::REQUEST_TYPE_ID, $context->getTypeId());

        $limits = $this->limitConfigManager->readLimit($context);
        $this->assertEquals($expectedLimit, $limits->getLimit());
        $this->assertEquals(60, $limits->getPeriod());
    }
}
