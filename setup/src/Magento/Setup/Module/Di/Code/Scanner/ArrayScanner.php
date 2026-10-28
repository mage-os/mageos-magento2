<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Setup\Module\Di\Code\Scanner;

use Magento\Framework\Filesystem\SecurePathValidator;

class ArrayScanner implements ScannerInterface
{
    /**
     * @var string|null
     */
    private $rootPath;

    /**
     * @param string|null $rootPath Application root the include guard is anchored to; defaults to BP.
     */
    public function __construct(?string $rootPath = null)
    {
        $this->rootPath = $rootPath ?? (defined('BP') ? BP : null);
    }

    /**
     * Scan files
     *
     * @param array $files
     * @return array
     */
    public function collectEntities(array $files)
    {
        $output = [];
        foreach ($files as $file) {
            if (!$this->isSafeToInclude($file)) {
                continue;
            }
            // phpcs:ignore Magento2.Security.IncludeFile
            $data = include $file;
            // phpcs:ignore Magento2.Performance.ForeachArrayMerge
            $output = array_merge($output, $data);
        }
        return $output;
    }

    /**
     * Only include real .php files outside runtime-writable dirs (LFI hardening).
     *
     * @param string $file
     * @return bool
     */
    private function isSafeToInclude(string $file): bool
    {
        if ($file === '' || strpos($file, "\0") !== false) {
            return false;
        }
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $real = realpath($file);
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        if ($real === false || !is_file($real)) {
            return false;
        }
        if (strtolower(substr($real, -4)) !== '.php') {
            return false;
        }
        return !SecurePathValidator::isUnsafeIncludePathForRoot($real, $this->rootPath);
    }
}
