<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Console\Command;

use Magento\Backend\Console\Command\CacheDisableCommand;
use Symfony\Component\Console\Tester\CommandTester;

class CacheDisableCommandTest extends AbstractCacheSetCommandTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new CacheDisableCommand($this->cacheManagerMock);
    }

    /**
     * @param array $param
     * @param array $enable
     * @param array $result
     * @param string $output
     * @dataProvider executeDataProvider
     */
    public function testExecute($param, $enable, $result, $output)
    {
        $this->cacheManagerMock->expects($this->once())
            ->method('getAvailableTypes')
            ->willReturn(['A', 'B', 'C']);
        $this->cacheManagerMock->expects($this->once())
            ->method('setEnabled')
            ->with($enable, false)
            ->willReturn($result);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute($param);

        $this->assertEquals($output, $commandTester->getDisplay());
    }

    /**
     * {@inheritdoc}
     */
    public static function getExpectedExecutionOutput(array $changes)
    {
        return static::getExpectedChangeOutput($changes, false);
    }
}
