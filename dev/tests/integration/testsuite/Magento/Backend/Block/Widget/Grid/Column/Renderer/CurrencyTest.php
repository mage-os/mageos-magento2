<?php
declare(strict_types=1);

/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\DataObject;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Test\Fixture\Group as StoreGroupFixture;
use Magento\Store\Test\Fixture\Store as StoreFixture;
use Magento\Store\Test\Fixture\Website as WebsiteFixture;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\Config;
use Magento\TestFramework\Fixture\DataFixtureBeforeTransaction;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for ACP2E-3658: Admin displays incorrect currency symbol when creating return.
 *
 * In a multi-website setup, the currency renderer must use the currency configured for the
 * row's store/website instead of always falling back to the system default currency.
 */
class CurrencyTest extends TestCase
{
    private const SECOND_STORE_CODE = 'fixture_second_store';

    private ObjectManager $objectManager;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * Main regression test for ACP2E-3658.
     *
     * When a grid row carries a store_id that belongs to a website configured with EUR,
     * the renderer must display prices in EUR, not in the system default (USD).
     *
     * Secondary website/store are created with DataFixtureBeforeTransaction so DB isolation can
     * remain enabled: Magento\Store\Test\Fixture\Store may perform DDL (sequences) outside the
     * test transaction, and revertible fixtures tear down website and store entities afterward.
     *
     * @return void
     */
    #[AppArea('adminhtml')]
    #[Config('currency/options/default', 'EUR', 'store', self::SECOND_STORE_CODE)]
    #[Config('currency/options/allow', 'EUR', 'store', self::SECOND_STORE_CODE)]
    #[
        DbIsolation(true),
        DataFixtureBeforeTransaction(
            WebsiteFixture::class,
            ['code' => 'test', 'name' => 'Test Website'],
            as: 'second_website'
        ),
        DataFixtureBeforeTransaction(
            StoreGroupFixture::class,
            [
                'code' => 'second_group',
                'name' => 'Second Store Group',
                'website_id' => '$second_website.id$',
            ],
            as: 'second_store_group'
        ),
        DataFixtureBeforeTransaction(
            StoreFixture::class,
            [
                'code' => self::SECOND_STORE_CODE,
                'name' => 'Fixture Second Store',
                'sort_order' => 10,
                'store_group_id' => '$second_store_group.id$',
            ],
            as: 'second_store'
        ),
    ]
    public function testRenderUsesWebsiteCurrencyWhenStoreIdProvided(): void
    {
        $store = $this->objectManager->get(StoreRepositoryInterface::class)->get(self::SECOND_STORE_CODE);

        $renderer = $this->objectManager->create(Currency::class);
        $column = $this->objectManager->create(
            Column::class,
            ['data' => ['index' => 'price', 'type' => 'currency']]
        );
        $row = new DataObject(['price' => 100.00, 'store_id' => $store->getId()]);

        $result = $renderer->setColumn($column)->render($row);

        $this->assertStringContainsString('€', $result, 'Price should display in EUR for a store with EUR currency');
        $this->assertStringNotContainsString('$', $result, 'Price must not display in USD (the default system currency)');
    }

    /**
     * An explicit currency_code on the column must take precedence over the store_id lookup.
     *
     * Verifies that the priority ordering in _getCurrencyCode() was not disturbed by the fix.
     *
     * @return void
     */
    #[AppArea('adminhtml')]
    #[
        DbIsolation(true),
        DataFixtureBeforeTransaction(
            WebsiteFixture::class,
            ['code' => 'test', 'name' => 'Test Website'],
            as: 'second_website'
        ),
        DataFixtureBeforeTransaction(
            StoreGroupFixture::class,
            [
                'code' => 'second_group',
                'name' => 'Second Store Group',
                'website_id' => '$second_website.id$',
            ],
            as: 'second_store_group'
        ),
        DataFixtureBeforeTransaction(
            StoreFixture::class,
            [
                'code' => self::SECOND_STORE_CODE,
                'name' => 'Fixture Second Store',
                'sort_order' => 10,
                'store_group_id' => '$second_store_group.id$',
            ],
            as: 'second_store'
        ),
    ]
    public function testColumnCurrencyCodeTakesPrecedenceOverStoreId(): void
    {
        $store = $this->objectManager->get(StoreRepositoryInterface::class)->get(self::SECOND_STORE_CODE);

        $renderer = $this->objectManager->create(Currency::class);
        $column = $this->objectManager->create(
            Column::class,
            ['data' => ['index' => 'price', 'type' => 'currency', 'currency_code' => 'GBP']]
        );
        $row = new DataObject(['price' => 100.00, 'store_id' => $store->getId()]);

        $result = $renderer->setColumn($column)->render($row);

        $this->assertStringContainsString('£', $result, 'Column-level currency_code must override the store currency');
    }

    /**
     * When the row carries a currency field that maps to a column index, that value is used.
     *
     * Covers the second priority branch in _getCurrencyCode(): column->getCurrency() + row data.
     *
     * @return void
     */
    #[AppArea('adminhtml')]
    public function testRowCurrencyFieldIsUsedWhenColumnCurrencyIsSet(): void
    {
        $renderer = $this->objectManager->create(Currency::class);
        $column = $this->objectManager->create(
            Column::class,
            ['data' => ['index' => 'price', 'type' => 'currency', 'currency' => 'order_currency_code']]
        );
        $row = new DataObject(['price' => 100.00, 'order_currency_code' => 'EUR']);

        $result = $renderer->setColumn($column)->render($row);

        $this->assertStringContainsString(
            '€',
            $result,
            'Currency from the row data field should be used when column->getCurrency() is set'
        );
    }

    /**
     * A non-existent store_id must not throw; the renderer falls back to the default currency.
     *
     * Covers the NoSuchEntityException catch block added in the fix.
     *
     * @return void
     */
    #[AppArea('adminhtml')]
    public function testRenderFallsBackToDefaultCurrencyForInvalidStoreId(): void
    {
        $renderer = $this->objectManager->create(Currency::class);
        $column = $this->objectManager->create(
            Column::class,
            ['data' => ['index' => 'price', 'type' => 'currency']]
        );
        $row = new DataObject(['price' => 100.00, 'store_id' => 99999]);

        $result = $renderer->setColumn($column)->render($row);

        $this->assertStringContainsString(
            '$',
            $result,
            'An invalid store_id should not throw and should fall back to the default currency'
        );
    }

    /**
     * A row with a store_id from the default (USD) website must still render in USD.
     *
     * Verifies that the fix did not alter behaviour for stores that were already correct.
     *
     * @return void
     */
    #[AppArea('adminhtml')]
    public function testRenderUsesDefaultCurrencyForDefaultWebsiteStore(): void
    {
        $defaultStore = $this->objectManager->get(StoreRepositoryInterface::class)->get('default');

        $renderer = $this->objectManager->create(Currency::class);
        $column = $this->objectManager->create(
            Column::class,
            ['data' => ['index' => 'price', 'type' => 'currency']]
        );
        $row = new DataObject(['price' => 100.00, 'store_id' => $defaultStore->getId()]);

        $result = $renderer->setColumn($column)->render($row);

        $this->assertStringContainsString('$', $result, 'Default website store should still render prices in USD');
    }

    /**
     * When no store_id is set on the row, the renderer must fall back to the system default currency.
     *
     * @return void
     */
    #[AppArea('adminhtml')]
    public function testRenderFallsBackToDefaultCurrencyWithoutStoreId(): void
    {
        $renderer = $this->objectManager->create(Currency::class);
        $column = $this->objectManager->create(
            Column::class,
            ['data' => ['index' => 'price', 'type' => 'currency']]
        );
        $row = new DataObject(['price' => 100.00]);

        $result = $renderer->setColumn($column)->render($row);

        $this->assertStringContainsString(
            '$',
            $result,
            'Price should display in USD (system default) when no store_id is set'
        );
    }
}
