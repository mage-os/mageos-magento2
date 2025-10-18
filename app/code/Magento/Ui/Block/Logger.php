<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Ui\Block;

use Magento\Framework\View\Element\Template;
use Magento\Ui\Model\Config;

/**
 * Logger block
 *
 * @api
 * @since 100.0.2
 */
class Logger extends Template
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Is session storage logging enabled
     *
     * @return bool
     */
    public function isLoggingEnabled()
    {
        return $this->config->isLoggingEnabled();
    }

    /**
     * Get session storage key
     *
     * @return string
     */
    public function getSessionStorageKey()
    {
        return $this->config->getSessionStorageKey();
    }
}
