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
 * @magentoConfigFixture default_store checkout/payment_failed/template payment_failed_template
 * @magentoConfigFixture default_store checkout/payment_failed/identity support
 *
 * @AllureSuite("Checkout")
 * @AllureFeature("Payment Failed Email")
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
     * Transport builder mock
     *
     * @var TransportBuilderMock
     */
    private TransportBuilderMock $transportBuilder;
    /**
     * Reserved order ID used in fixture.
     */
    private const FIXTURE_RESERVED_ORDER_ID = 'test_order_with_virtual_product';
    /**
     * Payment method code to use in test.
     */
    private const PAYMENT_METHOD = 'checkmo';
    /**
     * Payment failure message used in test.
     */
    private const PAYMENT_FAILURE_MESSAGE = 'Simulated payment failure';
    /**
     * Set up required Magento services for the test.
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
        $this->transportBuilder = $this->objectManager->get(TransportBuilderMock::class);
    }

    /**
     * Test sending the "payment failed" email for an order with a virtual product.
     *
     * This test verifies that:
     * - The payment failure email is sent successfully
     * - The email content does not include shipping address or shipping method
     * - The email contains appropriate payment failure information
     * - The email is properly formatted for virtual products
     *
     * @return void
     */
    public function testSendPaymentFailedEmail(): void
    {
        [$order, $quote] = $this->prepareOrderFromFixtureQuote();
        $this->simulatePaymentFailure($order);
        $this->checkoutHelper->sendPaymentFailedEmail(
            $quote,
            (string)__(self::PAYMENT_FAILURE_MESSAGE),
            $quote->getPayment()->getMethod(),
            $quote->getCheckoutMethod()
        );
        $message = $this->transportBuilder->getSentMessage();
        $this->assertNotNull($message, 'Expected a payment failed email to be sent.');
        $emailContent = $this->extractEmailContent($message->getBody());
        $this->assertVirtualProductEmailContent($emailContent);
    }
    /**
     * Test payment failed email with custom checkout method.
     *
     * @return void
     */
    public function testSendPaymentFailedEmailWithCustomCheckoutMethod(): void
    {
        [$order, $quote] = $this->prepareOrderFromFixtureQuote();
        $quote->setCheckoutMethod('custom_method');
        $this->simulatePaymentFailure($order);
        $this->checkoutHelper->sendPaymentFailedEmail(
            $quote,
            (string)__(self::PAYMENT_FAILURE_MESSAGE),
            $quote->getPayment()->getMethod(),
            $quote->getCheckoutMethod()
        );
        $message = $this->transportBuilder->getSentMessage();
        $this->assertNotNull($message, 'Expected a payment failed email to be sent with custom checkout method.');
        $emailContent = $this->extractEmailContent($message->getBody());
        $this->assertVirtualProductEmailContent($emailContent);
    }
    /**
     * Prepare an order from a fixture quote containing a virtual product.
     *
     * Loads the quote with reserved_order_id from fixture,
     * sets payment method, submits the quote to create the order.
     *
     * @return array{0: Order, 1: Quote} Returns the created order and the original quote.
     */
    private function prepareOrderFromFixtureQuote(): array
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class)
            ->load(self::FIXTURE_RESERVED_ORDER_ID, 'reserved_order_id');
        $this->assertNotNull($quote->getId(), 'Failed to load quote from fixture.');
        $this->assertNotEmpty($quote->getAllItems(), 'Quote from fixture is empty.');
        $this->assertTrue($quote->hasVirtualItems(), 'Quote should contain virtual items.');
        $quote->getPayment()->setMethod(self::PAYMENT_METHOD);
        $quote->collectTotals();
        $order = $this->quoteManagement->submit($quote);
        $this->assertNotNull($order->getId(), 'Order was not created from quote.');
        $this->assertNotEmpty($order->getIncrementId(), 'Order increment ID is missing.');
        return [$order, $quote];
    }
    /**
     * Simulate a payment failure by cancelling the order and adding a history comment.
     *
     * This method updates the order state and status to 'canceled',
     * adds a comment explaining the payment failure, and saves the order.
     *
     * @param Order $order
     * @return void
     */
    private function simulatePaymentFailure(Order $order): void
    {
        $order->setState(Order::STATE_CANCELED)
            ->setStatus(Order::STATE_CANCELED)
            ->addCommentToStatusHistory((string)__('Simulated: Payment failure due to gateway timeout.'));
        $this->orderRepository->save($order);
        $this->assertSame(
            Order::STATE_CANCELED,
            $order->getState(),
            'Order state should be canceled after simulating payment failure.'
        );
    }
    /**
     * Extract email content for testing from various email body formats.
     *
     * @param mixed $emailBody
     * @return string
     */
    private function extractEmailContent($emailBody): string
    {
        // Try different methods to extract email content
        if (method_exists($emailBody, 'bodyToString')) {
            return quoted_printable_decode($emailBody->bodyToString());
        }
        if (method_exists($emailBody, 'getParts')) {
            $parts = $emailBody->getParts();
            if (!empty($parts) && method_exists($parts[0], 'getRawContent')) {
                return $parts[0]->getRawContent();
            }
        }
        if (method_exists($emailBody, 'getContent')) {
            return $emailBody->getContent();
        }
        if (method_exists($emailBody, '__toString')) {
            return (string)$emailBody;
        }
        $this->fail(
            'Unable to extract email content. Email body type: ' . get_class($emailBody) .
            '. Available methods: ' . implode(', ', get_class_methods($emailBody))
        );
    }
    /**
     * Assert virtual product email content meets requirements.
     *
     * @param string $emailContent
     * @return void
     */
    private function assertVirtualProductEmailContent(string $emailContent): void
    {
        // Negative assertions - what should NOT be included for virtual products
        $this->assertStringNotContainsString(
            'Shipping Address',
            $emailContent,
            'Shipping address should not appear in payment failed email for virtual product.'
        );
        $this->assertStringNotContainsString(
            'Shipping Method',
            $emailContent,
            'Shipping method should not appear in payment failed email for virtual product.'
        );
        $this->assertStringNotContainsString(
            'Delivery',
            $emailContent,
            'Delivery information should not appear in payment failed email for virtual product.'
        );
        $this->assertStringNotContainsString(
            'Ship to',
            $emailContent,
            'Ship to information should not appear in payment failed email for virtual product.'
        );
        // Positive assertions - what should be included
        $this->assertStringContainsString(
            self::PAYMENT_FAILURE_MESSAGE,
            $emailContent,
            'Payment failure message should be present in email.'
        );
        $this->assertStringContainsString(
            self::PAYMENT_METHOD,
            $emailContent,
            'Payment method should be mentioned in email.'
        );
        // Verify email is not empty
        $this->assertNotEmpty(
            trim($emailContent),
            'Email content should not be empty.'
        );
        // Verify email contains order information
        $this->assertThat(
            $emailContent,
            $this->logicalOr(
                $this->stringContains('Order'),
                $this->stringContains('Payment'),
                $this->stringContains('Failed')
            ),
            'Email should contain order or payment related information.'
        );
    }
    /**
     * Assert that email contains billing information but not shipping information.
     *
     * @param string $emailContent
     * @return void
     */
    private function assertBillingButNoShippingInformation(string $emailContent): void
    {
        // Billing information should be present
        $this->assertStringContainsString(
            'Billing Address',
            $emailContent,
            'Billing address should be present in payment failed email.'
        );
        // Shipping information should not be present
        $this->assertStringNotContainsString(
            'Shipping Address',
            $emailContent,
            'Shipping address should not be present for virtual products.'
        );
    }
    /**
     * Test that email template variables are properly set.
     *
     * @return void
     */
    public function testEmailTemplateVariables(): void
    {
        [$order, $quote] = $this->prepareOrderFromFixtureQuote();
        $this->simulatePaymentFailure($order);
        $this->checkoutHelper->sendPaymentFailedEmail(
            $quote,
            (string)__(self::PAYMENT_FAILURE_MESSAGE),
            $quote->getPayment()->getMethod(),
            $quote->getCheckoutMethod()
        );
        $message = $this->transportBuilder->getSentMessage();
        $this->assertNotNull($message, 'Expected a payment failed email to be sent.');
        // Verify email headers
        $headers = $message->getHeaders();
        $this->assertNotNull($headers, 'Email headers should be present.');
        // Verify email subject
        $subject = $message->getSubject();
        $this->assertNotEmpty($subject, 'Email subject should not be empty.');
        // Verify email recipient
        $to = $message->getTo();
        $this->assertNotEmpty($to, 'Email recipient should be present.');
    }
}

