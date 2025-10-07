<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Swatches\Test\Unit\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options\AbstractSwatch;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Test\Unit\Helper\AttributeTestHelper;
use Magento\Swatches\Test\Unit\Helper\OptionTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Backend swatch abstract block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractSwatchTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $contextMock;

    /**
     * @var MockObject
     */
    protected $registryMock;

    /**
     * @var MockObject
     */
    protected $attrOptionCollectionFactoryMock;

    /**
     * @var MockObject
     */
    protected $mediaConfigMock;

    /**
     * @var MockObject
     */
    protected $universalFactoryMock;

    /**
     * @var MockObject
     */
    protected $swatchHelperMock;

    /**
     * @var MockObject
     */
    protected $block;

    /**
     * @var AdapterInterface|MockObject
     */
    protected $connectionMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        // Initialize ObjectManager for global access
        $objectManager = new ObjectManager($this);
        $objects = [
            [
                \Magento\Framework\Json\Helper\Data::class,
                $this->createMock(\Magento\Framework\Json\Helper\Data::class)
            ],
            [
                \Magento\Framework\View\Element\Html\Select::class,
                $this->createMock(\Magento\Framework\View\Element\Html\Select::class)
            ]
        ];
        $objectManager->prepareObjectManager($objects);

        $this->contextMock = $this->createMock(Context::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->attrOptionCollectionFactoryMock = $this->createPartialMock(
            CollectionFactory::class,
            ['create']
        );
        $this->mediaConfigMock = $this->createMock(Config::class);
        $this->universalFactoryMock = $this->createMock(UniversalFactory::class);
        $this->swatchHelperMock = $this->createMock(Media::class);

        $this->block = $this->createPartialMock(AbstractSwatch::class, ['getData']);
        // Set constructor arguments using reflection
        $reflection = new \ReflectionClass($this->block);
        $constructor = $reflection->getConstructor();
        if ($constructor) {
            $constructor->invokeArgs($this->block, [
                $this->contextMock,
                $this->registryMock,
                $this->attrOptionCollectionFactoryMock,
                $this->universalFactoryMock,
                $this->mediaConfigMock,
                $this->swatchHelperMock,
                []
            ]);
        }
            $this->connectionMock = $this->createStub(AdapterInterface::class);
    }

    /**
     * Test getStoreOptionValues with cached data
     *
     * @return void
     */
    public function testGetStoreOptionValuesWithCachedData(): void
    {
        $cachedValues = [
            14 => 'Blue',
            15 => 'Black'
        ];
        
        $this->block->expects($this->once())
            ->method('getData')
            ->with('store_option_values_1')
            ->willReturn($cachedValues);

        $result = $this->block->getStoreOptionValues(1);
        $this->assertEquals($cachedValues, $result);
    }

    /**
     * Test getStoreOptionValues with null data (database fetch scenario)
     *
     * @return void
     */
    public function testGetStoreOptionValuesWithNullData(): void
    {
        $this->block->expects($this->once())
            ->method('getData')
            ->with('store_option_values_1')
            ->willReturn(null);

        $option = new OptionTestHelper();

        $attrOptionCollectionMock = $this->createPartialMock(
            Collection::class,
            [
                'addFieldToFilter',
                'getIterator',
                'setAttributeFilter',
                'getConnection',
                'getTable',
                'load',
                'getSelect'
            ]
        );
        
        $attrOptionCollectionMock->method('getIterator')->willReturn(new \ArrayIterator([$option]));

        $this->attrOptionCollectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($attrOptionCollectionMock);

        $attribute = new AttributeTestHelper();

        $this->registryMock
            ->expects($this->once())
            ->method('registry')
            ->with('entity_attribute')
            ->willReturn($attribute);

        $attrOptionCollectionMock
            ->expects($this->once())
            ->method('setAttributeFilter')
            ->with(23)
            ->willReturnSelf();

        $attrOptionCollectionMock
            ->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->connectionMock);
        
        $attrOptionCollectionMock
            ->expects($this->atLeastOnce())
            ->method('getTable')
            ->willReturnMap([
                ['eav_attribute_option_value', 'eav_attribute_option_value'],
                ['eav_attribute_option_swatch', 'eav_attribute_option_swatch']
            ]);
        
        $attrOptionCollectionMock
            ->expects($this->once())
            ->method('load')
            ->willReturnSelf();

        $zendDbSelectMock = $this->createMock(Select::class);
        $attrOptionCollectionMock->expects($this->atLeastOnce())->method('getSelect')->willReturn($zendDbSelectMock);
        $zendDbSelectMock->expects($this->atLeastOnce())->method('joinLeft')->willReturnSelf();

        $expectedValues = [
            14 => 'Blue',
            'swatch' => [
                14 => '#0000FF'
            ]
        ];
        
        $result = $this->block->getStoreOptionValues(1);
        $this->assertEquals($expectedValues, $result);
    }
}
