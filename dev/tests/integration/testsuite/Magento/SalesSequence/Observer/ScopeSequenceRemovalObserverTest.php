<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesSequence\Observer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group as GroupResource;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\ResourceModel\Website as WebsiteResource;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Store\Test\Fixture\Group as GroupFixture;
use Magento\Store\Test\Fixture\Store as StoreFixture;
use Magento\Store\Test\Fixture\Website as WebsiteFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Sequences of store views deleted together with their website or store group must be removed.
 */
#[DbIsolation(false)]
class ScopeSequenceRemovalObserverTest extends TestCase
{
    #[
        DataProvider('scopeDataProvider'),
        DataFixture(WebsiteFixture::class, as: 'website'),
        DataFixture(GroupFixture::class, ['website_id' => '$website.id$'], 'group'),
        DataFixture(StoreFixture::class, ['store_group_id' => '$group.id$'], 'store'),
    ]
    public function testSequencesAreRemovedWithScope(string $scope): void
    {
        $fixtures = DataFixtureStorageManager::getStorage();
        $storeId = (int)$fixtures->get('store')->getId();
        $this->assertNotEmpty($this->getSequenceTables($storeId), 'Sequences were not created for the store view.');

        $this->deleteScope($scope, (int)$fixtures->get($scope)->getId());

        $this->assertEmpty($this->getSequenceTables($storeId));
        $this->assertEmpty($this->getSequenceMetaIds($storeId));
    }

    /**
     * @return array
     */
    public static function scopeDataProvider(): array
    {
        return [
            'website' => ['website'],
            'store group' => ['group'],
            'store view' => ['store'],
        ];
    }

    /**
     * @param string $scope
     * @param int $id
     * @return void
     */
    private function deleteScope(string $scope, int $id): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $registry = $objectManager->get(Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        [$factory, $resourceClass] = match ($scope) {
            'website' => [WebsiteFactory::class, WebsiteResource::class],
            'group' => [GroupFactory::class, GroupResource::class],
            'store' => [StoreFactory::class, StoreResource::class],
        };
        $model = $objectManager->get($factory)->create();
        $resource = $objectManager->get($resourceClass);
        $resource->load($model, $id);
        $resource->delete($model);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }

    /**
     * @param int $storeId
     * @return string[]
     */
    private function getSequenceTables(int $storeId): array
    {
        $resource = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $pattern = '/^' . preg_quote($resource->getTableName('sequence_'), '/') . '[a-z]+_' . $storeId . '$/';

        return array_values(array_filter(
            $resource->getConnection('sales')->listTables(),
            static fn (string $table): bool => (bool)preg_match($pattern, $table)
        ));
    }

    /**
     * @param int $storeId
     * @return array
     */
    private function getSequenceMetaIds(int $storeId): array
    {
        $resource = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $connection = $resource->getConnection('sales');

        return $connection->fetchCol(
            $connection->select()
                ->from($resource->getTableName('sales_sequence_meta'), ['meta_id'])
                ->where('store_id = ?', $storeId)
        );
    }
}
