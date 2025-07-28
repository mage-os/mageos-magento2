<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Csp\Plugin;

use Magento\Framework\View\Asset\File;
use Magento\RequireJs\Model\FileManager;
use Magento\Csp\Model\SubresourceIntegrityFactory;
use Magento\Csp\Model\SubresourceIntegrityCollector;
use Magento\Csp\Model\SubresourceIntegrity\HashGenerator;
use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;

/**
 * Plugin to add asset integrity value after static content deploy.
 */
class GenerateAssetIntegrity
{
    /**
     * Supported content types.
     *
     * @var array
     */
    private const CONTENT_TYPES = ["js"];

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
     * @param HashGenerator $hashGenerator
     * @param SubresourceIntegrityFactory $integrityFactory
     * @param SubresourceIntegrityCollector $integrityCollector
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        HashGenerator $hashGenerator,
        SubresourceIntegrityFactory $integrityFactory,
        SubresourceIntegrityCollector $integrityCollector,
        ?LoggerInterface $logger = null
    ) {
        $this->hashGenerator = $hashGenerator;
        $this->integrityFactory = $integrityFactory;
        $this->integrityCollector = $integrityCollector;
        $this->logger = $logger ?? ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * Generates integrity for RequireJs config.
     *
     * @param FileManager $subject
     * @param File $result
     *
     * @return File
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateRequireJsConfigAsset(
        FileManager $subject,
        File $result
    ): File {
        if (PHP_SAPI == 'cli') {
            $this->logger->info('GenerateAssetIntegrity: Called for "' . $result->getPath() . '" (PID: ' . getmypid() . ')');
            
            if (in_array($result->getContentType(), self::CONTENT_TYPES)) {
                $integrity = $this->integrityFactory->create(
                    [
                        "data" => [
                            'hash' => $this->hashGenerator->generate(
                                $result->getContent()
                            ),
                            'path' => $result->getPath()
                        ]
                    ]
                );

                $this->integrityCollector->collect($integrity);
                $this->logger->info('GenerateAssetIntegrity: Collected "' . $result->getPath() . '" (PID: ' . getmypid() . ')');
            }
        }

        return $result;
    }
}
