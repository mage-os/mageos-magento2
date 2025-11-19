<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model\Order\Email\Sender;

use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Checkout\Test\Fixture\PlaceOrder as PlaceOrderFixture;
use Magento\Checkout\Test\Fixture\SetBillingAddress as SetBillingAddressFixture;
use Magento\Checkout\Test\Fixture\SetDeliveryMethod as SetDeliveryMethodFixture;
use Magento\Checkout\Test\Fixture\SetGuestEmail as SetGuestEmailFixture;
use Magento\Checkout\Test\Fixture\SetPaymentMethod as SetPaymentMethodFixture;
use Magento\Checkout\Test\Fixture\SetShippingAddress as SetShippingAddressFixture;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Quote\Test\Fixture\AddProductToCart as AddProductToCartFixture;
use Magento\Quote\Test\Fixture\GuestCart as GuestCartFixture;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Shipping\Model\ShipmentNotifier;
use Magento\TestFramework\Fixture\Config;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Mail\Template\TransportBuilderMock;
use PHPUnit\Framework\TestCase;

/**
 * Test shipment creation with async email notification.
 */
class AdminShipmentAsyncEmailTest extends TestCase
{
    /**
     * @var TransportBuilderMock
     */
    private TransportBuilderMock $transportBuilder;

    /**
     * @var EmailMessageInterface[]
     */
    private array $sentEmails = [];

    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = Bootstrap::getObjectManager();
        $this->transportBuilder = $objectManager->get(TransportBuilderMock::class);
        $this->sentEmails = [];

        $this->transportBuilder->setOnMessageSentCallback(
            function (EmailMessageInterface $message): void {
                $this->sentEmails[] = $message;
            }
        );
    }

    /**
     * Ensures shipment emails created in async mode are dispatched only after the cron runs.
     *
     * @return void
     * @throws LocalizedException
     * @throws MailException
     */
    #[
        Config('payment/checkmo/active', '1'),
        Config('carriers/flatrate/active', '1'),
        Config('sales_email/general/async_sending', '1'),
        Config('sales_email/shipment/enabled', '1'),
        Config('sales_email/general/sending_limit', '10'),
        DataFixture(ProductFixture::class, as: 'product'),
        DataFixture(GuestCartFixture::class, as: 'cart'),
        DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$product.id$']),
        DataFixture(SetBillingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetShippingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetGuestEmailFixture::class, ['cart_id' => '$cart.id$', 'email' => 'async-shipment@example.com']),
        DataFixture(SetDeliveryMethodFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetPaymentMethodFixture::class, ['cart_id' => '$cart.id$', 'method' => 'checkmo']),
        DataFixture(PlaceOrderFixture::class, ['cart_id' => '$cart.id$'], 'order'),
    ]
    public function testShipmentEmailDispatchedByCron(): void
    {
        Bootstrap::getInstance()->loadArea(Area::AREA_ADMINHTML);

        $fixtures = DataFixtureStorageManager::getStorage();
        /** @var Order $fixtureOrder */
        $fixtureOrder = $fixtures->get('order');

        $objectManager = Bootstrap::getObjectManager();
        $orderRepository = $objectManager->get(OrderRepositoryInterface::class);
        $invoiceService = $objectManager->get(InvoiceService::class);
        $invoiceRepository = $objectManager->get(InvoiceRepositoryInterface::class);
        $shipmentRepository = $objectManager->get(ShipmentRepositoryInterface::class);
        $shipmentNotifier = $objectManager->get(ShipmentNotifier::class);
        $shipmentFactory = $objectManager->get(ShipmentFactory::class);

        $order = $orderRepository->get((int)$fixtureOrder->getEntityId());
        $this->assertTrue($order->canInvoice(), 'Order must be invoiceable before creating a shipment.');

        $invoice = $invoiceService->prepareInvoice($order);
        $invoice->register();
        $invoice->setSendEmail(false);
        $invoiceRepository->save($invoice);
        $orderRepository->save($order);

        $this->assertTrue($order->canShip(), 'Order must be shippable after invoicing.');

        $quantities = [];
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getIsVirtual()) {
                continue;
            }
            $qtyToShip = $orderItem->getQtyOrdered() - $orderItem->getQtyShipped();
            if ($qtyToShip > 0) {
                $quantities[$orderItem->getItemId()] = $qtyToShip;
            }
        }
        $this->assertNotEmpty($quantities, 'At least one shippable item is required.');

        $shipment = $shipmentFactory->create($order, $quantities);
        $shipment->register();
        $shipment->setSendEmail(true);

        $shipmentRepository->save($shipment);
        $orderRepository->save($shipment->getOrder());

        $notifyResult = $shipmentNotifier->notify($shipment);
        $this->assertFalse(
            $notifyResult,
            'ShipmentNotifier::notify should return false while the email is queued for async sending.'
        );
        $this->assertCount(
            0,
            $this->sentEmails,
            'No shipment email should be dispatched immediately.'
        );

        $cron = $objectManager->get('SalesShipmentSendEmailsCron');
        $cron->execute();
        $cron->execute();

        $this->assertCount(
            1,
            $this->sentEmails,
            'One shipment email should be dispatched after cron execution.'
        );
        $email = $this->sentEmails[0];
        $this->assertInstanceOf(EmailMessageInterface::class, $email);
        $this->assertStringContainsString('order has shipped', $email->getSubject());
        $this->assertEquals('async-shipment@example.com', $email->getTo()[0]->getEmail());
    }

    protected function tearDown(): void
    {
        $this->transportBuilder->clean();
        $this->sentEmails = [];
        parent::tearDown();
    }
}
