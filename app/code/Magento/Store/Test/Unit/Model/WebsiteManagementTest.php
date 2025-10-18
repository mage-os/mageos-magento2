<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Model;

use Magento\Store\Model\ResourceModel\Website\Collection;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory;
use Magento\Store\Model\WebsiteManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WebsiteManagementTest extends TestCase
{
    /**
     * @var WebsiteManagement
     */
    protected $model;

    /**
     * @var CollectionFactory|MockObject
     */
    protected $websitesFactoryMock;

    protected function setUp(): void
    {
        $this->websitesFactoryMock = $this->createPartialMock(
            CollectionFactory::class,
            ['create']
        );
        $this->model = new WebsiteManagement(
            $this->websitesFactoryMock
        );
    }

    public function testGetCount()
    {
        $websitesMock = $this->createMock(Collection::class);

        $this->websitesFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($websitesMock);
        $websitesMock
            ->expects($this->once())
            ->method('getSize')
            ->willReturn('expected');

        $this->assertEquals(
            'expected',
            $this->model->getCount()
        );
    }
}
