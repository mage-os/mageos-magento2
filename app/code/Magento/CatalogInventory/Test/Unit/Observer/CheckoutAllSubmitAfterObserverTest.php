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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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

        // Create anonymous class for Event with all required methods
        $this->event = new class extends Event {
            private $product = null;
            private $collection = null;
            private $creditmemo = null;
            private $quote = null;
            private $website = null;

            public function __construct() {
                parent::__construct();
            }

            public function getProduct() {
                return $this->product;
            }

            public function setProduct($product) {
                $this->product = $product;
                return $this;
            }

            public function getCollection() {
                return $this->collection;
            }

            public function setCollection($collection) {
                $this->collection = $collection;
                return $this;
            }

            public function getCreditmemo() {
                return $this->creditmemo;
            }

            public function setCreditmemo($creditmemo) {
                $this->creditmemo = $creditmemo;
                return $this;
            }

            public function getQuote() {
                return $this->quote;
            }

            public function setQuote($quote) {
                $this->quote = $quote;
                return $this;
            }

            public function getWebsite() {
                return $this->website;
            }

            public function setWebsite($website) {
                $this->website = $website;
                return $this;
            }
        };

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
        // Create anonymous class for Quote with getInventoryProcessed method
        $quote = new class extends Quote {
            private $inventoryProcessed = false;

            public function __construct() {
                
            }

            public function getInventoryProcessed() {
                return $this->inventoryProcessed;
            }

            public function setInventoryProcessed($inventoryProcessed) {
                $this->inventoryProcessed = $inventoryProcessed;
                return $this;
            }
        };

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
