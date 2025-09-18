<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\GraphQl\Schema\Type;

use LogicException;
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\TypeInterface;
use Magento\Framework\ObjectManager\ResetAfterRequestInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;

/**
 * GraphQL type object registry
 */
class TypeRegistry implements ResetAfterRequestInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Key is config class name, value is related type class name
     *
     * @var array
     */
    private $configToTypeMap;

    /**
     * @var TypeInterface[]
     */
    private $types = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ConfigInterface $config
     * @param array $configToTypeMap
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ConfigInterface $config,
        array $configToTypeMap
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->configToTypeMap = $configToTypeMap;
    }

    /**
     * Get GraphQL type object by type name
     *
     * @param string $typeName
     * @return TypeInterface|InputTypeInterface|OutputTypeInterface
     * @throws GraphQlInputException
     */
    public function get(string $typeName): TypeInterface
    {
        if (!isset($this->types[$typeName])) {
            try {
                $configElement = $this->config->getConfigElement($typeName);
            } catch (LogicException) {
                throw new GraphQlInputException(
                    new Phrase('Unknown type "%1".', [$typeName])
                );
            }

            $configElementClass = get_class($configElement);
            if (!isset($this->configToTypeMap[$configElementClass])) {
                throw new GraphQlInputException(
                    new Phrase(
                        "No mapping to Webonyx type is declared for '%1' config element type.",
                        [$configElementClass]
                    )
                );
            }

            $this->types[$typeName] = $this->objectManager->create(
                $this->configToTypeMap[$configElementClass],
                [
                    'configElement' => $configElement,
                ]
            );

            if (!($this->types[$typeName] instanceof TypeInterface)) {
                throw new GraphQlInputException(
                    new Phrase("Type '{$typeName}' was requested but is not declared in the GraphQL schema.")
                );
            }
        }
        return $this->types[$typeName];
    }

    /**
     * @inheritDoc
     */
    public function _resetState(): void
    {
        $this->types = [];
    }
}
