<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Csp\Plugin;

use Magento\Csp\Model\SubresourceIntegrity\HashGenerator;
use Magento\Csp\Model\SubresourceIntegrityCollector;
use Magento\Csp\Model\SubresourceIntegrityFactory;
use Magento\Deploy\Service\Bundle;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;

class GenerateBundleAssetIntegrity
{
    /**
     * @var HashGenerator
     */
    private HashGenerator $hashGenerator;

    /**
     * @var SubresourceIntegrityFactory
     */
    private SubresourceIntegrityFactory $integrityFactory;

    /**
     * @var SubresourceIntegrityCollector
     */
    private SubresourceIntegrityCollector $integrityCollector;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var Files
     */
    private Files $utilityFiles;

    /**
     * @param HashGenerator $hashGenerator
     * @param SubresourceIntegrityFactory $integrityFactory
     * @param SubresourceIntegrityCollector $integrityCollector
     * @param Filesystem $filesystem
     * @param Files $utilityFiles
     */
    public function __construct(
        HashGenerator $hashGenerator,
        SubresourceIntegrityFactory $integrityFactory,
        SubresourceIntegrityCollector $integrityCollector,
        Filesystem $filesystem,
        Files $utilityFiles
    ) {
        $this->hashGenerator = $hashGenerator;
        $this->integrityFactory = $integrityFactory;
        $this->integrityCollector = $integrityCollector;
        $this->filesystem = $filesystem;
        $this->utilityFiles = $utilityFiles;
    }

    /**
     * @param Bundle $subject
     * @param string|null $result
     * @param string $area
     * @param string $theme
     * @param string $locale
     * @return void
     * @throws FileSystemException
     */
    public function afterDeploy(Bundle $subject, ?string $result, string $area, string $theme, string $locale)
    {
        if (PHP_SAPI == 'cli') {
            $pubStaticDir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
            $bundleDir = $pubStaticDir->getAbsolutePath($area . '/' . $theme . '/' . $locale) .
                "/". Bundle::BUNDLE_JS_DIR;
            $files = $this->utilityFiles->getFiles([$bundleDir], '*.js');

            foreach ($files as $file) {
                $integrity = $this->integrityFactory->create(
                    [
                        "data" => [
                            'hash' => $this->hashGenerator->generate(
                                file_get_contents($file)
                            ),
                            'path' => $area . '/' . $theme . '/' . $locale .
                                "/" . Bundle::BUNDLE_JS_DIR . '/' . basename($file)
                        ]
                    ]
                );

                $this->integrityCollector->collect($integrity);
            }
        }
    }
}
