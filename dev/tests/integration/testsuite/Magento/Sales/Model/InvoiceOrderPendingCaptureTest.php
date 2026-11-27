<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Fixture\Config;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Invoicing through the API (POST /V1/order/:orderId/invoice) must treat a pending capture like the admin does
 *
 * @see https://github.com/mage-os/mageos-magento2/issues/417
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
#[DbIsolation(true)]
class InvoiceOrderPendingCaptureTest extends TestCase
{
    private const METHOD_MODEL_PATH = 'payment/' . PendingCapturePaymentMethodStub::CODE . '/model';
    private const CAPTURE_PENDING_PATH = 'payment/' . PendingCapturePaymentMethodStub::CODE . '/capture_pending';

    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    #[Config(self::METHOD_MODEL_PATH, PendingCapturePaymentMethodStub::class, ScopeInterface::SCOPE_STORE, 'default')]
    #[Config(self::CAPTURE_PENDING_PATH, 1, ScopeInterface::SCOPE_STORE, 'default')]
    #[DataFixture('Magento/Sales/_files/order.php')]
    public function testPendingCaptureLeavesInvoiceOpenAndOrderInPaymentReview(): void
    {
        [$invoice, $order] = $this->invoiceWithOnlineCapture();

        $this->assertSame(
            [
                'invoice transaction' => 'mageos-capture-100000001',
                'invoice state' => Invoice::STATE_OPEN,
                'order state' => Order::STATE_PAYMENT_REVIEW,
                'order status' => Order::STATE_PAYMENT_REVIEW,
                'order total paid' => 0.0,
            ],
            $this->describe($invoice, $order)
        );
    }

    #[Config(self::METHOD_MODEL_PATH, PendingCapturePaymentMethodStub::class, ScopeInterface::SCOPE_STORE, 'default')]
    #[Config(self::CAPTURE_PENDING_PATH, 0, ScopeInterface::SCOPE_STORE, 'default')]
    #[DataFixture('Magento/Sales/_files/order.php')]
    public function testCompletedCaptureMarksInvoiceAndOrderPaid(): void
    {
        [$invoice, $order] = $this->invoiceWithOnlineCapture();

        $this->assertSame(
            [
                'invoice transaction' => 'mageos-capture-100000001',
                'invoice state' => Invoice::STATE_PAID,
                'order state' => Order::STATE_PROCESSING,
                'order status' => Order::STATE_PROCESSING,
                'order total paid' => (float)$invoice->getGrandTotal(),
            ],
            $this->describe($invoice, $order)
        );
    }

    /**
     * Switch the fixture order to the stub payment method, invoice it with online capture and reload both documents
     *
     * @return array{0: InvoiceInterface, 1: OrderInterface}
     */
    private function invoiceWithOnlineCapture(): array
    {
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        $payment = $order->getPayment();
        $payment->setMethod(PendingCapturePaymentMethodStub::CODE);
        $this->objectManager->get(OrderPaymentRepositoryInterface::class)->save($payment);

        $invoiceId = $this->objectManager->get(InvoiceOrderInterface::class)->execute($order->getEntityId(), true);

        return [
            $this->objectManager->create(InvoiceRepositoryInterface::class)->get($invoiceId),
            $this->objectManager->create(OrderRepositoryInterface::class)->get($order->getEntityId()),
        ];
    }

    /**
     * Collect the values that tell whether the documents were treated as paid
     *
     * @param InvoiceInterface $invoice
     * @param OrderInterface $order
     * @return array<string, int|string|float|null>
     */
    private function describe(InvoiceInterface $invoice, OrderInterface $order): array
    {
        return [
            'invoice transaction' => $invoice->getTransactionId(),
            'invoice state' => (int)$invoice->getState(),
            'order state' => $order->getState(),
            'order status' => $order->getStatus(),
            'order total paid' => (float)$order->getTotalPaid(),
        ];
    }
}
