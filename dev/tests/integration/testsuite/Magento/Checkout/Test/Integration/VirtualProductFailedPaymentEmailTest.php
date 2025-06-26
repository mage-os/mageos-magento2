<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Integration;

use Magento\Checkout\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
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
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 * @magentoDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
 */
class VirtualProductFailedPaymentEmailTest extends TestCase
{
    /** @var ObjectManagerInterface */
    private ObjectManagerInterface $objectManager;

    /** @var QuoteManagement */
    private QuoteManagement $quoteManagement;

    /** @var QuoteFactory */
    private QuoteFactory $quoteFactory;

    /** @var Data */
    private Data $checkoutHelper;

    /** @var OrderRepositoryInterface */
    private OrderRepositoryInterface $orderRepository;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->quoteManagement = $this->objectManager->get(QuoteManagement::class);
        $this->quoteFactory = $this->objectManager->get(QuoteFactory::class);
        $this->checkoutHelper = $this->objectManager->get(Data::class);
        $this->orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
    }

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

        // Compatibility with Magento 2.4.6+ and 2.4.7+
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
    private function prepareOrderFromFixtureQuote(): array
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteFactory->create()->loadByIdWithoutStore(1);
        $this->assertTrue((bool)$quote->getId(), 'Quote was not loaded from fixture.');
        $this->assertNotEmpty($quote->getAllItems(), 'Quote from fixture is empty.');
        $quote->getPayment()->setMethod('checkmo');

        $order = $this->quoteManagement->submit($quote);
        $this->assertNotNull($order->getId(), 'Order was not created.');
        $this->assertNotEmpty($order->getIncrementId(), 'Order increment ID is missing.');
        return [$order, $quote];
    }
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
