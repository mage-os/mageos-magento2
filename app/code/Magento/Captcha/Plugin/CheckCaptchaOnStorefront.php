<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Captcha\Plugin;

use Magento\Captcha\Helper\Data as HelperCaptcha;
use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Check need captcha for authentication popup
 */
class CheckCaptchaOnStorefront
{
    /**
     * CheckCaptchaOnStorefront constructor
     *
     * @param HelperCaptcha $helper
     * @param HttpContext $httpContext
     */
    public function __construct(
        private readonly HelperCaptcha $helper,
        private readonly HttpContext   $httpContext
    ) {}

    /**
     * Remove template when login or disable captcha storefront
     *
     * @param AuthenticationPopup $subject
     * @param string $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetTemplate(
        AuthenticationPopup $subject,
        string $result
    ): string {
        if ($this->isLoggedIn() || !$this->helper->getConfig('enable')) {
            return '';
        }

        return $result;
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    private function isLoggedIn(): bool
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
