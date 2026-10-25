<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\Query;

use Magento\Catalog\Model\Indexer\Product\Price\Processor as PriceIndexerProcessor;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Test\Fixture\PriceScope as PriceScopeFixture;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Customer\Model\Group;
use Magento\Directory\Model\Currency;
use Magento\Directory\Model\ResourceModel\Currency as CurrencyResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Test\Fixture\Group as GroupFixture;
use Magento\Store\Test\Fixture\Store as StoreFixture;
use Magento\Store\Test\Fixture\Website as WebsiteFixture;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\Config;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test price index calculation of BaseFinalPrice for websites with a non-default base currency.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BaseFinalPriceTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * USD -> EUR rate stored before the test, restored afterwards
     *
     * @var string|false|null
     */
    private $originalRate = null;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        if ($this->originalRate !== null) {
            $currencyResource = $this->objectManager->get(CurrencyResource::class);
            if ($this->originalRate === false) {
                // No API removes a rate; drop the row this test added
                $connection = $this->objectManager->get(ResourceConnection::class)->getConnection();
                $connection->delete(
                    $currencyResource->getTable('directory_currency_rate'),
                    ['currency_from = ?' => 'USD', 'currency_to = ?' => 'EUR']
                );
            } else {
                $currencyResource->saveRates(['USD' => ['EUR' => $this->originalRate]]);
            }
            $currencyResource->_resetState();
        }
        parent::tearDown();
    }

    /**
     * A percentage tier price must not be scaled by the website currency rate.
     *
     * With a USD -> EUR rate of 2, a 10% discount used to be indexed as a 20% discount on the EUR website.
     * DB isolation is off because the store fixture creates sequence tables (DDL); fixtures and tearDown revert.
     *
     * @return void
     */
    #[
        DbIsolation(false),
        AppIsolation(true),
        Config(Currency::XML_PATH_CURRENCY_BASE, 'USD'),
        Config(Store::XML_PATH_PRICE_SCOPE, Store::PRICE_SCOPE_WEBSITE),
        Config(Store::XML_PATH_PRICE_SCOPE, Store::PRICE_SCOPE_WEBSITE, 'website', 'eur_website'),
        Config(Currency::XML_PATH_CURRENCY_BASE, 'EUR', 'website', 'eur_website'),
        DataFixture(WebsiteFixture::class, ['code' => 'eur_website'], as: 'eur_website'),
        DataFixture(GroupFixture::class, ['website_id' => '$eur_website.id$'], as: 'eur_group'),
        DataFixture(StoreFixture::class, ['store_group_id' => '$eur_group.id$'], as: 'eur_store'),
        DataFixture(PriceScopeFixture::class, ['scope' => 'website']),
        DataFixture(
            ProductFixture::class,
            [
                'price' => 100,
                'website_ids' => [1, '$eur_website.id$'],
                'tier_prices' => [
                    [
                        'customer_group_id' => Group::CUST_GROUP_ALL,
                        'qty' => 1,
                        'extension_attributes' => ['website_id' => 0, 'percentage_value' => 10],
                    ],
                ],
            ],
            as: 'product'
        ),
    ]
    public function testPercentageTierPriceIsNotConvertedByCurrencyRate(): void
    {
        $fixtures = DataFixtureStorageManager::getStorage();
        $product = $fixtures->get('product');
        $eurWebsiteId = (int)$fixtures->get('eur_website')->getId();

        $this->setUsdToEurRate(2);

        // Make sure the reindex works with fresh website objects
        $this->objectManager->get(StoreManagerInterface::class)->reinitStores();
        $this->objectManager->get(PriceIndexerProcessor::class)->reindexRow($product->getId(), true);
        $this->assertEquals(2, $this->getIndexedWebsiteRate($eurWebsiteId), 'EUR website rate was not applied.');

        $defaultWebsitePrices = $this->getIndexedPrices((int)$product->getId(), 1);
        $this->assertEquals(100, $defaultWebsitePrices['price']);
        $this->assertEquals(90, $defaultWebsitePrices['final_price']);
        $this->assertEquals(90, $defaultWebsitePrices['min_price']);
        $this->assertEquals(90, $defaultWebsitePrices['tier_price']);

        $eurWebsitePrices = $this->getIndexedPrices((int)$product->getId(), $eurWebsiteId);
        $this->assertEquals(100, $eurWebsitePrices['price']);
        $this->assertEquals(90, $eurWebsitePrices['final_price']);
        $this->assertEquals(90, $eurWebsitePrices['min_price']);
        $this->assertEquals(90, $eurWebsitePrices['tier_price']);
    }

    /**
     * Save the USD -> EUR rate, remembering the previous value for tearDown
     *
     * @param float $rate
     * @return void
     */
    private function setUsdToEurRate(float $rate): void
    {
        $currencyResource = $this->objectManager->get(CurrencyResource::class);
        $currencyResource->_resetState();
        $this->originalRate = $currencyResource->getRate('USD', 'EUR');
        $currencyResource->saveRates(['USD' => ['EUR' => $rate]]);
        // getRate() keeps a static cache that saveRates() does not clear
        $currencyResource->_resetState();
    }

    /**
     * Read the currency rate the price indexer prepared for a website
     *
     * @param int $websiteId
     * @return float
     */
    private function getIndexedWebsiteRate(int $websiteId): float
    {
        $connection = $this->objectManager->get(ResourceConnection::class)->getConnection();

        return (float)$connection->fetchOne(
            $connection->select()
                ->from($connection->getTableName('catalog_product_index_website'), 'rate')
                ->where('website_id = ?', $websiteId)
        );
    }

    /**
     * Read the price index row for the NOT LOGGED IN customer group
     *
     * @param int $productId
     * @param int $websiteId
     * @return array
     */
    private function getIndexedPrices(int $productId, int $websiteId): array
    {
        $row = $this->objectManager->get(CollectionFactory::class)->create()
            ->addIdFilter($productId)
            ->addPriceData(Group::NOT_LOGGED_IN_ID, $websiteId)
            ->getFirstItem()
            ->getData();
        $this->assertNotEmpty($row, sprintf('No price index row for website %d.', $websiteId));

        return $row;
    }
}
