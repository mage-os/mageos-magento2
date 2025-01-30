<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\ViewModel;

use Magento\Backend\Model\Image\UploadResizeConfigInterface;

/**
 * Get configuration values for frontend image uploader.
 */
class uploadResizeConfigValue implements \Magento\Framework\View\Element\Block\ArgumentInterface

{
    /**
     * @var UploadResizeConfigInterface
     */
    private UploadResizeConfigInterface $uploadResizeConfig;

    /**
     * @param UploadResizeConfigInterface $uploadResizeConfig
     */
    public function __construct(
        UploadResizeConfigInterface $uploadResizeConfig
    ) {
        $this->uploadResizeConfig = $uploadResizeConfig;
    }

    /**
     * Get maximal width value for resized image
     *
     * @return int
     */
    public function getMaxWidth(): int
    {
        return $this->uploadResizeConfig->getMaxWidth();
    }

    /**
     * Get maximal height value for resized image
     *
     * @return int
     */
    public function getMaxHeight(): int
    {
        return $this->uploadResizeConfig->getMaxHeight();
    }

    /**
     * Get config value for frontend resize
     *
     * @return bool
     */
    public function isResizeEnabled(): bool
    {
        return $this->uploadResizeConfig->isResizeEnabled();
    }


}
