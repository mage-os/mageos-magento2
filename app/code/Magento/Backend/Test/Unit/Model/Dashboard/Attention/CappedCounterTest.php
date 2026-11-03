<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\Dashboard\Attention;

use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CappedCounterTest extends TestCase
{
    #[DataProvider('countProvider')]
    public function testCountIsBoundedByCap(int $rows, int $cap, int $expectedCount, string $expectedDisplay): void
    {
        $connection = $this->createMock(AdapterInterface::class);
        $connection->method('fetchCol')->willReturn(array_fill(0, min($rows, $cap + 1), '1'));

        $select = $this->createMock(Select::class);
        $select->method('getConnection')->willReturn($connection);
        $select->method('reset')->willReturnSelf();
        $select->method('columns')->willReturnSelf();
        $select->method('distinct')->willReturnSelf();
        $select->expects($this->once())->method('limit')->with($cap + 1)->willReturnSelf();

        $counter = new CappedCounter();
        $count = $counter->count($select, $cap);

        $this->assertSame($expectedCount, $count);
        $this->assertSame($expectedDisplay, $counter->format($count, $cap));
    }

    public static function countProvider(): array
    {
        return [
            'none' => [0, 1000, 0, '0'],
            'under cap' => [42, 1000, 42, '42'],
            'at cap' => [1000, 1000, 1000, '1000'],
            'over cap' => [250000, 1000, 1001, '1000+'],
            'custom cap' => [7, 5, 6, '5+'],
        ];
    }

    public function testDistinctColumnReplacesLiteralColumn(): void
    {
        $connection = $this->createMock(AdapterInterface::class);
        $connection->method('fetchCol')->willReturn([]);

        $select = $this->createMock(Select::class);
        $select->method('getConnection')->willReturn($connection);
        $select->method('reset')->willReturnSelf();
        $select->method('limit')->willReturnSelf();
        $select->expects($this->once())->method('distinct')->willReturnSelf();
        $select->expects($this->once())->method('columns')->with('main_table.review_id')->willReturnSelf();

        $this->assertSame(0, (new CappedCounter())->count($select, 10, 'main_table.review_id'));
    }
}
