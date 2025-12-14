<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Theme\Model\Theme;

use Magento\Framework\View\Design\ThemeInterface;
use Magento\Theme\Model\ResourceModel\Theme\Data\CollectionFactory;
use Magento\Theme\Model\Theme\Data\Collection;

/**
 * Detects unregistered themes by comparing filesystem to database
 */
class RegistrationDetector
{
    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     * @param Collection $filesystemCollection
     */
    public function __construct(
        private CollectionFactory $collectionFactory,
        private Collection        $filesystemCollection
    ) {
    }

    /**
     * Check if there are any unregistered themes
     *
     * @return bool
     */
    public function hasUnregisteredTheme(): bool
    {
        return !empty($this->getMissingThemes());
    }

    /**
     * Get list of themes that exist on filesystem but not in database
     *
     * @return string[]
     */
    public function getMissingThemes(): array
    {
        $databaseThemes = $this->getRegisteredThemePaths();
        $filesystemThemes = $this->getFilesystemThemePaths();

        return array_diff($filesystemThemes, $databaseThemes);
    }

    /**
     * Get registered theme paths from database
     *
     * @return array
     */
    private function getRegisteredThemePaths(): array
    {
        $collection = $this->collectionFactory->create()
            ->addTypeFilter(ThemeInterface::TYPE_PHYSICAL);

        $paths = [];
        foreach ($collection as $theme) {
            $paths[] = $theme->getFullPath();
        }
        return $paths;
    }

    /**
     * Get theme paths from filesystem
     *
     * @return array
     */
    private function getFilesystemThemePaths(): array
    {
        $this->filesystemCollection->clear();

        $paths = [];
        foreach ($this->filesystemCollection as $theme) {
            $paths[] = $theme->getFullPath();
        }
        return $paths;
    }
}
