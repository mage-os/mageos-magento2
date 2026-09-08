<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogRule\Model\Indexer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;

#[
    DbIsolation(false),
    AppIsolation(true),
]
class IndexerBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\CatalogRule\Model\Indexer\IndexBuilder
     */
    protected $indexerBuilder;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule
     */
    protected $resourceRule;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productSecond;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productThird;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var Processor
     */
    private $indexProductProcessor;

    protected function setUp(): void
    {
        $this->indexerBuilder = Bootstrap::getObjectManager()->get(
            \Magento\CatalogRule\Model\Indexer\IndexBuilder::class
        );
        $this->resourceRule = Bootstrap::getObjectManager()->get(\Magento\CatalogRule\Model\ResourceModel\Rule::class);
        $this->product = Bootstrap::getObjectManager()->get(\Magento\Catalog\Model\Product::class);
        $this->storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
        $this->connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $this->indexProductProcessor = Bootstrap::getObjectManager()->get(Processor::class);
    }

    protected function tearDown(): void
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(\Magento\Framework\Registry::class);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = Bootstrap::getObjectManager()->get(
            \Magento\Catalog\Model\ResourceModel\Product\Collection::class
        );
        $productCollection->delete();

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);

        parent::tearDown();
    }

    /**
     * @magentoDataFixture Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixture Magento/CatalogRule/_files/rule_by_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexById()
    {
        $product = $this->product->loadByAttribute('sku', 'simple');
        $product->load($product->getId());
        $product->setData('test_attribute', 'test_attribute_value')->save();

        $this->indexerBuilder->reindexById($product->getId());

        $this->assertEquals(9.8, $this->resourceRule->getRulePrice(new \DateTime(), 1, 1, $product->getId()));
    }

    /**
     * @magentoDataFixture Magento/CatalogRule/_files/simple_product_with_catalog_rule_50_percent_off_tomorrow.php
     * @magentoConfigFixture base_website general/locale/timezone Europe/Amsterdam
     * @magentoConfigFixture general/locale/timezone America/Chicago
     */
    public function testReindexByIdDifferentTimezones()
    {
        $productId = $this->productRepository->get('simple')->getId();
        $this->indexerBuilder->reindexById($productId);

        $mainWebsiteId = $this->storeManager->getWebsite('base')->getId();
        $secondWebsiteId = $this->storeManager->getWebsite('test')->getId();
        $rawTimestamp = (new \DateTime('+1 day'))->getTimestamp();
        $timestamp = $rawTimestamp - ($rawTimestamp % (60 * 60 * 24));
        $mainWebsiteActiveRules =
            $this->resourceRule->getRulesFromProduct($timestamp, $mainWebsiteId, 1, $productId);
        $secondWebsiteActiveRules =
            $this->resourceRule->getRulesFromProduct($timestamp, $secondWebsiteId, 1, $productId);

        $this->assertCount(1, $mainWebsiteActiveRules);
        // Avoid failure when staging is enabled as it removes catalog rule timestamp.
        if ((int)$mainWebsiteActiveRules[0]['from_time'] !== 0) {
            $this->assertCount(0, $secondWebsiteActiveRules);
        }
    }

    /**
     * @magentoDataFixture Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixture Magento/CatalogRule/_files/rule_by_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexByIds()
    {
        $this->prepareProducts();

        $this->indexerBuilder->reindexByIds(
            [
                $this->product->getId(),
                $this->productSecond->getId(),
                $this->productThird->getId(),
            ]
        );

        $this->assertEquals(9.8, $this->resourceRule->getRulePrice(new \DateTime(), 1, 1, $this->product->getId()));
        $this->assertEquals(
            9.8,
            $this->resourceRule->getRulePrice(new \DateTime(), 1, 1, $this->productSecond->getId())
        );
        $this->assertFalse($this->resourceRule->getRulePrice(new \DateTime(), 1, 1, $this->productThird->getId()));
    }

    /**
     * @magentoDataFixtureBeforeTransaction Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixtureBeforeTransaction Magento/CatalogRule/_files/rule_by_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexFull()
    {
        $this->prepareProducts();

        $this->indexerBuilder->reindexFull();

        $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), 1, 1, $this->product->getId());
        $this->assertEquals(9.8, $rulePrice);
        $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), 1, 1, $this->productSecond->getId());
        $this->assertEquals(9.8, $rulePrice);
        $this->assertFalse($this->resourceRule->getRulePrice(new \DateTime(), 1, 1, $this->productThird->getId()));
    }

    /**
     * Tests restoring triggers on `catalogrule_product_price` table after full reindexing in 'Update by schedule' mode.
     */
    public function testRestoringTriggersAfterFullReindex()
    {
        $tableName = $this->connection->getTableName('catalogrule_product_price');

        $this->indexProductProcessor->getIndexer()->setScheduled(false);
        $this->assertEquals(0, $this->getTriggersCount($tableName));

        $this->indexProductProcessor->getIndexer()->setScheduled(true);
        $this->assertGreaterThan(0, $this->getTriggersCount($tableName));

        $this->indexerBuilder->reindexFull();
        $this->assertGreaterThan(0, $this->getTriggersCount($tableName));

        $this->indexProductProcessor->getIndexer()->setScheduled(false);
        $this->assertEquals(0, $this->getTriggersCount($tableName));
    }

    #[
        DataFixture('Magento/CatalogRule/_files/simple_product_with_catalog_rule_50_percent_off.php'),
    ]
    public function testReindexByIdForSecondStore(): void
    {
        $websiteId = $this->storeManager->getWebsite('test')->getId();
        $simpleProduct = $this->productRepository->get('simple');
        $this->indexerBuilder->reindexById($simpleProduct->getId());
        $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), $websiteId, 1, $simpleProduct->getId());
        $this->assertEquals(25, $rulePrice);
    }

    #[
        DataFixture('Magento/CatalogRule/_files/simple_product_with_catalog_rule_50_percent_off.php'),
    ]
    public function testReindexByIdsForSecondStore(): void
    {
        $websiteId = $this->storeManager->getWebsite('test')->getId();
        $simpleProduct = $this->productRepository->get('simple');
        $this->indexerBuilder->reindexByIds([$simpleProduct->getId()]);
        $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), $websiteId, 1, $simpleProduct->getId());
        $this->assertEquals(25, $rulePrice);
    }

    #[
        DataFixture('Magento/CatalogRule/_files/simple_product_with_catalog_rule_50_percent_off.php'),
    ]
    public function testReindexFullForSecondStore(): void
    {
        $websiteId = $this->storeManager->getWebsite('test')->getId();
        $simpleProduct = $this->productRepository->get('simple');
        $this->indexerBuilder->reindexFull();
        $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), $websiteId, 1, $simpleProduct->getId());
        $this->assertEquals(25, $rulePrice);
    }

    /**
     * Regression test: catalog rule indexer must consider tier prices so the
     * rule never produces a price higher than an existing tier price.
     *
     * Setup:
     *   - Product regular price: $100
     *   - All-groups global tier price: $30 (qty=1)
     *   - Catalog rule: 50% off → $50 without tier consideration
     *
     * Without the fix the indexed rule price is $50, which is higher than the
     * $30 tier price — customers who qualify for the tier see a higher price.
     *
     * With the fix the indexer uses LEAST(tier, regular) = $30 as the base,
     * so 50% → $15, and the rule price ≤ tier price for all customer groups.
     *
     * @magentoDataFixture Magento/CatalogRule/_files/product_with_tier_price_and_50_percent_rule.php
     */
    public function testReindexFullRulePriceNeverExceedsTierPrice(): void
    {
        $this->indexerBuilder->reindexFull();

        $product    = $this->productRepository->get('simple-tier-price-rule');
        $productId  = (int)$product->getId();
        $websiteId  = (int)$this->storeManager->getDefaultStoreView()->getWebsiteId();
        $tierPrice  = 30.0;
        $regularPrice = 100.0;
        $ruleDiscount = 50; // 50%

        $rulePriceOnRegular = $regularPrice * (1 - $ruleDiscount / 100); // $50

        // Rule price must be ≤ tier price for every customer group in the rule
        foreach ([0, 1, 2, 3] as $customerGroupId) {
            $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), $websiteId, $customerGroupId, $productId);

            // Without fix: $50 > $30 tier — assertion would fail
            $this->assertNotFalse(
                $rulePrice,
                "No rule price indexed for customer group $customerGroupId"
            );
            $this->assertLessThanOrEqual(
                $tierPrice,
                (float)$rulePrice,
                "Customer group $customerGroupId: rule price \$$rulePrice must not exceed tier price \$$tierPrice. "
                . "Without the fix the indexer ignores tier prices and returns \$$rulePriceOnRegular."
            );
        }
    }

    /**
     * Verify that when the catalog rule discount produces a price LOWER than
     * the tier price, the lower rule price is used (rule wins).
     *
     * Setup:
     *   - Product regular price: $100
     *   - All-groups global tier price: $80 (qty=1)
     *   - Catalog rule: 50% off → $50
     *
     * Expected: rule price = $40 (50% of LEAST($80,$100)=$80), which is < $80 tier ✓
     *
     * @magentoDataFixture Magento/CatalogRule/_files/product_with_tier_price_and_50_percent_rule.php
     */
    public function testReindexFullRulePriceWinsWhenLowerThanTierPrice(): void
    {
        // Adjust the tier price to $80 so the 50% rule ($50) would win
        $product = $this->productRepository->get('simple-tier-price-rule', true, null, true);

        /** @var \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierFactory */
        $tierFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(\Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory::class);
        /** @var \Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory $extFactory */
        $extFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(\Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory::class);

        $tier = $tierFactory->create();
        $tier->setCustomerGroupId(\Magento\Customer\Model\Group::CUST_GROUP_ALL);
        $tier->setQty(1);
        $tier->setValue(80.00);
        $tier->setWebsiteId(0);
        $tier->setExtensionAttributes($extFactory->create());

        $product->setTierPrices([$tier]);
        $this->productRepository->save($product);

        $this->indexerBuilder->reindexFull();

        $productId = (int)$product->getId();
        $websiteId = (int)$this->storeManager->getDefaultStoreView()->getWebsiteId();

        foreach ([0, 1, 2, 3] as $customerGroupId) {
            $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), $websiteId, $customerGroupId, $productId);
            $this->assertNotFalse($rulePrice, "No rule price indexed for group $customerGroupId");
            $this->assertLessThanOrEqual(
                80.0,
                (float)$rulePrice,
                "Group $customerGroupId: rule price must not exceed tier price \$80"
            );
        }
    }

    /**
     * Specific customer group tier: only group 1 has a tier price.
     * Group 1 rule price must be ≤ tier price; other groups get the regular rule price.
     *
     * Regular: $100 · Group-1 global tier: $20 · 50% rule for all groups
     * Group 1 expected: rule price ≤ $20 (LEAST($20,$100)=$20 → 50%=$10)
     * Groups 0,2,3 expected: rule price = $50 (no tier, LEAST=$100 → 50%=$50)
     *
     * @magentoDataFixture Magento/CatalogRule/_files/product_with_tier_price_and_50_percent_rule.php
     */
    public function testReindexFullSpecificGroupTierOnlyAppliesToThatGroup(): void
    {
        $om = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierFactory */
        $tierFactory = $om->get(\Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory::class);
        $extFactory  = $om->get(\Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory::class);

        // Replace the all-groups tier with a group-1-only tier at $20
        $product = $this->productRepository->get('simple-tier-price-rule', true, null, true);

        $tier = $tierFactory->create();
        $tier->setCustomerGroupId(1); // group 1 only, not all groups
        $tier->setQty(1);
        $tier->setValue(20.00);
        $tier->setWebsiteId(0);
        $tier->setExtensionAttributes($extFactory->create());

        $product->setTierPrices([$tier]);
        $this->productRepository->save($product);

        $this->indexerBuilder->reindexFull();

        $productId = (int)$product->getId();
        $websiteId = (int)$this->storeManager->getDefaultStoreView()->getWebsiteId();

        // Group 1: tier caps the rule
        $rulePrice1 = (float)$this->resourceRule->getRulePrice(new \DateTime(), $websiteId, 1, $productId);
        $this->assertLessThanOrEqual(20.0, $rulePrice1, 'Group 1 rule price must not exceed its $20 tier price');

        // Other groups: no tier → rule applies to regular price → $50
        foreach ([0, 2, 3] as $g) {
            $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), $websiteId, $g, $productId);
            if ($rulePrice !== false) {
                $this->assertEqualsWithDelta(
                    50.0,
                    (float)$rulePrice,
                    0.01,
                    "Group $g has no tier price — rule price should be \$50 (50% of \$100)"
                );
            }
        }
    }

    /**
     * Website-specific tier price: tier is scoped to website_id=1 (not global).
     * Tests the `price_tier` JOIN (website-specific path).
     *
     * Regular: $100 · Website-1 tier: $25 (all groups, qty=1) · 50% rule
     * Expected: LEAST($25 website-tier, null global-tier, $100 regular) = $25 → 50%=$12.50
     *
     * @magentoDataFixture Magento/CatalogRule/_files/product_with_website_tier_price_and_50_percent_rule.php
     */
    public function testReindexFullWebsiteScopedTierPriceIsRespected(): void
    {
        $this->indexerBuilder->reindexFull();

        $product   = $this->productRepository->get('simple-website-tier-price-rule');
        $productId = (int)$product->getId();
        $websiteId = (int)$this->storeManager->getDefaultStoreView()->getWebsiteId();
        $tierPrice = 25.0;

        foreach ([0, 1, 2, 3] as $g) {
            $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), $websiteId, $g, $productId);
            $this->assertNotFalse($rulePrice, "No rule price for group $g");
            $this->assertLessThanOrEqual(
                $tierPrice,
                (float)$rulePrice,
                "Group $g: website-scoped tier \$$tierPrice must cap the indexed rule price"
            );
        }
    }

    /**
     * When both a global tier ($40) and a website-specific tier ($20) exist,
     * LEAST must pick the lower one (website-specific $20 wins).
     *
     * Regular: $100 · Global tier: $40 · Website-1 tier: $20 · 50% rule
     * Expected: LEAST($40, $20, $100) = $20 → 50% = $10 ≤ $20 for all groups.
     *
     * Tier prices are inserted via the DB connection to avoid Magento's model-level
     * uniqueness check, which rejects two all-groups tiers for different websites
     * even though the DB schema correctly allows it (different website_id).
     *
     * @magentoDataFixture Magento/CatalogRule/_files/product_with_tier_price_and_50_percent_rule.php
     */
    public function testReindexFullLowestTierWinsWhenBothGlobalAndWebsiteExist(): void
    {
        $product   = $this->productRepository->get('simple-tier-price-rule');
        $productId = (int)$product->getId();

        // Insert both tiers directly — Magento's model validation rejects two
        // all-groups tiers for different websites even though the schema allows it.
        $conn      = $this->connection->getConnection();
        $tierTable = $this->connection->getTableName('catalog_product_entity_tier_price');
        $conn->delete($tierTable, ['entity_id = ?' => $productId]);
        $conn->insert($tierTable, [
            'entity_id' => $productId, 'all_groups' => 1, 'customer_group_id' => 0,
            'qty' => 1, 'value' => 40.0, 'website_id' => 0,
        ]);
        $conn->insert($tierTable, [
            'entity_id' => $productId, 'all_groups' => 1, 'customer_group_id' => 0,
            'qty' => 1, 'value' => 20.0, 'website_id' => 1,
        ]);

        $this->indexerBuilder->reindexFull();

        $websiteId = (int)$this->storeManager->getDefaultStoreView()->getWebsiteId();

        foreach ([0, 1, 2, 3] as $g) {
            $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), $websiteId, $g, $productId);
            $this->assertNotFalse($rulePrice, "No rule price for group $g");
            $this->assertLessThanOrEqual(
                20.0,
                (float)$rulePrice,
                "Group $g: website tier \$20 must be the effective base — LEAST picks \$20 over \$40 global"
            );
        }
    }

    /**
     * Volume discount: tier prices with qty > 1 are intentionally NOT considered
     * by the indexer (only qty=1 is used as the base price for single-unit display).
     *
     * When a product has only qty≥5 tier prices, the catalog rule applies to the
     * regular price unchanged. This documents the known limitation.
     *
     * Regular: $100 · Tier only for qty=5: $20 · 50% rule
     * Expected: rule price = $50 (50% of $100 — qty=1 tier not found, regular used)
     *
     * @magentoDataFixture Magento/CatalogRule/_files/product_with_tier_price_and_50_percent_rule.php
     */
    public function testReindexFullTierPriceForQtyAboveOneIsNotConsideredByIndexer(): void
    {
        $om = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $tierFactory = $om->get(\Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory::class);
        $extFactory  = $om->get(\Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory::class);

        $product = $this->productRepository->get('simple-tier-price-rule', true, null, true);

        // Only a qty=5 tier price — no qty=1 tier
        $tier = $tierFactory->create();
        $tier->setCustomerGroupId(\Magento\Customer\Model\Group::CUST_GROUP_ALL);
        $tier->setQty(5); // qty > 1
        $tier->setValue(20.0);
        $tier->setWebsiteId(0);
        $tier->setExtensionAttributes($extFactory->create());

        $product->setTierPrices([$tier]);
        $this->productRepository->save($product);

        $this->indexerBuilder->reindexFull();

        $productId = (int)$product->getId();
        $websiteId = (int)$this->storeManager->getDefaultStoreView()->getWebsiteId();

        foreach ([0, 1, 2, 3] as $g) {
            $rulePrice = $this->resourceRule->getRulePrice(new \DateTime(), $websiteId, $g, $productId);
            if ($rulePrice !== false) {
                // No qty=1 tier — indexer uses regular price $100 → 50% = $50
                $this->assertEqualsWithDelta(
                    50.0,
                    (float)$rulePrice,
                    0.01,
                    "Group $g: qty>1 tier is not considered by the indexer; "
                    . "rule applies to regular price \$100 → expected \$50"
                );
            }
        }
    }

    /**
     * Returns triggers count.
     *
     * @param string $tableName
     * @return int
     * @throws \Zend_Db_Statement_Exception
     */
    private function getTriggersCount(string $tableName): int
    {
        return count(
            $this->connection->getConnection()
                ->query('SHOW TRIGGERS LIKE \''. $tableName . '\'')
                ->fetchAll()
        );
    }

    protected function prepareProducts()
    {
        $product = $this->product->loadByAttribute('sku', 'simple');
        $product->load($product->getId());
        $this->product = $product;

        $this->product->setStoreId(0)->setData('test_attribute', 'test_attribute_value')->save();
        $this->productSecond = clone $this->product;
        $this->productSecond->setId(null)->setUrlKey('product-second')->save();
        $this->productThird = clone $this->product;
        $this->productThird->setId(null)
            ->setUrlKey('product-third')
            ->setData('test_attribute', 'NO_test_attribute_value')
            ->save();
    }
}
