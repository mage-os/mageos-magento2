<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Attribute\Backend;

use Magento\Catalog\Helper\Data;
use Magento\Catalog\Model\Product\Attribute\Backend\Url;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /**
     * @var Data|MockObject
     */
    private Data $helper;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->helper = $this->createMock(Data::class);
        parent::setUp();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSetAttribute(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->expects($this->once())
            ->method('__call')
            ->with('setIsGlobal', [ScopedAttributeInterface::SCOPE_WEBSITE]);
        $this->helper->expects($this->once())->method('isUrlScopeWebsite')->willReturn(true);

        $url = new Url($this->helper);
        $url->setAttribute($attribute);
    }
}
