<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
namespace Magento\SalesRule\Plugin;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Model\SubmitQuoteValidator;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\Service\OrderService;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\ResourceModel\Coupon\Usage;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\MessageQueue\EnvironmentPreconditionException;
use Magento\TestFramework\MessageQueue\PreconditionFailedException;
use Magento\TestFramework\MessageQueue\PublisherConsumerController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Framework\DB\Transaction;

/**
 * Test increasing coupon usages after order placing and decreasing after order cancellation.
 *
 * @magentoAppArea frontend
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponUsagesTest extends TestCase
{
    /**
     * @var PublisherConsumerController
     */
    private $publisherConsumerController;

    /**
     * @var array
     */
    private $consumers = ['sales.rule.update.coupon.usage'];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Usage
     */
    private $usage;

    /**
     * @var DataObject
     */
    private $couponUsage;

    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->usage = $this->objectManager->get(Usage::class);
        $this->couponUsage = $this->objectManager->create(DataObject::class);
        $this->quoteManagement = $this->objectManager->get(QuoteManagement::class);
        $this->orderService = $this->objectManager->get(OrderService::class);

        $this->publisherConsumerController = Bootstrap::getObjectManager()->create(
            PublisherConsumerController::class,
            [
                'consumers' => $this->consumers,
                'logFilePath' => TESTS_TEMP_DIR . "/MessageQueueTestLog.txt",
                'maxMessages' => 100,
                'appInitParams' => Bootstrap::getInstance()->getAppInitParams()
            ]
        );
        try {
            $this->publisherConsumerController->startConsumers();
        } catch (EnvironmentPreconditionException $e) {
            $this->markTestSkipped($e->getMessage());
        } catch (PreconditionFailedException $e) {
            $this->fail(
                $e->getMessage()
            );
        }
        parent::setUp();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->publisherConsumerController->stopConsumers();
        parent::tearDown();
    }

    /**
     * Test increasing coupon usages after order placing and decreasing after order cancellation.
     *
     * @magentoDataFixture Magento/SalesRule/_files/coupons_limited_order.php
     * @magentoDbIsolation disabled
     */
    public function testSubmitQuoteAndCancelOrder()
    {
        $customerId = 1;
        $couponCode = 'one_usage';
        $reservedOrderId = 'test01';

        /** @var Coupon $coupon */
        $coupon = $this->objectManager->create(Coupon::class);
        $coupon->loadByCode($couponCode);
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load($reservedOrderId, 'reserved_order_id');

        // Make sure coupon usages value is incremented then order is placed.
        $order = $this->quoteManagement->submit($quote);
        sleep(30); // timeout to processing Magento queue
        $this->usage->loadByCustomerCoupon($this->couponUsage, $customerId, $coupon->getId());
        $coupon->loadByCode($couponCode);

        self::assertEquals(
            1,
            $coupon->getTimesUsed()
        );
        self::assertEquals(
            1,
            $this->couponUsage->getTimesUsed()
        );

        // Make sure order coupon usages value is decremented then order is cancelled.
        $this->orderService->cancel($order->getId());
        $this->usage->loadByCustomerCoupon($this->couponUsage, $customerId, $coupon->getId());
        $coupon->loadByCode($couponCode);

        self::assertEquals(
            0,
            $coupon->getTimesUsed()
        );
        self::assertEquals(
            0,
            $this->couponUsage->getTimesUsed()
        );
    }

    /**
     * Test to decrement coupon usages after exception on order placing
     *
     * @param array $mockObjects
     * @magentoDataFixture Magento/SalesRule/_files/coupons_limited_order.php
     * @magentoDbIsolation disabled
     * @dataProvider quoteSubmitFailureDataProvider
     */
    public function testQuoteSubmitFailure(array $mockObjects)
    {
        if (!empty($mockObjects['orderManagement'])) {
            $mockObjects['orderManagement'] = $mockObjects['orderManagement']($this);
        } elseif (!empty($mockObjects['submitQuoteValidator'])) {
            $mockObjects['submitQuoteValidator'] = $mockObjects['submitQuoteValidator']($this);
        }

        $customerId = 1;
        $couponCode = 'one_usage';
        $reservedOrderId = 'test01';

        /** @var Coupon $coupon */
        $coupon = $this->objectManager->get(Coupon::class);
        $coupon->loadByCode($couponCode);
        /** @var Quote $quote */
        $quote = $this->objectManager->get(Quote::class);
        $quote->load($reservedOrderId, 'reserved_order_id');

        /** @var QuoteManagement $quoteManagement */
        $quoteManagement = $this->objectManager->create(
            QuoteManagement::class,
            $mockObjects
        );

        try {
            $quoteManagement->submit($quote);
        } catch (\Exception $exception) {
            sleep(30); // timeout to processing queue
            $this->usage->loadByCustomerCoupon($this->couponUsage, $customerId, $coupon->getId());
            $coupon->loadByCode($couponCode);
            self::assertEquals(
                0,
                $coupon->getTimesUsed()
            );
            self::assertEquals(
                0,
                $this->couponUsage->getTimesUsed()
            );
        }
    }

    /**
     * @return array
     */
    public static function quoteSubmitFailureDataProvider(): array
    {
        $orderManagement = static function (self $testCase) {
            $mock = $testCase->createMock(OrderManagementInterface::class);
            $mock->expects($testCase->once())
                ->method('place')
                ->willThrowException(new \Exception());
            return $mock;
        };

        $submitQuoteValidator = static function (self $testCase) {
            $mock = $testCase->createMock(SubmitQuoteValidator::class);
            $mock->expects($testCase->once())
                ->method('validateQuote')
                ->willThrowException(new \Exception());
            return $mock;
        };

        return [
            'order placing failure' => [
                ['orderManagement' => $orderManagement]
            ],
            'quote validation failure' => [
                ['submitQuoteValidator' => $submitQuoteValidator]
            ],
        ];
    }

    /**
     * Test that coupon usage is NOT decremented when order is partially invoiced and then cancelled
     *
     * @magentoDataFixture Magento/SalesRule/_files/coupons_limited_order_once.php
     * @magentoDbIsolation disabled
     * @throws LocalizedException
     */
    public function testCancelOrderAfterPartialInvoice()
    {
        $customerId = 1;
        $couponCode = 'test_once_usage';
        $reservedOrderId = 'test_quote_two_products';
        /** @var Coupon $coupon */
        $coupon = $this->objectManager->create(Coupon::class);
        $coupon->loadByCode($couponCode);
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load($reservedOrderId, 'reserved_order_id');
        $order = $this->quoteManagement->submit($quote);
        sleep(30);
        $this->usage->loadByCustomerCoupon($this->couponUsage, $customerId, $coupon->getId());
        $coupon->loadByCode($couponCode);
        self::assertEquals(1, $coupon->getTimesUsed());
        self::assertEquals(1, $this->couponUsage->getTimesUsed());

        /** @var InvoiceManagementInterface $invoiceService */
        $invoiceService = $this->objectManager->get(InvoiceManagementInterface::class);
        $orderItems = $order->getAllItems();
        $firstItem = reset($orderItems);
        $invoiceItems = [$firstItem->getId() => $firstItem->getQtyOrdered()];
        $invoice = $invoiceService->prepareInvoice($order, $invoiceItems);
        $invoice->register();
        $order->setIsInProcess(true);
        /** @var Transaction $transactionSave */
        $transactionSave = $this->objectManager->create(Transaction::class);
        $transactionSave->addObject($invoice)
            ->addObject($order)
            ->save();
        self::assertGreaterThan(
            0,
            abs($invoice->getDiscountAmount()),
            'Invoice should have discount amount applied'
        );
        self::assertGreaterThan(
            0,
            abs($order->getDiscountInvoiced()),
            'Order should have invoiced discount amount'
        );
        $this->orderService->cancel($order->getId());
        $this->usage->loadByCustomerCoupon($this->couponUsage, $customerId, $coupon->getId());
        $coupon->loadByCode($couponCode);
        self::assertEquals(1, $coupon->getTimesUsed());
        self::assertEquals(1, $this->couponUsage->getTimesUsed());
    }
}
