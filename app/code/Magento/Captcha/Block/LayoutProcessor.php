<?php
namespace Magento\Captcha\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

class LayoutProcessor implements LayoutProcessorInterface
{
    const CAPTCHA_ENABLED_PATH = 'customer/captcha/enable'; // Captcha config path

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        die('LayoutProcessor');
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Modify jsLayout to exclude Captcha if disabled.
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout): array
    {
        // Check Captcha enable status
        $isCaptchaEnabled = $this->scopeConfig->isSetFlag(self::CAPTCHA_ENABLED_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!$isCaptchaEnabled) {
            // Remove Captcha-related components
            if (isset($jsLayout['components']['captcha'])) {
                unset($jsLayout['components']['captcha']);
            }
        }

        return $jsLayout;
    }
}
