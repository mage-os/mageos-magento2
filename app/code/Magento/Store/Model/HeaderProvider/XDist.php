<?php

namespace Magento\Store\Model\HeaderProvider;

use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\Response\HeaderProvider\AbstractHeaderProvider;

/**
 * Add distribution name as a header for identification
 */
class XDist extends AbstractHeaderProvider
{
    /**
     * @var string
     */
    protected $headerValue = ProductMetadata::DISTRIBUTION_NAME;

    /**
     * @var string
     */
    protected $headerName = 'X-Dist';
}
