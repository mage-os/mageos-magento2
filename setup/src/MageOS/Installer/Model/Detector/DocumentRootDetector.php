<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Detector;

/**
 * Detects document root configuration
 */
class DocumentRootDetector
{
    /**
     * Detect if pub/ is being used as document root
     *
     * @param string $baseDir
     * @return array{isPub: bool, recommendation: string}
     */
    public function detect(string $baseDir): array
    {
        $isPub = false;
        $recommendation = 'For better security, configure your web server to use /pub as document root';

        // Check if there's a .htaccess in root that redirects to pub
        $rootHtaccess = $baseDir . '/.htaccess';
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        if (file_exists($rootHtaccess)) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $content = file_get_contents($rootHtaccess);
            if ($content !== false && str_contains($content, 'RewriteRule .* pub/$0 [L]')) {
                $isPub = false;
                $recommendation = 'Detected root directory setup with pub/ redirect';
            }
        }

        // Check if pub/index.php exists (standard Magento structure)
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        if (file_exists($baseDir . '/pub/index.php')) {
            // If SCRIPT_FILENAME contains /pub/, we're likely using pub as doc root
            // phpcs:ignore Magento2.Security.Superglobal
            $scriptFile = $_SERVER['SCRIPT_FILENAME'] ?? '';
            if (str_contains($scriptFile, '/pub/')) {
                $isPub = true;
                $recommendation = 'Document root is correctly set to /pub (secure configuration)';
            }
        }

        return [
            'isPub' => $isPub,
            'recommendation' => $recommendation
        ];
    }
}
