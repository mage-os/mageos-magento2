<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Filesystem;

/**
 * Rejects include/require targets pointing at stream wrappers or runtime-writable,
 * non-code locations. Blocked directories are anchored to the application root (BP);
 * directories relocated through MAGE_DIRS are not covered.
 */
class SecurePathValidator
{
    /**
     * Runtime-writable locations relative to the application root. Nothing under them is code.
     */
    private const BLOCKED_DIRECTORIES = [
        'var/report/',
        'var/log/',
        'var/tmp/',
        'var/session/',
        'var/cache/',
        'var/page_cache/',
        'var/importexport/',
        'var/backups/',
        'pub/media/',
        'pub/static/',
    ];

    /**
     * @var array<string, string[]>
     */
    private static $prefixCache = [];

    /**
     * Whether a path must not be include()d / require()d, anchored to the application root.
     *
     * @param string $path
     * @return bool
     */
    public static function isUnsafeIncludePath(string $path): bool
    {
        return self::isUnsafeIncludePathForRoot($path, defined('BP') ? BP : null);
    }

    /**
     * Whether a path must not be include()d / require()d, anchored to the given root.
     *
     * With a null root the blocked directories are matched anywhere in the path.
     *
     * @param string $path
     * @param string|null $root
     * @return bool
     */
    public static function isUnsafeIncludePathForRoot(string $path, ?string $root): bool
    {
        if ($path === '' || strpos($path, "\0") !== false || strpos($path, '://') !== false) {
            return true;
        }

        $candidates = [self::normalize($path, $root)];
        // Resolve symlinks and parent references only when present: keeps realpath() off the
        // common template-render path.
        if (strpos($path, '..') !== false) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $real = realpath($path);
            if ($real !== false) {
                $candidates[] = self::normalize($real, $root);
            }
        }

        $anchored = $root !== null;
        foreach (self::blockedPrefixes($root) as $prefix) {
            foreach ($candidates as $candidate) {
                $position = strpos($candidate, $prefix);
                if ($position !== false && (!$anchored || $position === 0)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Lower-case, forward-slash form with repeated separators and "." segments collapsed.
     *
     * A relative path is resolved against the root when one is given.
     *
     * @param string $path
     * @param string|null $root
     * @return string
     */
    private static function normalize(string $path, ?string $root = null): string
    {
        $path = str_replace('\\', '/', $path);
        if ($root !== null && !preg_match('#^(?:[a-zA-Z]:)?/#', $path)) {
            $path = str_replace('\\', '/', $root) . '/' . $path;
        }
        $path = strtolower((string)preg_replace('#/+#', '/', $path));
        while (strpos($path, '/./') !== false) {
            $path = str_replace('/./', '/', $path);
        }

        return $path;
    }

    /**
     * Blocked directory prefixes for a root: both its literal and resolved forms when they differ.
     *
     * @param string|null $root
     * @return string[]
     */
    private static function blockedPrefixes(?string $root): array
    {
        $cacheKey = $root ?? '';
        if (isset(self::$prefixCache[$cacheKey])) {
            return self::$prefixCache[$cacheKey];
        }

        $roots = [''];
        if ($root !== null) {
            $roots = [rtrim(self::normalize($root), '/')];
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $realRoot = realpath($root);
            if ($realRoot !== false) {
                $roots[] = rtrim(self::normalize($realRoot), '/');
            }
            $roots = array_unique($roots);
        }

        $prefixes = [];
        foreach ($roots as $normalizedRoot) {
            foreach (self::BLOCKED_DIRECTORIES as $directory) {
                $prefixes[] = $normalizedRoot . '/' . $directory;
            }
        }

        return self::$prefixCache[$cacheKey] = $prefixes;
    }
}
