<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Checkout\Api\Exception;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Thrown when too many payment processing/saving requests have been initiated by a user.
 *
 * @api
 */
class PaymentProcessingRateLimitExceededException extends LocalizedException
{
    /**
     * @param Phrase $phrase
     * @param \Exception|null $cause
     */
    public function __construct(Phrase $phrase, ?\Exception $cause = null)
    {
        parent::__construct($phrase, $cause, 429);
    }
}
