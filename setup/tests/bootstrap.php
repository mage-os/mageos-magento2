<?php
/**
 * PHPUnit bootstrap file for MageOS Installer tests
 */

declare(strict_types=1);

// Composer autoloader from project root
require __DIR__ . '/../../vendor/autoload.php';

// Set error reporting for tests
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set timezone to avoid warnings
date_default_timezone_set('UTC');
