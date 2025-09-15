<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Captcha\Model\Cart;

class ConfigPlugin
{
    /**
     * @var \Magento\Captcha\Model\Checkout\ConfigProvider
     */
    protected $configProvider;

    /**
     * @param \Magento\Captcha\Model\Checkout\ConfigProvider $configProvider
     */
    public function __construct(
        \Magento\Captcha\Model\Checkout\ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param array $result
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(\Magento\Checkout\Block\Cart\Sidebar $subject, array $result)
    {
        return array_merge_recursive($result, $this->configProvider->getConfig());
    }
}
