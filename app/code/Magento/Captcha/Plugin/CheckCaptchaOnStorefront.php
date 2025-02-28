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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

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
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly HelperCaptcha $helper,
        private readonly HttpContext   $httpContext,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

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
        ?string $result
    ): string {
        // Check the value for recaptcha_frontend/type_for/customer_login
        $recaptchaType = $this->scopeConfig->getValue(
            'recaptcha_frontend/type_for/customer_login',
            ScopeInterface::SCOPE_WEBSITE
        );

        if ($this->isLoggedIn() || (!$this->helper->getConfig('enable') && $recaptchaType === null)) {
            return '';
        }

        return $result ?? '';
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    private function isLoggedIn(): ?bool
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
