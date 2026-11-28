<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Setup\Patch\Data;

use Magento\Catalog\Setup\Patch\Data\UpdateProductUrlKey;
use Magento\Catalog\Setup\Patch\Data\UpdateProductUrlKeyBackendModel;
use Magento\CatalogUrlRewrite\Setup\Patch\Data\CreateUrlAttributes;
use Magento\Framework\Setup\Patch\PatchFactory;
use Magento\Framework\Setup\Patch\PatchHistory;
use Magento\Framework\Setup\Patch\PatchRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The url_key backend model patch must run after url_key exists, whatever order the patch files are read in
 *
 * PatchReader applies a module's data patches in glob order, and glob sorts by the collation of LC_COLLATE.
 * In byte order UpdateProductUrlKey.php comes first and creates url_key through its dependencies. Under a
 * collation that ignores punctuation, such as glibc's en_US.UTF-8, UpdateProductUrlKeyBackendModel.php comes first.
 *
 * @see https://github.com/mage-os/mageos-magento2/issues/402
 */
class UpdateProductUrlKeyBackendModelTest extends TestCase
{
    /**
     * Resolve the order PatchApplier would apply the patches in and check url_key is created first
     *
     * @param string[] $readOrder
     * @return void
     */
    #[DataProvider('patchReadOrders')]
    public function testUrlKeyIsCreatedBeforeItsBackendModelIsSet(array $readOrder): void
    {
        $patchHistory = $this->createStub(PatchHistory::class);
        $patchHistory->method('isApplied')->willReturn(false);
        $registry = new PatchRegistry($this->createStub(PatchFactory::class), $patchHistory);
        foreach ($readOrder as $patchName) {
            $registry->registerPatch($patchName);
        }

        $applyOrder = array_keys(iterator_to_array($registry->getIterator()));

        $this->assertLessThan(
            array_search(UpdateProductUrlKeyBackendModel::class, $applyOrder, true),
            array_search(CreateUrlAttributes::class, $applyOrder, true),
            'url_key is updated before it is created: ' . implode(', ', $applyOrder)
        );
    }

    /**
     * Orders in which glob can return the two Magento_Catalog patch files
     *
     * @return array<string, array{0: string[]}>
     */
    public static function patchReadOrders(): array
    {
        return [
            'byte order' => [
                [UpdateProductUrlKey::class, UpdateProductUrlKeyBackendModel::class],
            ],
            'collation that ignores punctuation' => [
                [UpdateProductUrlKeyBackendModel::class, UpdateProductUrlKey::class],
            ],
        ];
    }
}
