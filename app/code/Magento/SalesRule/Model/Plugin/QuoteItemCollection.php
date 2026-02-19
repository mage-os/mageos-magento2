<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Plugin;

class QuoteItemCollection
{
    /**
     * @param RequestTypeRegistry     $requestTypeRegistry
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetQuote(
        \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $subject,
        \Magento\Quote\Model\Quote $quote
    ) {
        if ($quote->getTriggerRecollect() == 1 && $this->requestTypeRegistry->isGetRequestOrQuery()) {
            $this->requestTypeRegistry->setIsGetRequestOrQuery(false);
        }
    }
}
