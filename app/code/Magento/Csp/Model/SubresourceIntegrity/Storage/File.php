<?php
/************************************************************************
 *
 * Copyright 2024 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 * ************************************************************************
 */
declare(strict_types=1);

namespace Magento\Csp\Model\SubresourceIntegrity\Storage;

use Magento\Deploy\Package\Package;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;

/**
 * Persistence of sri hashes in the local file system
 */
class File
{
    /**
     * Constant for sri hashes filename
     */
    private const FILENAME = 'sri-hashes.json';

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * Load data from filesystem
     *
     * @param string|null $area
     * @return string|bool
     * @throws FileSystemException
     */
    public function load(?string $area = null): string|bool
    {
        $staticDir = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);

        if ($area) {
            $path = $area . DIRECTORY_SEPARATOR . self::FILENAME;
            if ($staticDir->isFile($path)) {
                return $staticDir->readFile($path);
            }
        }
        return false;
    }

    /**
     * Save File to Local Storage by area
     *
     * @param string $data
     * @param string|null $area
     * @return bool
     * @throws FileSystemException
     */
    public function save(string $data, ?string $area = null): bool
    {
        $staticDir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);

        if ($area) {
            $path = $area . DIRECTORY_SEPARATOR . self::FILENAME;
            return (bool)$staticDir->writeFile($path, $data, 'w');
        }
        return false;
    }

    /**
     * Delete all Sri Hashes files
     *
     * @throws FileSystemException
     */
    public function remove():bool
    {
        $staticDir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);

        //delete all json files from all areas
        foreach ([Package::BASE_AREA, Area::AREA_FRONTEND, Area::AREA_ADMINHTML] as $area) {
            $path = $area . DIRECTORY_SEPARATOR . self::FILENAME;
            if ($staticDir->isFile($path)) {
               $staticDir->delete($path);
            }
        }
        return true;
    }
}
