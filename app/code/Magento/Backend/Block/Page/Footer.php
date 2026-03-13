<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Block\Page;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\VersionCheck\VersionComparisonInterface;
use Magento\Framework\App\DistributionMetadataInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Adminhtml footer block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Footer extends Template
{
    private const XML_PATH_RELEASES_URL = 'system/version_check/releases_url';

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::page/footer.phtml';

    /**
     * @var ProductMetadataInterface|DistributionMetadataInterface
     * @since 100.1.0
     */
    protected $productMetadata;

    /**
     * @var VersionComparisonInterface|null
     */
    private $versionComparison;

    /**
     * @param Context $context
     * @param ProductMetadataInterface $productMetadata
     * @param VersionComparisonInterface|null $versionComparison
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductMetadataInterface $productMetadata,
        ?VersionComparisonInterface $versionComparison = null,
        array $data = []
    ) {
        $this->productMetadata = $productMetadata;
        $this->versionComparison = $versionComparison
            ?: ObjectManager::getInstance()->get(VersionComparisonInterface::class);

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->setShowProfiler(true);
    }

    /**
     * Get product version
     *
     * @return string
     * @since 100.1.0
     */
    public function getMagentoVersion()
    {
        return $this->productMetadata->getDistributionVersion();
    }

    /**
     * Get product name
     *
     * @return string
     */
    public function getName()
    {
        return $this->productMetadata->getDistributionName();
    }

    /**
     * Check if a newer version is available
     *
     * @return bool
     */
    public function isUpdateAvailable(): bool
    {
        return $this->versionComparison !== null && $this->versionComparison->isUpdateAvailable();
    }

    /**
     * Get the latest available version
     *
     * @return string|null
     */
    public function getLatestVersion(): ?string
    {
        return $this->versionComparison?->getLatestVersion();
    }

    /**
     * Check if the update is a major or minor version bump
     *
     * @return bool
     */
    public function isMajorOrMinorUpdate(): bool
    {
        return $this->versionComparison !== null && $this->versionComparison->isMajorOrMinorUpdate();
    }

    /**
     * Get the URL to the Mage-OS releases page
     *
     * @return string
     */
    public function getReleasesUrl(): string
    {
        return (string) $this->_scopeConfig->getValue(self::XML_PATH_RELEASES_URL);
    }

    /**
     * @inheritdoc
     */
    public function getCacheKeyInfo(): array
    {
        $info = parent::getCacheKeyInfo();
        try {
            $info[] = 'latest_version_' . ($this->versionComparison?->getLatestVersion() ?? 'none');
        } catch (Exception $e) {
            $info[] = 'latest_version_error';
        }
        return $info;
    }

    /**
     * @inheritdoc
     * @since 101.0.0
     */
    protected function getCacheLifetime()
    {
        return 3600 * 24 * 10;
    }
}
