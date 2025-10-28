<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\SalesRule\Model\Plugin;

class QuoteItemCollection
{
    /**
     * @param RequestTypeRegistry   $requestTypeRegistry
     */
    public function __construct(
        private RequestTypeRegistry $requestTypeRegistry
    ) {
    }

    /**
     * Set the Request Type before setting the quote in the Quote Item Collection.
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $subject The collection instance.
     * @param \Magento\Quote\Model\Quote $quote The quote to be processed.
     * @return void
     */
    public function beforeSetQuote(
        \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $subject,
        \Magento\Quote\Model\Quote $quote
    ) {
        if ($quote->getTriggerRecollect() && $this->requestTypeRegistry->isGetRequestOrQuery()) {
            $this->requestTypeRegistry->setIsGetRequestOrQuery(false);
        }
    }
}
