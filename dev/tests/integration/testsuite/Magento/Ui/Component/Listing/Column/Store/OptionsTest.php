<?php
/************************************************************************
 *
 * Copyright 2024 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 * ************************************************************************
 */
declare(strict_types=1);

namespace Magento\Ui\Component\Listing\Column\Store;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Test\Fixture\Store as StoreFixture;
use Magento\Store\Ui\Component\Listing\Column\Store\Options;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    /**
     * @var DataFixtureStorage
     */
    private $fixtures;

    /**
     * @var Options
     */
    private $storesList;

    /**
     * @throws LocalizedException
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->fixtures = DataFixtureStorageManager::getStorage();
        $this->storesList = $objectManager->get(Options::class);
    }

    #[
        DataFixture(StoreFixture::class, ["name" =>"Default's Store"], 'store'),
    ]
    public function testStoreLabeleWithSingleQuote()
    {
        $storeName = $this->fixtures->get('store')->getName();
        $storeStructure = $this->storesList->toOptionArray();
        self::assertNotEmpty($storeStructure);
        $storeGroups = $storeStructure[0]['value'];
        foreach ($storeGroups as $storeGroup) {
            $storeViews = $storeGroup['value'];
            $storeLabels = array_column($storeViews, 'label');
            $expectedLabel = str_repeat(' ', 8) . $storeName;
            self::assertContainsEquals($expectedLabel, $storeLabels);
        }
    }
}
