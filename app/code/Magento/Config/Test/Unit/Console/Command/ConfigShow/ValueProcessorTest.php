<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Console\Command\ConfigShow;

use Magento\Config\Console\Command\ConfigShow\ValueProcessor;
use Magento\Config\Model\Config\Backend\Encrypted;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Config\Model\Config\StructureFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use PHPUnit\Framework\MockObject\Matcher\InvokedCount;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test for ValueProcessor.
 *
 * @see ValueProcessor
 */
class ValueProcessorTest extends TestCase
{
    /**
     * @var ValueFactory|MockObject
     */
    private $valueFactoryMock;

    /**
     * @var ScopeInterface|MockObject
     */
    private $scopeMock;

    /**
     * @var StructureFactory|MockObject
     */
    private $structureFactoryMock;

    /**
     * @var JsonSerializer|MockObject
     */
    private $jsonSerializerMock;

    /**
     * @var ValueProcessor
     */
    private $valueProcessor;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->valueFactoryMock = $this->createMock(ValueFactory::class);
        $this->scopeMock = $this->createMock(ScopeInterface::class);
        $this->structureFactoryMock = $this->createPartialMock(StructureFactory::class, ['create']);
        $this->jsonSerializerMock = $this->createMock(JsonSerializer::class);

        $this->valueProcessor = new ValueProcessor(
            $this->scopeMock,
            $this->structureFactoryMock,
            $this->valueFactoryMock,
            $this->jsonSerializerMock
        );
    }

    /**
     * @param bool $hasBackendModel
     * @param InvokedCount $expectsGetBackendModel
     * @param InvokedCount $expectsCreate
     * @param InvokedCount $expectsGetValue
     * @param InvokedCount $expectsSetPath
     * @param InvokedCount $expectsSetScope
     * @param InvokedCount $expectsSetScopeId
     * @param InvokedCount $expectsSetValue
     * @param InvokedCount $expectsAfterLoad
     * @param InvokedCount $expectsSerialize
     * @param string $expectsValue
     * @param string $className
     * @param string $value
     * @param string|array $processedValue
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    #[DataProvider('processDataProvider')]
    public function testProcess(
        $hasBackendModel,
        $expectsGetBackendModel,
        $expectsCreate,
        $expectsGetValue,
        $expectsSetPath,
        $expectsSetScope,
        $expectsSetScopeId,
        $expectsSetValue,
        $expectsAfterLoad,
        $expectsSerialize,
        $expectsValue,
        $className,
        $value,
        $processedValue
    ): void {
        $scope = 'someScope';
        $scopeCode = 'someScopeCode';
        $path = 'some/config/path';
        $oldConfigScope = 'oldConfigScope';

        $this->scopeMock->expects($this->once())
            ->method('getCurrentScope')
            ->willReturn($oldConfigScope);
        $this->scopeMock
            ->method('setCurrentScope')
            ->willReturnCallback(function ($scope) use ($oldConfigScope) {
                if ($scope == Area::AREA_ADMINHTML || $scope == $oldConfigScope) {
                    return null;
                }
            });

        /** @var Structure|MockObject $structureMock */
        $structureMock = $this->createMock(Structure::class);
        $this->structureFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($structureMock);

        /** @var Value|Encrypted|MockObject $valueMock */
        $backendModelMock = $this->createPartialMock(
            $className,
            ['setPath', 'setScope', 'setScopeId', 'setValue', 'getValue', 'afterLoad']
        );
        $backendModelMock->expects($expectsSetPath)
            ->method('setPath')
            ->with($path)
            ->willReturnSelf();
        $backendModelMock->expects($expectsSetScope)
            ->method('setScope')
            ->with($scope)
            ->willReturnSelf();
        $backendModelMock->expects($expectsSetScopeId)
            ->method('setScopeId')
            ->with($scopeCode)
            ->willReturnSelf();
        $backendModelMock->expects($expectsSetValue)
            ->method('setValue')
            ->with($value)
            ->willReturnSelf();
        $backendModelMock->expects($expectsAfterLoad)
            ->method('afterLoad')
            ->willReturnSelf();
        $backendModelMock->expects($expectsGetValue)
            ->method('getValue')
            ->willReturn($processedValue);

        /** @var Field|MockObject $fieldMock */
        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->once())
            ->method('hasBackendModel')
            ->willReturn($hasBackendModel);
        $fieldMock->expects($expectsGetBackendModel)
            ->method('getBackendModel')
            ->willReturn($backendModelMock);
        $this->valueFactoryMock->expects($expectsCreate)
            ->method('create')
            ->willReturn($backendModelMock);
        $this->jsonSerializerMock->expects($expectsSerialize)
            ->method('serialize')
            ->with($processedValue)
            ->willReturn($expectsValue);

        $structureMock->expects($this->once())
            ->method('getElementByConfigPath')
            ->with($path)
            ->willReturn($fieldMock);

        $this->assertSame($expectsValue, $this->valueProcessor->process($scope, $scopeCode, $value, $path));
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function processDataProvider(): array
    {
        return [
            [
                'hasBackendModel' => true,
                'expectsGetBackendModel' => self::once(),
                'expectsCreate' => self::never(),
                'expectsGetValue' => self::once(),
                'expectsSetPath' => self::once(),
                'expectsSetScope' => self::once(),
                'expectsSetScopeId' => self::once(),
                'expectsSetValue' => self::once(),
                'expectsAfterLoad' => self::once(),
                'expectsSerialize' => self::once(),
                'expectsValue' => '{value:someValue}',
                'className' => Value::class,
                'value' => '{value:someValue}',
                'processedValue' => ['someValue']
            ],
            [
                'hasBackendModel' => true,
                'expectsGetBackendModel' => self::once(),
                'expectsCreate' => self::never(),
                'expectsGetValue' => self::once(),
                'expectsSetPath' => self::once(),
                'expectsSetScope' => self::once(),
                'expectsSetScopeId' => self::once(),
                'expectsSetValue' => self::once(),
                'expectsAfterLoad' => self::once(),
                'expectsSerialize' => self::never(),
                'expectsValue' => 'someValue',
                'className' => Value::class,
                'value' => 'someValue',
                'processedValue' => 'someValue'
            ],
            [
                'hasBackendModel' => false,
                'expectsGetBackendModel' => self::never(),
                'expectsCreate' => self::once(),
                'expectsGetValue' => self::once(),
                'expectsSetPath' => self::once(),
                'expectsSetScope' => self::once(),
                'expectsSetScopeId' => self::once(),
                'expectsSetValue' => self::once(),
                'expectsAfterLoad' => self::once(),
                'expectsSerialize' => self::never(),
                'expectsValue' => 'someValue',
                'className' => Value::class,
                'value' => 'someValue',
                'processedValue' => 'someValue'
            ],
            [
                'hasBackendModel' => true,
                'expectsGetBackendModel' => self::once(),
                'expectsCreate' => self::never(),
                'expectsGetValue' => self::never(),
                'expectsSetPath' => self::never(),
                'expectsSetScope' => self::never(),
                'expectsSetScopeId' => self::never(),
                'expectsSetValue' => self::never(),
                'expectsAfterLoad' => self::never(),
                'expectsSerialize' => self::never(),
                'expectsValue' => ValueProcessor::SAFE_PLACEHOLDER,
                'className' => Encrypted::class,
                'value' => 'someValue',
                'processedValue' => 'someValue'
            ],
            [
                'hasBackendModel' => true,
                'expectsGetBackendModel' => self::once(),
                'expectsCreate' => self::never(),
                'expectsGetValue' => self::once(),
                'expectsSetPath' => self::once(),
                'expectsSetScope' => self::once(),
                'expectsSetScopeId' => self::once(),
                'expectsSetValue' => self::once(),
                'expectsAfterLoad' => self::once(),
                'expectsSerialize' => self::never(),
                'expectsValue' => null,
                'className' => Value::class,
                'value' => null,
                'processedValue' => null
            ],
            [
                'hasBackendModel' => true,
                'expectsGetBackendModel' => self::once(),
                'expectsCreate' => self::never(),
                'expectsGetValue' => self::never(),
                'expectsSetPath' => self::never(),
                'expectsSetScope' => self::never(),
                'expectsSetScopeId' => self::never(),
                'expectsSetValue' => self::never(),
                'expectsAfterLoad' => self::never(),
                'expectsSerialize' => self::never(),
                'expectsValue' => null,
                'className' => Encrypted::class,
                'value' => null,
                'processedValue' => null
            ],
        ];
    }
}
