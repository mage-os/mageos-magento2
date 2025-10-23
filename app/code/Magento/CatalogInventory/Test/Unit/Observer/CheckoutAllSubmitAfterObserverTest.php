<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Observer;

use Magento\CatalogInventory\Observer\CheckoutAllSubmitAfterObserver;
use Magento\CatalogInventory\Observer\ReindexQuoteInventoryObserver;
use Magento\CatalogInventory\Observer\SubtractQuoteInventoryObserver;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote;
use Magento\Quote\Test\Unit\Helper\QuoteTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class CheckoutAllSubmitAfterObserverTest extends TestCase
{
    /**
     * @var CheckoutAllSubmitAfterObserver
     */
    protected $observer;

    /**
     * @var SubtractQuoteInventoryObserver|MockObject
     */
    protected $subtractQuoteInventoryObserver;

    /**
     * @var ReindexQuoteInventoryObserver|MockObject
     */
    protected $reindexQuoteInventoryObserver;

    /**
     * @var Event|MockObject
     */
    protected $event;

    /**
     * @var Observer|MockObject
     */
    protected $eventObserver;

    protected function setUp(): void
    {
        $this->subtractQuoteInventoryObserver = $this->createMock(
            SubtractQuoteInventoryObserver::class
        );

        $this->reindexQuoteInventoryObserver = $this->createMock(
            ReindexQuoteInventoryObserver::class
        );

        // Create Event with all required methods via __call
        $this->event = new Event();

        $this->eventObserver = $this->createMock(Observer::class);

        $this->eventObserver->expects($this->atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->event);

        // Direct instantiation instead of ObjectManagerHelper
        $this->observer = new CheckoutAllSubmitAfterObserver(
            $this->subtractQuoteInventoryObserver,
            $this->reindexQuoteInventoryObserver
        );
    }

    public function testCheckoutAllSubmitAfter()
    {
        // Create QuoteTestHelper for Quote with getInventoryProcessed method
        $quote = new QuoteTestHelper();

        // Use setter instead of expects for the anonymous class
        $quote->setInventoryProcessed(false);
        $this->event->setQuote($quote);

        $this->subtractQuoteInventoryObserver->expects($this->once())
            ->method('execute')
            ->with($this->eventObserver);

        $this->reindexQuoteInventoryObserver->expects($this->once())
            ->method('execute')
            ->with($this->eventObserver);

        $this->observer->execute($this->eventObserver);
    }
}
