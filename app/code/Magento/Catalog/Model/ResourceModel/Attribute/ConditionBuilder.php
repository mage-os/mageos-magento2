<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\Catalog\Model\ResourceModel\Attribute;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\Framework\Model\Entity\ScopeInterface;

/**
 * Builds scope-related conditions for catalog attributes
 *
 * Class ConditionBuilder
 */
class ConditionBuilder
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ConditionBuilder constructor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Returns conditions for existing attribute actions (update/delete) if attribute scope is "website"
     *
     * @param AbstractAttribute $attribute
     * @param EntityMetadataInterface $metadata
     * @param ScopeInterface[] $scopes
     * @param string $linkFieldValue
     * @return array
     * @throws NoSuchEntityException
     */
    public function buildExistingAttributeWebsiteScope(
        AbstractAttribute $attribute,
        EntityMetadataInterface $metadata,
        array $scopes,
        $linkFieldValue
    ) {
        $website = $this->getWebsiteForWebsiteScope($scopes);
        if (!$website) {
            return [];
        }
        $storeIds = $this->getStoreIds($website);

        $condition = [
            $metadata->getLinkField() . ' = ?' => $linkFieldValue,
            'attribute_id = ?' => $attribute->getAttributeId(),
        ];

        $conditions = [];
        foreach ($storeIds as $storeId) {
            $identifier = $metadata->getEntityConnection()->quoteIdentifier(Store::STORE_ID);
            $condition[$identifier . ' = ?'] = $storeId;
            $conditions[] = $condition;
        }

        return $conditions;
    }

    /**
     * Returns conditions for new attribute action (insert) if attribute scope is "website"
     *
     * @param AbstractAttribute $attribute
     * @param EntityMetadataInterface $metadata
     * @param ScopeInterface[] $scopes
     * @param string $linkFieldValue
     * @return array
     * @throws NoSuchEntityException
     */
    public function buildNewAttributesWebsiteScope(
        AbstractAttribute $attribute,
        EntityMetadataInterface $metadata,
        array $scopes,
        $linkFieldValue
    ) {
        $website = $this->getWebsiteForWebsiteScope($scopes);
        if (!$website) {
            return [];
        }
        $storeIds = $this->getStoreIds($website);

        $condition = [
            $metadata->getLinkField() => $linkFieldValue,
            'attribute_id' => $attribute->getAttributeId(),
        ];

        $conditions = [];
        foreach ($storeIds as $storeId) {
            $condition[Store::STORE_ID] = $storeId;
            $conditions[] = $condition;
        }

        return $conditions;
    }

    /**
     * Get website for website scope
     *
     * @param array $scopes
     * @return null|Website
     * @throws NoSuchEntityException
     */
    private function getWebsiteForWebsiteScope(array $scopes)
    {
        $store = $this->getStoreFromScopes($scopes);
        return $store ? $store->getWebsite() : null;
    }

    /**
     * Get store from scopes
     *
     * @param ScopeInterface[] $scopes
     * @return StoreInterface|null
     * @throws NoSuchEntityException
     */
    private function getStoreFromScopes(array $scopes)
    {
        foreach ($scopes as $scope) {
            if (Store::STORE_ID === $scope->getIdentifier()) {
                return $this->storeManager->getStore($scope->getValue());
            }
        }

        return null;
    }

    /**
     * Get storeIds from the website
     *
     * @param Website $website
     * @return array
     */
    private function getStoreIds(Website $website): array
    {
        $storeIds = $website->getStoreIds();

        if (empty($storeIds) && $website->getCode() === Website::ADMIN_CODE) {
            $storeIds[] = Store::DEFAULT_STORE_ID;
        }
        return $storeIds;
    }
}
