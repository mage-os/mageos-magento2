<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Catalog\Helper\DefaultCategory;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\GroupInterfaceFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\StoreInterfaceFactory;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\Data\WebsiteInterfaceFactory;
use Magento\Store\Model\ResourceModel\Group as GroupResource;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\ResourceModel\Website as WebsiteResource;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
/** @var WebsiteResource $websiteResource */
$websiteResource = $objectManager->get(WebsiteResource::class);
/** @var StoreResource $storeResource */
$storeResource = $objectManager->get(StoreResource::class);
/** @var GroupResource $groupResource */
$groupResource = $objectManager->get(GroupResource::class);
/** @var DefaultCategory $defaultCategory */
$defaultCategory = $objectManager->get(DefaultCategory::class);

/** @var WebsiteInterface $website */
$website = $objectManager->get(WebsiteInterfaceFactory::class)->create();
$website->setCode('design_cfg_test_ws')->setName('Design Config Test Website');
$websiteResource->save($website);

/** @var GroupInterface $storeGroup */
$storeGroup = $objectManager->get(GroupInterfaceFactory::class)->create();
$storeGroup->setCode('design_cfg_test_grp')
    ->setRootCategoryId($defaultCategory->getId())
    ->setName('Design Config Test Store Group')
    ->setWebsite($website);
$groupResource->save($storeGroup);

$storeManager->reinitStores();

/** @var StoreInterface $store */
$store = $objectManager->get(StoreInterfaceFactory::class)->create();
$store->setCode('design_cfg_test_sv')
    ->setWebsiteId($website->getId())
    ->setGroupId($storeGroup->getId())
    ->setName('Design Config Test Store View')
    ->setSortOrder(10)
    ->setIsActive(1);
$storeResource->save($store);

$storeManager->reinitStores();

/** @var IndexerRegistry $indexerRegistry */
$indexerRegistry = $objectManager->get(IndexerRegistry::class);
$indexerRegistry->get(Fulltext::INDEXER_ID)->reindexAll();
