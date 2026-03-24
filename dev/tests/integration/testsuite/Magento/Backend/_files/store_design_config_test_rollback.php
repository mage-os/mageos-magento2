<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Framework\Registry;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\GroupInterfaceFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\StoreInterfaceFactory;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\Data\WebsiteInterfaceFactory;
use Magento\Store\Model\ResourceModel\Group as GroupResource;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\ResourceModel\Website as WebsiteResource;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var WebsiteResource $websiteResource */
$websiteResource = $objectManager->get(WebsiteResource::class);
/** @var StoreResource $storeResource */
$storeResource = $objectManager->get(StoreResource::class);
/** @var GroupResource $groupResource */
$groupResource = $objectManager->get(GroupResource::class);
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$storeCodesAfterRename = ['design_cfg_test_sv_rn', 'design_cfg_test_sv'];
/** @var StoreInterface $store */
$store = $objectManager->get(StoreInterfaceFactory::class)->create();
foreach ($storeCodesAfterRename as $storeCode) {
    $storeResource->load($store, $storeCode, 'code');
    if ($store->getId()) {
        $storeResource->delete($store);
    }
}

/** @var GroupInterface $storeGroup */
$storeGroup = $objectManager->get(GroupInterfaceFactory::class)->create();
$groupResource->load($storeGroup, 'design_cfg_test_grp', 'code');
if ($storeGroup->getId()) {
    $groupResource->delete($storeGroup);
}

/** @var WebsiteInterface $website */
$website = $objectManager->get(WebsiteInterfaceFactory::class)->create();
$websiteResource->load($website, 'design_cfg_test_ws', 'code');
if ($website->getId()) {
    $websiteResource->delete($website);
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
