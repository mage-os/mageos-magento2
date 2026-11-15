<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Block\Adminhtml\Order\Create;

use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\Sidebar\Wishlist;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Checks that Magento_Wishlist adds the wish list section to the admin order create sidebar
 *
 * @magentoAppArea adminhtml
 */
class SidebarLayoutTest extends TestCase
{
    /**
     * The wish list section sits between the shopping cart and the last ordered items
     *
     * @return void
     */
    public function testWishlistSectionFollowsCart(): void
    {
        $layout = $this->createLayout('sales_order_create_load_block_sidebar');
        $children = $layout->getChildNames('sidebar');
        $cartPosition = array_search('cart', $children, true);

        $this->assertInstanceOf(Wishlist::class, $layout->getBlock('wishlist'));
        $this->assertNotFalse($cartPosition);
        $this->assertSame('wishlist', $children[$cartPosition + 1] ?? null);
        $this->assertSame('reorder', $children[$cartPosition + 2] ?? null);
    }

    /**
     * Build the layout for a single handle
     *
     * @param string $handle
     * @return LayoutInterface
     */
    private function createLayout(string $handle): LayoutInterface
    {
        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->create(LayoutInterface::class);
        $layout->getUpdate()->addHandle($handle);
        $layout->getUpdate()->load();
        $layout->generateXml();
        $layout->generateElements();

        return $layout;
    }
}
