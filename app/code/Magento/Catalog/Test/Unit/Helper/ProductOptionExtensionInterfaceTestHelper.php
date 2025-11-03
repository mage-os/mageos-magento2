<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductOptionExtensionInterface;

class ProductOptionExtensionInterfaceTestHelper implements ProductOptionExtensionInterface
{
    /**
     * @var mixed
     */
    private $customOptionsResult;

    /**
     * @return mixed
     */
    public function getCustomOptions()
    {
        return $this->customOptionsResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setCustomOptions($result)
    {
        $this->customOptionsResult = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBundleOptions()
    {
        return null;
    }

    /**
     * @param mixed $bundleOptions
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setBundleOptions($bundleOptions)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDownloadableOption()
    {
        return null;
    }

    /**
     * @param mixed $downloadableOption
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setDownloadableOption($downloadableOption)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfigurableItemOptions()
    {
        return null;
    }

    /**
     * @param mixed $configurableItemOptions
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setConfigurableItemOptions($configurableItemOptions)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGiftcardItemOption()
    {
        return null;
    }

    /**
     * @param mixed $giftcardItemOption
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setGiftcardItemOption($giftcardItemOption)
    {
        return $this;
    }
}

