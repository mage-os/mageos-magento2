<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\Dashboard\Attention;

use Magento\Backend\Model\Dashboard\Attention\ItemInterface;
use Magento\Backend\Model\Dashboard\Attention\Pool;
use PHPUnit\Framework\TestCase;

class PoolTest extends TestCase
{
    public function testItemsAreSortedBySortOrder(): void
    {
        $late = $this->createMock(ItemInterface::class);
        $late->method('getSortOrder')->willReturn(100);
        $early = $this->createMock(ItemInterface::class);
        $early->method('getSortOrder')->willReturn(10);

        $pool = new Pool(['late' => $late, 'early' => $early]);

        $this->assertSame([$early, $late], $pool->getItems());
    }

    public function testRejectsForeignObjects(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('bogus');

        new Pool(['bogus' => new \stdClass()]);
    }
}
