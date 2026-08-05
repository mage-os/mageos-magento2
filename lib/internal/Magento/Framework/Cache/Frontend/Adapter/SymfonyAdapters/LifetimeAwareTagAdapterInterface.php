<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache\Frontend\Adapter\SymfonyAdapters;

/**
 * Opt-in extension of TagAdapterInterface for adapters that bound their indices
 * to the saved entry's lifetime.
 *
 * Kept separate from TagAdapterInterface so existing third-party implementations
 * of that interface stay signature-compatible; callers instanceof-check this
 * interface before passing the lifetime.
 */
interface LifetimeAwareTagAdapterInterface extends TagAdapterInterface
{
    /**
     * Update tag-to-ID index when a cache item is saved
     *
     * @param string $id Cache ID
     * @param array $tags Tags associated with this ID
     * @param int|null $lifetime Effective data-key lifetime in seconds, so the adapter
     *        can bound its indices to the data key's lifetime
     * @return void
     */
    public function onSave(string $id, array $tags, ?int $lifetime = null): void;
}
