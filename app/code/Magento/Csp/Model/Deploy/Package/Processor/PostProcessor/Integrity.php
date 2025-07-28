<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Csp\Model\Deploy\Package\Processor\PostProcessor;

use Magento\Framework\Filesystem;
use Magento\Deploy\Package\Package;
use Magento\Csp\Model\SubresourceIntegrityFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Csp\Model\SubresourceIntegrityCollector;
use Magento\Deploy\Package\Processor\ProcessorInterface;
use Magento\Csp\Model\SubresourceIntegrity\HashGenerator;
use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;

/**
 * Post-processor that generates integrity hashes after static content package deployed.
 */
class Integrity implements ProcessorInterface
{
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Filesystem $filesystem
     * @param HashGenerator $hashGenerator
     * @param SubresourceIntegrityFactory $integrityFactory
     * @param SubresourceIntegrityCollector $integrityCollector
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Filesystem $filesystem,
        HashGenerator $hashGenerator,
        SubresourceIntegrityFactory $integrityFactory,
        SubresourceIntegrityCollector $integrityCollector,
        ?LoggerInterface $logger = null
    ) {
        $this->filesystem = $filesystem;
        $this->hashGenerator = $hashGenerator;
        $this->integrityFactory = $integrityFactory;
        $this->integrityCollector = $integrityCollector;
        $this->logger = $logger ?? ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function process(Package $package, array $options): bool
    {
        $this->logger->info('Integrity PostProcessor: Starting package "' . $package->getPath() . '" (PID: ' . getmypid() . ')');
        
        $staticDir = $this->filesystem->getDirectoryRead(
            DirectoryList::ROOT
        );

        $jsFiles = 0;
        foreach ($package->getFiles() as $file) {
            if ($file->getExtension() == "js") {
                $jsFiles++;
                $integrity = $this->integrityFactory->create(
                    [
                        "data" => [
                            'hash' => $this->hashGenerator->generate(
                                $staticDir->readFile($file->getSourcePath())
                            ),
                            'path' => $file->getDeployedFilePath()
                        ]
                    ]
                );

                $this->integrityCollector->collect($integrity);
                $this->logger->info('Integrity PostProcessor: Collected "' . $file->getDeployedFilePath() . '" (PID: ' . getmypid() . ')');
            }
        }

        $this->logger->info('Integrity PostProcessor: Completed package "' . $package->getPath() . '" - ' . $jsFiles . ' JS files processed (PID: ' . getmypid() . ')');
        return true;
    }
}
