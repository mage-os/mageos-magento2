<?php
declare(strict_types=1);

namespace Magento\Backend\Cron;

use Magento\Backend\Model\VersionCheck\LatestVersionFetcher;

class VersionCheck
{
    public function __construct(
        private readonly LatestVersionFetcher $fetcher
    ) {
    }

    public function execute(): void
    {
        $this->fetcher->fetchAndCache();
    }
}
