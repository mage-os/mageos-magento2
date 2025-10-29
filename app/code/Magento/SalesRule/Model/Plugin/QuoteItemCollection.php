<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Plugin;

class QuoteItemCollection
{
    /**
     * @param RequestTypeRegistry     $requestTypeRegistry
     * @param TriggerRecollectState   $triggerRecollectState
     */
    public function __construct(
        private RequestTypeRegistry $requestTypeRegistry,
        private TriggerRecollectState $triggerRecollectState
    ) {
    }

    /**
     * Set the Request Type before setting the quote in the Quote Item Collection.
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $subject The collection instance.
     * @param \Magento\Quote\Model\Quote $quote The quote to be processed.
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetQuote(
        \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $subject,
        \Magento\Quote\Model\Quote $quote
    ) {
        if ($quote->getTriggerRecollect() == 1 && $this->requestTypeRegistry->isGetRequestOrQuery()) {
            $this->triggerRecollectState->setTriggerRecollect(1);
        }
    }
}
