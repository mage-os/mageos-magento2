<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Checker;

/**
 * Checks file system permissions before installation
 */
class PermissionChecker
{
    /**
     * Critical directories that need write permissions
     *
     * @var array<string>
     */
    private array $criticalPaths = [
        'var',
        'var/log',
        'var/cache',
        'var/page_cache',
        'var/session',
        'generated',
        'pub/static',
        'pub/media',
        'app/etc'
    ];

    /**
     * Check if all critical paths have write permissions
     *
     * @param string $baseDir
     * @return array{success: bool, missing: array<string>, commands: array<string>}
     */
    public function check(string $baseDir): array
    {
        $missing = [];

        foreach ($this->criticalPaths as $path) {
            $fullPath = $baseDir . '/' . $path;

            // Check if directory exists and is writable
            if (!file_exists($fullPath)) {
                // Try to create it
                if (!@mkdir($fullPath, 0775, true)) {
                    $missing[] = $path;
                }
            } elseif (!is_writable($fullPath)) {
                $missing[] = $path;
            }
        }

        $success = empty($missing);
        $commands = $this->generateFixCommands($baseDir, $missing);

        return [
            'success' => $success,
            'missing' => $missing,
            'commands' => $commands
        ];
    }

    /**
     * Generate commands to fix permissions
     *
     * @param string $baseDir
     * @param array<string> $missingPaths
     * @return array<string>
     */
    private function generateFixCommands(string $baseDir, array $missingPaths): array
    {
        if (empty($missingPaths)) {
            return [];
        }

        return [
            sprintf('cd %s', $baseDir),
            'chmod -R u+w var generated vendor pub/static pub/media app/etc',
            'chmod -R g+w var generated vendor pub/static pub/media app/etc',
            '',
            '# Or use find for more control:',
            'find var generated vendor pub/static pub/media app/etc -type f -exec chmod g+w {} +',
            'find var generated vendor pub/static pub/media app/etc -type d -exec chmod g+ws {} +'
        ];
    }
}
