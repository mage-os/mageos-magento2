<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Csp\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Csp\Model\SubresourceIntegrity\Storage\File;

/**
 * Class contains methods equivalent to repository design to manage SRI hashes in cache.
 */
class SubresourceIntegrityRepository
{

    /**
     * @var array|null
     */
    private ?array $data = null;

    /**
     * @var string|null
     */
    private ?string $context;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var SubresourceIntegrityFactory
     */
    private SubresourceIntegrityFactory $integrityFactory;

    private File $sriStorage;

    /**
     * @param SerializerInterface $serializer
     * @param SubresourceIntegrityFactory $integrityFactory
     * @param string|null $context
     * @param File|null $sriStorage
     */
    public function __construct(
        SerializerInterface $serializer,
        SubresourceIntegrityFactory $integrityFactory,
        ?string $context = null,
        ? File $sriStorage = null
    ) {
        $this->serializer = $serializer;
        $this->integrityFactory = $integrityFactory;
        $this->context = $context;
        $this->sriStorage = $sriStorage ?? ObjectManager::getInstance()->get(File::class);
    }

    /**
     * Gets an Integrity object by path.
     *
     * @param string $path
     *
     * @return SubresourceIntegrity|null
     */
    public function getByPath(string $path): ?SubresourceIntegrity
    {
        $data = $this->getData();

        if (isset($data[$path])) {
            return $this->integrityFactory->create(
                [
                    "data" => [
                        "path" => $path,
                        "hash" => $data[$path]
                    ]
                ]
            );
        }

        return null;
    }

    /**
     * Gets all available Integrity objects.
     *
     * @return SubresourceIntegrity[]
     * @throws FileSystemException
     */
    public function getAll(): array
    {
        $result = [];

        foreach ($this->getData() as $path => $hash) {
            $result[] = $this->integrityFactory->create(
                [
                    "data" => [
                        "path" => $path,
                        "hash" => $hash
                    ]
                ]
            );
        }

        return $result;
    }

    /**
     * Saves Integrity object.
     *
     * @param SubresourceIntegrity $integrity
     *
     * @return bool
     * @throws FileSystemException
     */
    public function save(SubresourceIntegrity $integrity): bool
    {
        $data = $this->getData();

        $data[$integrity->getPath()] = $integrity->getHash();

        $this->data = $data;

        return $this->sriStorage->save(
            $this->serializer->serialize($this->data),
            $this->context
        );
    }

    /**
     * Saves a bunch of Integrity objects.
     *
     * @param SubresourceIntegrity[] $bunch
     *
     * @return bool
     */
    public function saveBunch(array $bunch): bool
    {
        $data = $this->getData();

        foreach ($bunch as $integrity) {
            $data[$integrity->getPath()] = $integrity->getHash();
        }

        $this->data = $data;

        return $this->sriStorage->save(
            $this->serializer->serialize($this->data),
           $this->context
        );
    }

    /**
     * Deletes all Integrity objects.
     *
     * @return bool
     * @throws FileSystemException
     */
    public function deleteAll(): bool
    {
        $this->data = null;

        return $this->sriStorage->remove();
    }

    /**
     * Loads integrity data from a storage.
     *
     * @return array
     * @throws FileSystemException
     */
    private function getData(): array
    {
        if ($this->data === null) {
            $cache = $this->sriStorage->load($this->context);

            $this->data = $cache ? $this->serializer->unserialize($cache) : [];
        }

        return $this->data;
    }
}
