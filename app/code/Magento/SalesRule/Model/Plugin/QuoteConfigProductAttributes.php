<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\SalesRule\Model\Plugin;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Model\Quote\Config;
use Magento\SalesRule\Model\ResourceModel\Rule as RuleResource;

class QuoteConfigProductAttributes
{
    /**
     * @var RuleResource
     */
    private $ruleResource;

    /**
     * @var array|null
     */
    private $activeAttributeCodes;

    /**
     * Cache key for active salesrule attributes
     */
    private const CACHE_KEY = 'salesrule_active_product_attributes';

    /**
     * Cache tag for salesrule attributes
     */
    private const CACHE_TAG = 'salesrule';

    /**
     * @param RuleResource $ruleResource
     * @param RequestTypeRegistry $requestTypeRegistry
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        RuleResource $ruleResource,
        private RequestTypeRegistry $requestTypeRegistry,
        private CacheInterface $cache,
        private SerializerInterface $serializer
    ) {
        $this->ruleResource = $ruleResource;
    }

    /**
     * Append sales rule product attribute keys to select by quote item collection
     *
     * @param Config $subject
     * @param array $attributeKeys
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetProductAttributes(Config $subject, array $attributeKeys): array
    {
        if ($this->requestTypeRegistry->isGetRequestOrQuery()) {
            return $attributeKeys;
        }

        $cachedData = $this->cache->load(self::CACHE_KEY);

        if ($cachedData !== false) {
            $this->activeAttributeCodes = $this->serializer->unserialize($cachedData);
        } else {
            $this->activeAttributeCodes = array_column(
                $this->ruleResource->getActiveAttributes(),
                'attribute_code'
            );
            $this->cache->save(
                $this->serializer->serialize($this->activeAttributeCodes),
                self::CACHE_KEY,
                [self::CACHE_TAG]
            );
        }

        return array_merge($attributeKeys, $this->activeAttributeCodes);
    }
}
