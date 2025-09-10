<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Dhl\Model\Source\Method;

/**
 * Source model for DHL shipping methods for documentation
 */
class Freenondoc extends \Magento\Dhl\Model\Source\Method\AbstractMethod
{
    /**
     * Carrier Product Type Indicator
     *
     * @var string $_contentType
     */
    protected $_contentType = \Magento\Dhl\Model\Carrier::DHL_CONTENT_TYPE_NON_DOC;

    /**
     * Show 'none' in methods list or not;
     *
     * @var bool
     */
    protected $_noneMethod = true;
}
