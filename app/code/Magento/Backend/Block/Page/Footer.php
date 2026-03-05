<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Block\Page;

use Magento\Backend\Api\VersionComparisonInterface;
use Magento\Framework\App\DistributionMetadataInterface;

/**
 * Adminhtml footer block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Footer extends \Magento\Backend\Block\Template
{
    private const XML_PATH_RELEASES_URL = 'system/version_check/releases_url';

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::page/footer.phtml';

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface|DistributionMetadataInterface
     * @since 100.1.0
     */
    protected $productMetadata;

    /**
     * @var VersionComparisonInterface|null
     */
    private $versionComparison;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param VersionComparisonInterface|null $versionComparison
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        ?VersionComparisonInterface $versionComparison = null,
        array $data = []
    ) {
        $this->productMetadata = $productMetadata;
        $this->versionComparison = $versionComparison;
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
     * @since 101.0.0
     */
    protected function getCacheLifetime()
    {
        return 3600 * 24 * 10;
    }
}
