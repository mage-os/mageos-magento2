<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */

namespace Magento\Framework\Filesystem;

use Laminas\Stdlib\Glob as LaminasGlob;
use Laminas\Stdlib\Exception\RuntimeException as LaminasRuntimeException;

/**
 * Wrapper for Laminas\Stdlib\Glob
 */
class Glob extends LaminasGlob
{
    /**
     * @var array
     */
    private static $cache = [];

    /**
     * Find path names matching a pattern.
     *
     * @param string $pattern
     * @param int $flags
     * @param bool $forceFallback
     * @return array
     */
    public static function glob($pattern, $flags = 0, $forceFallback = false)
    {
        $key = $pattern . '|' . $flags . '|' . ($forceFallback ? 1 : 0);
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        try {
            $result = LaminasGlob::glob($pattern, $flags, $forceFallback);
        } catch (LaminasRuntimeException $e) {
            $result = [];
        }
        self::$cache[$key] = $result;
        return $result;
    }
}
