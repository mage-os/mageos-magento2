<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Console\Command;

use Magento\Backend\Console\Command\CacheStatusCommand;
use Symfony\Component\Console\Tester\CommandTester;

class CacheStatusCommandTest extends AbstractCacheCommandTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new CacheStatusCommand($this->cacheManagerMock);
    }

    public function testExecute()
    {
        $cacheTypes = ['A' => 0, 'B' => 1, 'C' => 1];
        $this->cacheManagerMock->expects($this->once())->method('getStatus')->willReturn($cacheTypes);
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        $this->assertEquals($this->getExpectedExecutionOutput($cacheTypes), $commandTester->getDisplay());
    }

    /**
     * {@inheritdoc}
     */
    public static function getExpectedExecutionOutput(array $types)
    {
        $output = 'Current status:' . PHP_EOL;
        foreach ($types as $type => $status) {
            $output .= sprintf('%30s: %d', $type, $status) . PHP_EOL;
        }
        return $output;
    }
}
