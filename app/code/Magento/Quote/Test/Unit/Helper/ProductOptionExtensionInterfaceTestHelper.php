<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\GiftCard\Api\Data\GiftCardOptionInterface;
use Magento\Quote\Api\Data\ProductOptionExtensionInterface;

/**
 * Test helper class for ProductOptionExtensionInterface with custom methods
 *
 * This helper implements ProductOptionExtensionInterface and adds custom methods
 * that don't exist on the interface for testing purposes.
 */
class ProductOptionExtensionInterfaceTestHelper implements ProductOptionExtensionInterface
{
    private $data = [];

    /**
     * Custom getBundleOptions method for Bundle testing
     * Note: Returns mixed type to match PHPUnit 10 mock behavior where return types weren't enforced
     *
     * @return mixed
     */
    public function getBundleOptions()
    {
        return $this->data['bundle_options'] ?? null;
    }

    /**
     * Set bundle options for testing
     *
     * @param array|null $options
     * @return self
     */
    public function setBundleOptions($options): self
    {
        $this->data['bundle_options'] = $options;
        return $this;
    }

    /**
     * Custom getCustomOptions method for testing
     *
     * @return array|null
     */
    public function getCustomOptions(): ?array
    {
        return $this->data['custom_options'] ?? null;
    }

    /**
     * Set custom options for testing
     *
     * @param array|null $options
     * @return self
     */
    public function setCustomOptions($options): self
    {
        $this->data['custom_options'] = $options;
        return $this;
    }

    /**
     * Custom getDownloadableOption method for testing
     *
     * @return mixed
     */
    public function getDownloadableOption()
    {
        return $this->data['downloadable_option'] ?? null;
    }

    /**
     * Set downloadable option for testing
     *
     * @param mixed $option
     * @return self
     */
    public function setDownloadableOption($option): self
    {
        $this->data['downloadable_option'] = $option;
        return $this;
    }

    /**
     * Custom getConfigurableItemOptions method for testing
     *
     * @return array|null
     */
    public function getConfigurableItemOptions(): ?array
    {
        return $this->data['configurable_item_options'] ?? null;
    }

    /**
     * Set configurable item options for testing
     *
     * @param array|null $options
     * @return self
     */
    public function setConfigurableItemOptions($options): self
    {
        $this->data['configurable_item_options'] = $options;
        return $this;
    }

    /**
     * Custom getGroupedOptions method for testing
     *
     * @return array|null
     */
    public function getGroupedOptions(): ?array
    {
        return $this->data['grouped_options'] ?? null;
    }

    /**
     * Set grouped options for testing
     *
     * @param array|null $options
     * @return self
     */
    public function setGroupedOptions($options): self
    {
        $this->data['grouped_options'] = $options;
        return $this;
    }

    /**
     * Generic data setter for flexible testing
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setTestData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Generic data getter for flexible testing
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getTestData(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Get gift item option for testing
     *
     * @return GiftCardOptionInterface|null
     */
    public function getGiftcardItemOption()
    {
        return $this->data['giftcard_item_option'] ?? null;
    }

    /**
     * Set gift item option for testing
     *
     * @param GiftCardOptionInterface $giftcardItemOption
     * @return $this
     */
    public function setGiftcardItemOption(GiftCardOptionInterface $giftcardItemOption)
    {
        $this->data['giftcard_item_option'] = $giftcardItemOption;
        return $this;
    }
}
