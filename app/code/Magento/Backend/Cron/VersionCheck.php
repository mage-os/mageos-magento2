<?php
declare(strict_types=1);

namespace Magento\Backend\Cron;

use Magento\Backend\Model\VersionCheck\LatestVersionFetcher;

/**
 * Cron job to fetch and cache the latest distribution version.
 */
class VersionCheck
{
    /**
     * @param LatestVersionFetcher $fetcher
     */
    public function __construct(
        private readonly LatestVersionFetcher $fetcher
    ) {
    }

    /**
     * Execute the version check cron job
     *
     * @return void
     */
    public function execute(): void
    {
        $this->fetcher->fetchAndCache();
    }
}
