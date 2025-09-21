<?php
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\DirectoryGraphQl\Model\Cache\Tag\Strategy\Config;

use Magento\DirectoryGraphQl\Model\Resolver\Country\Identity;
use Magento\Framework\App\Config\ValueInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Config\Cache\Tag\Strategy\TagGeneratorInterface;

/**
 * Generator that generates cache tags for country configuration
 */
class CountryTagGenerator implements TagGeneratorInterface
{
    /**
     * @var string[]
     */
    private $countryConfigPaths = [
        'general/locale/code',
        'general/country/allow'
    ];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function generateTags(ValueInterface $config): array
    {
        if (in_array($config->getPath(), $this->countryConfigPaths)) {
            if ($config->getScope() == ScopeInterface::SCOPE_WEBSITES) {
                $website = $this->storeManager->getWebsite($config->getScopeId());
                $storeIds = $website->getStoreIds();
            } elseif ($config->getScope() == ScopeInterface::SCOPE_STORES) {
                $storeIds = [$config->getScopeId()];
            } else {
                $storeIds = array_keys($this->storeManager->getStores());
            }
            $tags = [];
            foreach ($storeIds as $storeId) {
                $tags[] = sprintf('%s_%s', Identity::CACHE_TAG, $storeId);
            }
            return $tags;
        }
        return [];
    }
}
