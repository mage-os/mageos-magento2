<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Block\Account;

use Magento\Customer\Block\Account\Link;
use Magento\Customer\Model\Url;
use Magento\Framework\Test\Unit\Helper\LayoutTestHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Layout;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    public function testGetHref()
    {
        $objectManager = new ObjectManager($this);
        $helper = $this->createPartialMock(
            Url::class,
            ['getAccountUrl']
        );
        $layout = new LayoutTestHelper();

        $objectManager->prepareObjectManager();
        $block = $objectManager->getObject(
            Link::class,
            ['layout' => $layout, 'customerUrl' => $helper]
        );
        $helper->expects($this->any())->method('getAccountUrl')->willReturn('account url');

        $this->assertEquals('account url', $block->getHref());
    }
}
