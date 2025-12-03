<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model\Order\Creditmemo\Validation;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Creditmemo\Validation\QuantityValidator;
use Magento\Sales\Model\Order\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QuantityValidatorTest extends TestCase
{

    /**
     * @var OrderRepositoryInterface|MockObject
     */
    private $orderRepositoryMock;

    /**
     * @var InvoiceRepositoryInterface|MockObject
     */
    private $invoiceRepositoryMock;

    /**
     * @var QuantityValidator
     */
    private $validator;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrencyMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->invoiceRepositoryMock = $this->createMock(InvoiceRepositoryInterface::class);
        $this->priceCurrencyMock = $this->createMock(PriceCurrencyInterface::class);

        $this->validator = new QuantityValidator(
            $this->orderRepositoryMock,
            $this->invoiceRepositoryMock,
            $this->priceCurrencyMock
        );
    }

    public function testValidateWithoutItems()
    {
        $creditmemoMock = $this->createPartialMock(
            Creditmemo::class,
            ['getOrderId', 'getItems', 'isValidGrandTotal']
        );
        $creditmemoMock->expects($this->exactly(2))->method('getOrderId')
            ->willReturn(1);
        $creditmemoMock->expects($this->once())->method('getItems')
            ->willReturn([]);
        $orderMock = $this->createMock(OrderInterface::class);
        $orderMock->expects($this->once())->method('getItems')
            ->willReturn([]);

        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($orderMock);
        $creditmemoMock->expects($this->once())->method('isValidGrandTotal')
            ->willReturn(false);
        $this->assertEquals(
            [
                __('The credit memo\'s total must be positive.')
            ],
            $this->validator->validate($creditmemoMock)
        );
    }

    public function testValidateWithoutOrder()
    {
        $creditmemoMock = $this->createPartialMock(Creditmemo::class, ['getOrderId', 'getItems']);
        $creditmemoMock->expects($this->once())->method('getOrderId')
            ->willReturn(null);
        $creditmemoMock->expects($this->never())->method('getItems');
        $this->assertEquals(
            [__('Order Id is required for creditmemo document')],
            $this->validator->validate($creditmemoMock)
        );
    }

    public function testValidateWithWrongItemId()
    {
        $orderId = 1;
        $orderItemId = 1;
        $creditmemoMock = $this->createPartialMock(
            Creditmemo::class,
            ['getOrderId', 'getItems', 'isValidGrandTotal']
        );
        $creditmemoMock->expects($this->once())->method('isValidGrandTotal')
            ->willReturn(true);
        $creditmemoMock->expects($this->exactly(2))->method('getOrderId')
            ->willReturn($orderId);
        $creditmemoItemMock = $this->createMock(CreditmemoItemInterface::class);
        $creditmemoItemMock->expects($this->once())->method('getOrderItemId')
            ->willReturn($orderItemId);
        $creditmemoItemSku = 'sku';
        $creditmemoItemMock->expects($this->once())->method('getSku')
            ->willReturn($creditmemoItemSku);
        $creditmemoMock->expects($this->exactly(1))->method('getItems')
            ->willReturn([$creditmemoItemMock]);

        $orderMock = $this->createMock(OrderInterface::class);
        $orderMock->expects($this->once())->method('getItems')
            ->willReturn([]);

        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willReturn($orderMock);

        $this->assertEquals(
            [
                __(
                    'The creditmemo contains product SKU "%1" that is not part of the original order.',
                    $creditmemoItemSku
                ),
            ],
            $this->validator->validate($creditmemoMock)
        );
    }

    private function getCreditMemoMockParams()
    {
        return [
            $this->createMock(\Magento\Framework\Model\Context::class),
            $this->createMock(\Magento\Framework\Registry::class),
            $this->createMock(\Magento\Framework\Api\ExtensionAttributesFactory::class),
            $this->createMock(\Magento\Framework\Api\AttributeValueFactory::class),
            $this->createMock(\Magento\Sales\Model\Order\Creditmemo\Config::class),
            $this->createMock(\Magento\Sales\Model\OrderFactory::class),
            $this->createMock(\Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item\CollectionFactory::class),
            $this->createMock(\Magento\Framework\Math\CalculatorFactory::class),
            $this->createMock(\Magento\Store\Model\StoreManagerInterface::class),
            $this->createMock(\Magento\Sales\Model\Order\Creditmemo\CommentFactory::class),
            $this->createMock(\Magento\Sales\Model\ResourceModel\Order\Creditmemo\Comment\CollectionFactory::class),
            $this->createMock(\Magento\Framework\Pricing\PriceCurrencyInterface::class),
            $this->createMock(\Magento\Framework\Model\ResourceModel\AbstractResource::class),
            $this->createMock(\Magento\Framework\Data\Collection\AbstractDb::class),
            [],
            $this->createMock(\Magento\Sales\Model\Order\InvoiceFactory::class),
            $this->createMock(ScopeConfigInterface::class),
            $this->createMock(\Magento\Sales\Api\OrderRepositoryInterface::class)
        ];
    }

    /**
     * @param int $orderId
     * @param int $orderItemId
     * @param int $qtyToRequest
     * @param int $qtyToRefund
     * @param string $sku
     * @param int $total
     * @param array $expected
     * @param bool $isQtyDecimalAllowed
     * @param bool $isAllowZeroGrandTotal
     */
    #[DataProvider('dataProviderForValidateQty')]
    public function testValidate(
        $orderId,
        $orderItemId,
        $qtyToRequest,
        $qtyToRefund,
        $sku,
        $total,
        array $expected,
        bool $isQtyDecimalAllowed,
        bool $isAllowZeroGrandTotal
    ) {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->any())->method('getValue')->willReturn($isAllowZeroGrandTotal);
        $creditMemoConstructorParams = $this->getCreditMemoMockParams();
        $creditMemoConstructorParams[16] = $scopeConfig;

        $creditmemoMock = $this->getMockBuilder(Creditmemo::class)
            ->setConstructorArgs($creditMemoConstructorParams)
            ->onlyMethods(['getOrderId', 'getItems', 'getGrandTotal', '_construct'])
            ->getMock();

        $creditmemoMock->expects($this->exactly(2))->method('getOrderId')
            ->willReturn($orderId);
        $creditmemoMock->expects($this->once())->method('getGrandTotal')
            ->willReturn($total);

        $creditmemoItemMock = $this->createMock(CreditmemoItemInterface::class);
        $creditmemoItemMock->expects($this->exactly(2))->method('getOrderItemId')
            ->willReturn($orderItemId);
        $creditmemoItemMock->expects($this->never())->method('getSku')
            ->willReturn($sku);
        $creditmemoItemMock->expects($this->atLeastOnce())->method('getQty')
            ->willReturn($qtyToRequest);
        $creditmemoMock->expects($this->exactly(1))->method('getItems')
            ->willReturn([$creditmemoItemMock]);

        $orderMock = $this->createMock(OrderInterface::class);
        $orderItemMock = $this->createPartialMock(
            Item::class,
            ['getIsQtyDecimal', 'getQtyToRefund', 'getItemId', 'getSku']
        );
        $orderItemMock->expects($this->any())->method('getIsQtyDecimal')
            ->willReturn($isQtyDecimalAllowed);
        $orderItemMock->expects($this->any())->method('getQtyToRefund')
            ->willReturn($qtyToRefund);
        $creditmemoItemMock->expects($this->any())->method('getQty')
            ->willReturn($qtyToRequest);
        $orderMock->expects($this->once())->method('getItems')
            ->willReturn([$orderItemMock]);
        $orderItemMock->expects($this->once())->method('getItemId')
            ->willReturn($orderItemId);
        $orderItemMock->expects($this->any())->method('getSku')
            ->willReturn($sku);

        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willReturn($orderMock);

        $this->assertEquals(
            $expected,
            $this->validator->validate($creditmemoMock)
        );
    }

    /**
     * @return array
     */
    public static function dataProviderForValidateQty()
    {
        $sku = 'sku';

        return [
            [
                'orderId' => 1,
                'orderItemId' => 1,
                'qtyToRequest' => 1,
                'qtyToRefund' => 1,
                'sku' => 'sku',
                'total' => 15,
                'expected' => [],
                'isQtyDecimalAllowed' => false,
                'isAllowZeroGrandTotal' => true
            ],
            [
                'orderId' => 1,
                'orderItemId' => 1,
                'qtyToRequest' => 0,
                'qtyToRefund' => 0,
                'sku' => 'sku',
                'total' => 15,
                'expected' => [],
                'isQtyDecimalAllowed' => false,
                'isAllowZeroGrandTotal' => true
            ],
            [
                'orderId' => 1,
                'orderItemId' => 1,
                'qtyToRequest' => 1.5,
                'qtyToRefund' => 3,
                'sku' => 'sku',
                'total' => 5,
                'expected' => [
                    __(
                        'We found an invalid quantity to refund item "%1".',
                        $sku
                    )
                ],
                'isQtyDecimalAllowed' => false,
                'isAllowZeroGrandTotal' => true
            ],
            [
                'orderId' => 1,
                'orderItemId' => 1,
                'qtyToRequest' => 2,
                'qtyToRefund' => 1,
                'sku' => 'sku',
                'total' => 0,
                'expected' => [
                    __(
                        'The quantity to creditmemo must not be greater than the unrefunded quantity'
                        . ' for product SKU "%1".',
                        $sku
                    ),
                    __('The credit memo\'s total must be positive.')
                ],
                'isQtyDecimalAllowed' => false,
                'isAllowZeroGrandTotal' => false
            ],
            [
                'orderId' => 1,
                'orderItemId' => 1,
                'qtyToRequest' => 1,
                'qtyToRefund' => 1,
                'sku' => 'sku',
                'total' => 0,
                'expected' => [],
                'isQtyDecimalAllowed' => false,
                'isAllowZeroGrandTotal' => true
            ]
        ];
    }
}
