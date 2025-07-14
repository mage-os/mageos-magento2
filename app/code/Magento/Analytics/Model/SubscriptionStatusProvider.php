<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
namespace Magento\Analytics\Model;

use Magento\Analytics\Model\Config\Backend\Baseurl\SubscriptionUpdateHandler;
use Magento\Analytics\Model\Config\Backend\Enabled\SubscriptionHandler;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\FlagManager;

/**
 * Provider of subscription status.
 */
class SubscriptionStatusProvider
{
    /**
     * Represents an enabled subscription state.
     */
    public const ENABLED = "Enabled";

    /**
     * Represents a failed subscription state.
     */
    public const FAILED = "Failed";

    /**
     * Represents a pending subscription state.
     */
    public const PENDING = "Pending";

    /**
     * Represents a disabled subscription state.
     */
    public const DISABLED = "Disabled";

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var AnalyticsToken
     */
    private $analyticsToken;

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param AnalyticsToken $analyticsToken
     * @param FlagManager $flagManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AnalyticsToken $analyticsToken,
        FlagManager $flagManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->analyticsToken = $analyticsToken;
        $this->flagManager = $flagManager;
    }

    /**
     * Retrieve subscription status to Magento BI Advanced Reporting.
     *
     * Statuses:
     * Enabled - if subscription is enabled and MA token was received;
     * Pending - if subscription is enabled and MA token was not received;
     * Disabled - if subscription is not enabled.
     * Failed - if subscription is enabled and token was not received after attempts ended.
     *
     * @return string
     */
    public function getStatus()
    {
        $isSubscriptionEnabledInConfig = $this->scopeConfig->getValue('analytics/subscription/enabled');
        if ($isSubscriptionEnabledInConfig) {
            return $this->getStatusForEnabledSubscription();
        }

        return $this->getStatusForDisabledSubscription();
    }

    /**
     * Retrieve status for subscription that enabled in config.
     *
     * @return string
     */
    public function getStatusForEnabledSubscription()
    {
        $status = static::ENABLED;
        if ($this->flagManager->getFlagData(SubscriptionUpdateHandler::PREVIOUS_BASE_URL_FLAG_CODE)) {
            $status = self::PENDING;
        }

        if (!$this->analyticsToken->isTokenExist()) {
            $status = static::PENDING;
            if ($this->flagManager->getFlagData(SubscriptionHandler::ATTEMPTS_REVERSE_COUNTER_FLAG_CODE) === null) {
                $status = static::FAILED;
            }
        }

        return $status;
    }

    /**
     * Retrieve status for subscription that disabled in config.
     *
     * @return string
     */
    public function getStatusForDisabledSubscription()
    {
        return static::DISABLED;
    }
}
