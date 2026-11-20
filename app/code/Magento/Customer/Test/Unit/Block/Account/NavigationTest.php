<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Block\Account;

use Magento\Customer\Block\Account\Link as CustomerAccountLink;
use Magento\Customer\Block\Account\Navigation;
use Magento\Customer\Block\Account\SortLink;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NavigationTest extends TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var Navigation
     */
    private $navigation;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var LayoutInterface|MockObject
     */
    private $layoutMock;

    /**
     * Setup environment for test
     */
    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->layoutMock = $this->createMock(LayoutInterface::class);
        $this->contextMock->expects($this->any())
            ->method('getLayout')
            ->willReturn($this->layoutMock);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->navigation = $this->objectManagerHelper->getObject(
            Navigation::class,
            [
                'context' => $this->contextMock
            ]
        );
    }

    /**
     * Test get links sorts a customer account link and a sort link by sort order
     *
     * @return void
     */
    public function testGetLinksWithCustomerAccountLinkAndSortLink()
    {
        $sortLinkMock = $this->getMockBuilder(SortLink::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSortOrder'])
            ->getMock();

        $customerAccountLinkMock = $this->getMockBuilder(CustomerAccountLink::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSortOrder'])
            ->getMock();

        $sortLinkMock->expects($this->any())
            ->method('getSortOrder')
            ->willReturn(100);

        $customerAccountLinkMock->expects($this->any())
            ->method('getSortOrder')
            ->willReturn(20);

        $nameInLayout = 'top.links';

        $blockChildren = [
            'sortLink' => $sortLinkMock,
            'customerAccountLink' => $customerAccountLinkMock
        ];

        $this->navigation->setNameInLayout($nameInLayout);
        $this->layoutMock->expects($this->any())
            ->method('getChildBlocks')
            ->with($nameInLayout)
            ->willReturn($blockChildren);

        /* Assertion */
        $this->assertEquals(
            [
                0 => $sortLinkMock,
                1 => $customerAccountLinkMock
            ],
            $this->navigation->getLinks()
        );
    }
}
