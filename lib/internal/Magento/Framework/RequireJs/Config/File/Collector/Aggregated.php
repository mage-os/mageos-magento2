<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */

namespace Magento\Framework\RequireJs\Config\File\Collector;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\RequireJs\Config as RequireJsConfig;
use Magento\Framework\View\Asset\Minification;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\File\Factory;

/**
 * Source of RequireJs config files basing on list of directories they may be located in
 */
class Aggregated implements CollectorInterface
{
    /**
     * Base files
     *
     * @var \Magento\Framework\View\File\CollectorInterface
     */
    protected $baseFiles;

    /**
     * Theme files
     *
     * @var \Magento\Framework\View\File\CollectorInterface
     */
    protected $themeFiles;

    /**
     * Theme modular files
     *
     * @var \Magento\Framework\View\File\CollectorInterface
     */
    protected $themeModularFiles;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $libDirectory;

    /**
     * @var Factory
     */
    protected $fileFactory;

    /**
     * @var Minification
     */
    private Minification $minification;

    /**
     * @param Filesystem $filesystem
     * @param Factory $fileFactory
     * @param CollectorInterface $baseFiles
     * @param CollectorInterface $themeFiles
     * @param CollectorInterface $themeModularFiles
     * @param Minification $minification
     */
    public function __construct(
        Filesystem         $filesystem,
        Factory            $fileFactory,
        CollectorInterface $baseFiles,
        CollectorInterface $themeFiles,
        CollectorInterface $themeModularFiles,
        Minification       $minification
    ) {
        $this->libDirectory = $filesystem->getDirectoryRead(DirectoryList::LIB_WEB);
        $this->fileFactory = $fileFactory;
        $this->baseFiles = $baseFiles;
        $this->themeFiles = $themeFiles;
        $this->themeModularFiles = $themeModularFiles;
        $this->minification = $minification;
    }

    /**
     * Get layout files from modules, theme with ancestors and library
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @throws \InvalidArgumentException
     * @return \Magento\Framework\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath)
    {
        if (empty($filePath)) {
            throw new \InvalidArgumentException('File path must be specified');
        }
        $files = [];
        if ($this->libDirectory->isExist($filePath)) {
            $filename = $this->libDirectory->getAbsolutePath($filePath);
            $files[] = $this->fileFactory->create($filename);
        }

        $files = array_merge($files, $this->baseFiles->getFiles($theme, $filePath));

        foreach ($theme->getInheritedThemes() as $currentTheme) {
            $files = array_merge($files, $this->themeModularFiles->getFiles($currentTheme, $filePath));
            $files = array_merge($files, $this->themeFiles->getFiles($currentTheme, $filePath));
        }
        //return $files;
        return $this->adjustMinification($theme, $files, $filePath);
    }

    /**
     * @param ThemeInterface $theme
     * @param array $files
     * @param string $filePath
     * @return array
     */
    private function adjustMinification(ThemeInterface $theme, array $files, string $filePath): array
    {
        if ($this->minification->isEnabled('js') && $filePath === RequireJsConfig::CONFIG_FILE_NAME) {
            $minifiedConfigurations = $this->baseFiles->getFiles($theme, RequireJsConfig::CONFIG_FILE_NAME_MIN);
            foreach ($theme->getInheritedThemes() as $currentTheme) {
                $minifiedConfigurations = array_merge(
                    $minifiedConfigurations,
                    $this->themeModularFiles->getFiles($currentTheme, RequireJsConfig::CONFIG_FILE_NAME_MIN)
                );
                $minifiedConfigurations = array_merge(
                    $minifiedConfigurations,
                    $this->themeFiles->getFiles($currentTheme, RequireJsConfig::CONFIG_FILE_NAME_MIN)
                );
            }
            if (!empty($minifiedConfigurations)) {
                /* @var \Magento\Framework\View\File $file */
                foreach ($files as $key => $file) {
                    foreach ($minifiedConfigurations as $minifiedConfiguration) {
                        $replacedFilename = str_replace(
                            RequireJsConfig::CONFIG_FILE_NAME_MIN,
                            RequireJsConfig::CONFIG_FILE_NAME,
                            $minifiedConfiguration->getFilename()
                        );
                        if ($file->getFilename() === $replacedFilename) {
                            $files[$key] = $minifiedConfiguration;
                        }
                    }
                }
            }
        }

        return $files;
    }
}
