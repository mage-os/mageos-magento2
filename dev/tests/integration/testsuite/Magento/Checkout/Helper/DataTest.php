<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Checkout\Helper;

use Magento\Checkout\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Mail\Template\TransportBuilderMock;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for sending the "payment failed" email on virtual product order failure.
 *
 * Ensures that when a virtual product order fails during payment, the appropriate failure email is
 * sent and does not include any shipping address or method information.
 *
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 * @magentoDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
 */
class DataTest extends TestCase
{
    /**
     * Magento Object Manager
     *
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * Quote management service
     *
     * @var QuoteManagement
     */
    private QuoteManagement $quoteManagement;

    /**
     * Quote factory
     *
     * @var QuoteFactory
     */
    private QuoteFactory $quoteFactory;

    /**
     * Checkout data helper
     *
     * @var Data
     */
    private Data $checkoutHelper;

    /**
     * Order repository
     *
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * Set up required Magento services for the test
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->quoteManagement = $this->objectManager->get(QuoteManagement::class);
        $this->quoteFactory = $this->objectManager->get(QuoteFactory::class);
        $this->checkoutHelper = $this->objectManager->get(Data::class);
        $this->orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
    }

    /**
     * Test the sending of the "payment failed" email for a virtual product order.
     *
     * Asserts that:
     * - The email is sent
     * - The email does not contain shipping address or shipping method
     *
     * @return void
     */
    public function testSendPaymentFailedEmail(): void
    {
        [$order, $quote] = $this->prepareOrderFromFixtureQuote();
        $this->simulatePaymentFailure($order);

        $this->checkoutHelper->sendPaymentFailedEmail(
            $quote,
            __('Simulated payment failure')->render(),
            $quote->getPayment()->getMethod(),
            $quote->getCheckoutMethod()
        );

        /** @var TransportBuilderMock $transportBuilder */
        $transportBuilder = $this->objectManager->get(TransportBuilderMock::class);

        /** @var \Magento\Framework\Mail\EmailMessageInterface $message */
        $message = $transportBuilder->getSentMessage();
        $this->assertNotNull($message, 'Expected a payment failed email to be sent.');

        $emailBody = $message->getBody();
        $emailContent = method_exists($emailBody, 'bodyToString')
            ? quoted_printable_decode($emailBody->bodyToString())
            : $emailBody->getParts()[0]->getRawContent();

        $this->assertStringNotContainsString(
            'Shipping Address',
            $emailContent,
            'Shipping address should not appear in the payment failed email for virtual product.'
        );
        $this->assertStringNotContainsString(
            'Shipping Method',
            $emailContent,
            'Shipping method should not appear in the payment failed email for virtual product.'
        );
    }

    /**
     * Prepare an order from a quote fixture containing a virtual product.
     *
     * Loads a predefined quote and submits it to create an order.
     *
     * @return array{0: Order, 1: Quote}
     */
    private function prepareOrderFromFixtureQuote(): array
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class)
            ->load('test_order_with_virtual_product', 'reserved_order_id');

        $this->assertTrue((bool)$quote->getId(), 'Quote was not loaded from fixture.');
        $this->assertNotEmpty($quote->getAllItems(), 'Quote from fixture is empty.');

        $quote->getPayment()->setMethod('checkmo');

        $order = $this->quoteManagement->submit($quote);
        $this->assertNotNull($order->getId(), 'Order was not created.');
        $this->assertNotEmpty($order->getIncrementId(), 'Order increment ID is missing.');

        return [$order, $quote];
    }

    /**
     * Simulate a payment failure by cancelling the order and adding a history comment.
     *
     * @param Order $order
     * @return void
     */
    private function simulatePaymentFailure(Order $order): void
    {
        $order->setStatus(Order::STATE_CANCELED)
            ->setState(Order::STATE_CANCELED)
            ->addCommentToStatusHistory(__('Simulated: Payment failure due to gateway timeout.'));

        $this->orderRepository->save($order);

        $this->assertSame(
            Order::STATE_CANCELED,
            $order->getState(),
            'Order state should be canceled after simulating payment failure.'
        );
    }
}
