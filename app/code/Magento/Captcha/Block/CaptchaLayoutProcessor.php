<?php
/**
 * Copyright 2025 Adobe.
 * All Rights Reserved.
 */

declare(strict_types=1);
namespace Magento\Captcha\Block;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Captcha\Helper\Data as HelperCaptcha;

class CaptchaLayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @param HelperCaptcha $helper
     */
    public function __construct(
        private readonly HelperCaptcha $helper
    ) {
    }

    /**
     * Remove captcha from checkout page if it is disabled
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout): array
    {
        if(!$this->helper->getConfig('enable')) {
            if (isset($jsLayout['components']['checkout']['children']['authentication']['children']['captcha'])) {
                unset($jsLayout['components']['checkout']['children']['authentication']['children']['captcha']);
            }
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']['children']['additional-login-form-fields']['children']['captcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']['children']['additional-login-form-fields']['children']['captcha']);
            }
            if (isset($jsLayout['components']['authenticationPopup']['children']['captcha'])) {
                unset($jsLayout['components']['authenticationPopup']['children']['captcha']);
            }
        }
        return $jsLayout;
    }
}
