<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$categoryFactory = $objectManager->get(CategoryInterfaceFactory::class);

// Adding enabled category
$category = $categoryFactory->create();
$category->isObjectNew(true);
$category->setId(3)
    ->setName('Enabled')
    ->setParentId(2)
    ->setPath('1/2/3')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

// Adding category disabled on default scope
$category = $categoryFactory->create();
$category->isObjectNew(true);
$category->setId(4)
    ->setName('Disabled on default scope')
    ->setParentId(2)
    ->setPath('1/2/4')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(false)
    ->setPosition(1)
    ->save();

// Adding category disabled on store scope
$category = $categoryFactory->create();
$category->isObjectNew(true);
$category->setId(5)
    ->setName('Disabled on store scope')
    ->setParentId(2)
    ->setPath('1/2/5')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();
$category->setStoreId(1)
    ->setIsActive(false)
    ->save();
