<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Router script for PHP built-in web server
 *
 * This script handles routing for the `bin/magento dev:serve` command.
 * It serves static files directly and routes dynamic requests through Magento.
 *
 * Usage: php -S localhost:8000 -t pub/ dev/router.php
 */

// Get the requested URI
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove query string for file existence check
$filePath = __DIR__ . '/pub' . $requestPath;

// Check if requesting a static file that exists
if ($requestPath !== '/' && file_exists($filePath)) {
    // Let PHP built-in server serve the file
    return false;
}

// Check if it's a directory with an index file
if (is_dir($filePath)) {
    $indexFile = rtrim($filePath, '/') . '/index.php';
    if (file_exists($indexFile)) {
        $filePath = $indexFile;
        // Let server handle it
        return false;
    }
}

// Route through Magento's index.php for dynamic content
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/pub/index.php';

// Ensure DOCUMENT_ROOT points to pub/
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/pub';

// Include Magento's entry point
require __DIR__ . '/pub/index.php';
