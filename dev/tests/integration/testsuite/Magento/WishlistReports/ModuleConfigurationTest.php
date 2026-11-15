<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\WishlistReports;

use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ConfigInterface as EventConfig;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\WishlistReports\Observer\WishlistAddProductObserver;
use Magento\WishlistReports\Observer\WishlistShareObserver;
use PHPUnit\Framework\TestCase;

/**
 * Checks that Magento_WishlistReports wires the wish list events into the Reports event log
 */
class ModuleConfigurationTest extends TestCase
{
    /**
     * The wish list events are observed on the storefront
     *
     * @magentoAppArea frontend
     * @return void
     */
    public function testWishlistEventsAreObserved(): void
    {
        $eventConfig = Bootstrap::getObjectManager()->get(EventConfig::class);

        $this->assertSame(
            WishlistAddProductObserver::class,
            $eventConfig->getObservers('wishlist_add_product')['reports']['instance'] ?? null
        );
        $this->assertSame(
            WishlistShareObserver::class,
            $eventConfig->getObservers('wishlist_share')['reports']['instance'] ?? null
        );
    }

    /**
     * Both wish list reports are enabled by default
     *
     * @return void
     */
    public function testWishlistReportsAreEnabledByDefault(): void
    {
        $scopeConfig = Bootstrap::getObjectManager()->get(ScopeConfigInterface::class);

        $this->assertTrue($scopeConfig->isSetFlag('reports/options/product_to_wishlist_enabled'));
        $this->assertTrue($scopeConfig->isSetFlag('reports/options/wishlist_share_enabled'));
    }

    /**
     * The settings for both wish list reports are shown with the other Reports options
     *
     * @magentoAppArea adminhtml
     * @return void
     */
    public function testWishlistReportSettingsAreInReportsOptions(): void
    {
        $fieldPaths = Bootstrap::getObjectManager()->get(Structure::class)->getFieldPaths();

        $this->assertArrayHasKey('reports/options/product_to_wishlist_enabled', $fieldPaths);
        $this->assertArrayHasKey('reports/options/wishlist_share_enabled', $fieldPaths);
    }
}
