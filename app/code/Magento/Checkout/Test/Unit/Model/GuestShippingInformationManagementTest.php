<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Model;

use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Checkout\Model\GuestShippingInformationManagement;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GuestShippingInformationManagementTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $shippingInformationManagementMock;

    /**
     * @var MockObject
     */
    protected $quoteIdMaskFactoryMock;

    /**
     * @var GuestShippingInformationManagement
     */
    protected $model;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->quoteIdMaskFactoryMock = $this->createMock(QuoteIdMaskFactory::class);
        $this->shippingInformationManagementMock = $this->createMock(
            ShippingInformationManagementInterface::class
        );

        $this->model = $objectManager->getObject(
            GuestShippingInformationManagement::class,
            [
                'quoteIdMaskFactory' => $this->quoteIdMaskFactoryMock,
                'shippingInformationManagement' => $this->shippingInformationManagementMock
            ]
        );
    }

    public function testSaveAddressInformation()
    {
        $cartId = 'masked_id';
        $quoteId = '100';
        $addressInformationMock = $this->createMock(ShippingInformationInterface::class);

        $quoteIdMask = new class($quoteId) extends QuoteIdMask {
            private $qid;
            public function __construct($qid) { $this->qid = $qid; }
            public function load($id, $field = null) { $this->setData('quote_id', $this->qid); return $this; }
        };
        $this->quoteIdMaskFactoryMock->expects($this->once())->method('create')->willReturn($quoteIdMask);

        $paymentInformationMock = $this->createMock(PaymentDetailsInterface::class);
        $this->shippingInformationManagementMock->expects($this->once())
            ->method('saveAddressInformation')
            ->with(
                self::callback(fn($actualQuoteId): bool => (int) $quoteId === $actualQuoteId),
                $addressInformationMock
            )
            ->willReturn($paymentInformationMock);

        $this->model->saveAddressInformation($cartId, $addressInformationMock);
    }
}
