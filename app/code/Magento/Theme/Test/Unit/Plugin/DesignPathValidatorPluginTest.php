<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Theme\Test\Unit\Plugin;

use Magento\Config\Model\Config\PathValidator;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Framework\Exception\ValidatorException;
use Magento\Theme\Api\Data\DesignConfigExtensionInterface;
use Magento\Theme\Api\Data\DesignConfigInterface;
use Magento\Theme\Model\DesignConfigRepository;
use Magento\Theme\Plugin\DesignPathValidatorPlugin;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DesignPathValidatorPluginTest extends TestCase
{
    /**
     * @var Structure|MockObject
     */
    private $structure;

    /**
     * @var DesignConfigRepository|MockObject
     */
    private $designConfigRepository;

    /**
     * @var DesignPathValidatorPlugin
     */
    private $plugin;

    protected function setUp(): void
    {
        $this->structure = $this->createMock(Structure::class);
        $this->designConfigRepository = $this->createMock(DesignConfigRepository::class);
        $this->plugin = new DesignPathValidatorPlugin($this->structure, $this->designConfigRepository);
    }

    /**
     * @return void
     * @throws ValidatorException
     * @throws Exception
     */
    public function testAroundValidateWithValidPath()
    {
        $pathValidator = $this->createMock(PathValidator::class);
        $proceed = function ($path) {
            return true;
        };
        $path = 'design/header/default_title';

        $field = $this->createMock(Field::class);
        $field->expects($this->exactly(2))
            ->method('getConfigPath')
            ->willReturn($path);

        $this->structure->expects($this->once())
            ->method('getElementByConfigPath')
            ->with($path)
            ->willReturn($field);
        $this->structure->expects($this->once())
            ->method('getFieldPaths')
            ->willReturn([$path => $path]);

        $designConfig = $this->createMock(DesignConfigInterface::class);
        $extensionAttributes = $this->createConfiguredStub(
            DesignConfigExtensionInterface::class,
            [
                'getDesignConfigData' => []
            ]
        );
        $designConfig->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributes);
        $extensionAttributes->expects($this->once())
            ->method('getDesignConfigData')
            ->willReturn([]);

        $this->designConfigRepository->expects($this->once())
            ->method('getByScope')
            ->with('default', null)
            ->willReturn($designConfig);

        $result = $this->plugin->aroundValidate($pathValidator, $proceed, $path);
        $this->assertTrue($result);
    }

    /**
     * @return void
     * @throws Exception
     * @throws ValidatorException
     */
    public function testAroundValidateWithInvalidPath()
    {
        $this->expectException(ValidatorException::class);

        $pathValidator = $this->createMock(PathValidator::class);
        $proceed = function ($path) {
            return true;
        };
        $path = 'design/invalid_path';

        $this->structure->expects($this->once())
            ->method('getElementByConfigPath')
            ->with($path)
            ->willReturn(null);
        $this->structure->expects($this->once())
            ->method('getFieldPaths')
            ->willReturn([]);

        $designConfig = $this->createMock(DesignConfigInterface::class);
        $extensionAttributes = $this->createConfiguredStub(
            DesignConfigExtensionInterface::class,
            [
                'getDesignConfigData' => []
            ]
        );
        $designConfig->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributes);
        $extensionAttributes->expects($this->once())
            ->method('getDesignConfigData')
            ->willReturn([]);

        $this->designConfigRepository->expects($this->once())
            ->method('getByScope')
            ->with('default', null)
            ->willReturn($designConfig);

        $this->plugin->aroundValidate($pathValidator, $proceed, $path);
    }
}
