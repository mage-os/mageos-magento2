<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache\Frontend\Adapter\SymfonyAdapters;

/**
 * Opt-in extension of TagAdapterInterface for adapters that sweep the source tag
 * sets during deletion.
 *
 * Kept separate from TagAdapterInterface so existing third-party implementations
 * of that interface stay signature-compatible; callers instanceof-check this
 * interface before passing the source tags.
 */
interface SourceTagsAwareTagAdapterInterface extends TagAdapterInterface
{
    /**
     * Delete cache items by their IDs
     *
     * @param array $ids Array of cache IDs to delete
     * @param array $sourceTags Tags the ids were discovered from, so the adapter can
     *        sweep stale members (ids whose reverse index already expired) from those
     *        tag sets
     * @return bool True on success
     */
    public function deleteByIds(array $ids, array $sourceTags = []): bool;
}
