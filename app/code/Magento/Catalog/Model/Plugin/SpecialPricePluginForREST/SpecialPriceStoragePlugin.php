<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\Catalog\Model\Plugin\SpecialPricePluginForREST;

use Magento\Catalog\Model\Product\Price\SpecialPriceStorage;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Special price storage Plugin to handle website scope issue at the frontend (only for REST API calls)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SpecialPriceStoragePlugin
{
    public function __construct(
        private StoreManagerInterface $storeManager
    ) {}

    public function aroundUpdate(SpecialPriceStorage $subject, callable $proceed, array $prices)
    {
        $prices = $this->applyWebsitePrices($prices);
        return $proceed($prices);
    }

    private function applyWebsitePrices(array $formattedPrices): array
    {
        $newPrices = [];

        foreach ($formattedPrices as $price) {
            // Add the original price first
            $newPrices[] = $price;

            if ($price->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                continue;
            }

            $store = $this->storeManager->getStore($price->getStoreId());
            $website = $store->getWebsite();
            $storeIds = $website->getStoreIds();

            // Unset origin store view to avoid duplication
            unset($storeIds[$price->getStoreId()]);

            foreach ($storeIds as $storeId) {
                /** @var \Magento\Catalog\Model\Product\Price\SpecialPrice $cloned */
                $cloned = clone $price;
                $cloned->setStoreId((int) $storeId);
                $newPrices[] = $cloned;
            }
        }

        return $newPrices;
    }

}
