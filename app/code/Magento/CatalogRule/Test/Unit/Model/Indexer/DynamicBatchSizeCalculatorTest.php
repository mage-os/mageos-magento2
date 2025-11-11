<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Model\Indexer;

use Magento\CatalogRule\Model\Indexer\DynamicBatchSizeCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for DynamicBatchSizeCalculator
 */
class DynamicBatchSizeCalculatorTest extends TestCase
{
    /**
     * @var DynamicBatchSizeCalculator
     */
    private $calculator;

    /**
     * @var string
     */
    private $originalMemoryLimit;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->calculator = new DynamicBatchSizeCalculator();
        $this->originalMemoryLimit = ini_get('memory_limit');
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        ini_set('memory_limit', $this->originalMemoryLimit);
    }

    /**
     * Test getAttributeBatchSize with standard memory limit
     *
     * @return void
     */
    public function testGetAttributeBatchSizeWithStandardMemoryLimit(): void
    {
        ini_set('memory_limit', '512M');

        $batchSize = $this->calculator->getAttributeBatchSize();

        $this->assertIsInt($batchSize);
        $this->assertGreaterThanOrEqual(500, $batchSize);
        $this->assertLessThanOrEqual(50000, $batchSize);
    }

    /**
     * Test getAttributeBatchSize with high memory limit
     *
     * @return void
     */
    public function testGetAttributeBatchSizeWithHighMemoryLimit(): void
    {
        ini_set('memory_limit', '4G');

        $batchSize = $this->calculator->getAttributeBatchSize();

        $this->assertIsInt($batchSize);
        $this->assertGreaterThanOrEqual(500, $batchSize);
        $this->assertLessThanOrEqual(50000, $batchSize);
    }

    /**
     * Test getAttributeBatchSize with low memory limit
     *
     * @return void
     */
    public function testGetAttributeBatchSizeWithLowMemoryLimit(): void
    {
        ini_set('memory_limit', '128M');

        $batchSize = $this->calculator->getAttributeBatchSize();

        $this->assertIsInt($batchSize);
        $this->assertGreaterThanOrEqual(500, $batchSize);
        $this->assertLessThanOrEqual(50000, $batchSize);
    }

    /**
     * Test getAttributeBatchSize with unlimited memory
     *
     * @return void
     */
    public function testGetAttributeBatchSizeWithUnlimitedMemory(): void
    {
        ini_set('memory_limit', '-1');

        $batchSize = $this->calculator->getAttributeBatchSize();

        $this->assertIsInt($batchSize);
        $this->assertGreaterThanOrEqual(500, $batchSize);
        $this->assertLessThanOrEqual(50000, $batchSize);
    }

    /**
     * Test getAttributeBatchSize returns consistent results on multiple calls
     *
     * @return void
     */
    public function testGetAttributeBatchSizeIsCached(): void
    {
        ini_set('memory_limit', '1G');

        $batchSize1 = $this->calculator->getAttributeBatchSize();
        $batchSize2 = $this->calculator->getAttributeBatchSize();

        $this->assertEquals($batchSize1, $batchSize2);
    }

    /**
     * Test getMaxBatchesInMemory with standard memory limit
     *
     * @return void
     */
    public function testGetMaxBatchesInMemoryWithStandardMemoryLimit(): void
    {
        ini_set('memory_limit', '512M');

        $maxBatches = $this->calculator->getMaxBatchesInMemory();

        $this->assertIsInt($maxBatches);
        $this->assertGreaterThanOrEqual(2, $maxBatches);
        $this->assertLessThanOrEqual(100, $maxBatches);
    }

    /**
     * Test getMaxBatchesInMemory with high memory limit
     *
     * @return void
     */
    public function testGetMaxBatchesInMemoryWithHighMemoryLimit(): void
    {
        ini_set('memory_limit', '4G');

        $maxBatches = $this->calculator->getMaxBatchesInMemory();

        $this->assertIsInt($maxBatches);
        $this->assertGreaterThanOrEqual(2, $maxBatches);
        $this->assertLessThanOrEqual(100, $maxBatches);
    }

    /**
     * Test getMaxBatchesInMemory with low memory limit
     *
     * @return void
     */
    public function testGetMaxBatchesInMemoryWithLowMemoryLimit(): void
    {
        ini_set('memory_limit', '128M');

        $maxBatches = $this->calculator->getMaxBatchesInMemory();

        $this->assertIsInt($maxBatches);
        $this->assertGreaterThanOrEqual(2, $maxBatches);
        $this->assertLessThanOrEqual(100, $maxBatches);
    }

    /**
     * Test getMaxBatchesInMemory with unlimited memory
     *
     * @return void
     */
    public function testGetMaxBatchesInMemoryWithUnlimitedMemory(): void
    {
        ini_set('memory_limit', '-1');

        $maxBatches = $this->calculator->getMaxBatchesInMemory();

        $this->assertIsInt($maxBatches);
        $this->assertGreaterThanOrEqual(2, $maxBatches);
        $this->assertLessThanOrEqual(100, $maxBatches);
    }

    /**
     * Test getMaxBatchesInMemory returns consistent results on multiple calls
     *
     * @return void
     */
    public function testGetMaxBatchesInMemoryIsCached(): void
    {
        ini_set('memory_limit', '1G');

        $maxBatches1 = $this->calculator->getMaxBatchesInMemory();
        $maxBatches2 = $this->calculator->getMaxBatchesInMemory();

        $this->assertEquals($maxBatches1, $maxBatches2);
    }

    /**
     * Test that batch size respects minimum bound
     *
     * @return void
     */
    public function testAttributeBatchSizeRespectsMinimumBound(): void
    {
        // Set very low memory to test minimum bound
        ini_set('memory_limit', '64M');

        $batchSize = $this->calculator->getAttributeBatchSize();

        $this->assertGreaterThanOrEqual(500, $batchSize);
    }

    /**
     * Test that batch size respects maximum bound
     *
     * @return void
     */
    public function testAttributeBatchSizeRespectsMaximumBound(): void
    {
        // Set very high memory to test maximum bound
        ini_set('memory_limit', '16G');

        $batchSize = $this->calculator->getAttributeBatchSize();

        $this->assertLessThanOrEqual(50000, $batchSize);
    }

    /**
     * Test memory limit parsing with different notations
     *
     * @dataProvider memoryLimitNotationProvider
     * @param string $memoryLimit
     * @return void
     */
    public function testMemoryLimitParsing(string $memoryLimit): void
    {
        ini_set('memory_limit', $memoryLimit);

        $batchSize = $this->calculator->getAttributeBatchSize();

        $this->assertIsInt($batchSize);
        $this->assertGreaterThan(0, $batchSize);
    }

    /**
     * Data provider for memory limit notation tests
     *
     * @return array
     */
    public function memoryLimitNotationProvider(): array
    {
        return [
            'gigabytes' => ['2G'],
            'megabytes' => ['512M'],
            'kilobytes' => ['524288K'],
            'bytes' => ['536870912'],
            'unlimited' => ['-1'],
        ];
    }

    /**
     * Test that max batches respects minimum bound
     *
     * @return void
     */
    public function testMaxBatchesRespectsMinimumBound(): void
    {
        ini_set('memory_limit', '64M');

        $maxBatches = $this->calculator->getMaxBatchesInMemory();

        $this->assertGreaterThanOrEqual(2, $maxBatches);
    }

    /**
     * Test that max batches respects maximum bound
     *
     * @return void
     */
    public function testMaxBatchesRespectsMaximumBound(): void
    {
        ini_set('memory_limit', '16G');

        $maxBatches = $this->calculator->getMaxBatchesInMemory();

        $this->assertLessThanOrEqual(100, $maxBatches);
    }

    /**
     * Test calculator behavior with various memory configurations
     *
     * @dataProvider memoryConfigurationProvider
     * @param string $memoryLimit
     * @param int $minExpectedBatchSize
     * @param int $maxExpectedBatchSize
     * @return void
     */
    public function testCalculatorBehaviorWithVariousMemoryConfigurations(
        string $memoryLimit,
        int $minExpectedBatchSize,
        int $maxExpectedBatchSize
    ): void {
        ini_set('memory_limit', $memoryLimit);

        $calculator = new DynamicBatchSizeCalculator();
        $batchSize = $calculator->getAttributeBatchSize();
        $maxBatches = $calculator->getMaxBatchesInMemory();

        $this->assertGreaterThanOrEqual($minExpectedBatchSize, $batchSize);
        $this->assertLessThanOrEqual($maxExpectedBatchSize, $batchSize);
        $this->assertIsInt($maxBatches);
    }

    /**
     * Data provider for memory configuration tests
     *
     * @return array
     */
    public function memoryConfigurationProvider(): array
    {
        return [
            'low_memory' => ['128M', 500, 50000],
            'medium_memory' => ['512M', 500, 50000],
            'high_memory' => ['2G', 500, 50000],
            'very_high_memory' => ['8G', 500, 50000],
        ];
    }
}
