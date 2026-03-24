<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ImportExport\Model\Export;

/**
 * Export file naming and filtering rules.
 */
class FileInfo
{
    /**
     * Suffix used for in-progress export files.
     */
    public const IN_PROGRESS_FILE_SUFFIX = '.tmp';

    /**
     * @var ConfigInterface
     */
    private $exportConfig;

    /**
     * @param ConfigInterface $exportConfig
     */
    public function __construct(ConfigInterface $exportConfig)
    {
        $this->exportConfig = $exportConfig;
    }

    /**
     * Get temporary export file path for queue-based exports.
     *
     * @param string $fileName
     * @return string
     */
    public function getInProgressFilePath(string $fileName): string
    {
        return 'export/' . $fileName . self::IN_PROGRESS_FILE_SUFFIX;
    }

    /**
     * Check whether file is an in-progress export artifact.
     *
     * @param string $filePath
     * @return bool
     */
    public function isInProgressFile(string $filePath): bool
    {
        return str_ends_with($filePath, self::IN_PROGRESS_FILE_SUFFIX);
    }

    /**
     * Check whether filename is a final export file.
     *
     * @param string $fileName
     * @return bool
     */
    public function isExportFile(string $fileName): bool
    {
        return isset($this->exportConfig->getFileFormats()[pathinfo($fileName, PATHINFO_EXTENSION)]);
    }
}
