<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Cron;

use Magento\Backend\Cron\VersionCheck;
use Magento\Backend\Model\VersionCheck\LatestVersionFetcher;
use PHPUnit\Framework\TestCase;

class VersionCheckTest extends TestCase
{
    public function testExecuteDelegatesToFetchAndCache(): void
    {
        $fetcher = $this->createMock(LatestVersionFetcher::class);
        $fetcher->expects($this->once())->method('fetchAndCache');

        $cron = new VersionCheck($fetcher);
        $cron->execute();
    }
}
