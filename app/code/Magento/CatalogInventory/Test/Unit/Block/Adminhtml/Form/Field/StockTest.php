<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Block\Adminhtml\Form\Field;

use Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\Data\Form\Element\TextFactory;
use Magento\Framework\Escaper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class StockTest extends TestCase
{
    use MockCreationTrait;

    public const ATTRIBUTE_NAME = 'quantity_and_stock_status';

    /**
     * @var Factory|MockObject
     */
    protected $_factoryElementMock;

    /**
     * @var CollectionFactory|MockObject
     */
    protected $_collectionFactoryMock;

    /**
     * @var Text|MockObject
     */
    protected $_qtyMock;

    /**
     * @var TextFactory|MockObject
     */
    protected $_factoryTextMock;

    /**
     * @var Stock
     */
    protected $_block;

    protected function setUp(): void
    {
        // Create minimal ObjectManager mock and set it up first
        $objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        ObjectManager::setInstance($objectManagerMock);
        
        $this->_factoryElementMock = $this->createMock(Factory::class);
        $this->_collectionFactoryMock = $this->createMock(
            CollectionFactory::class
        );
        $escaperMock = $this->createMock(Escaper::class);
        $secureHtmlRendererMock = $this->createMock(SecureHtmlRenderer::class);
        
        // Configure ObjectManager mock to return the appropriate mocks when requested
        $objectManagerMock->method('get')
            ->willReturnMap([
                [Escaper::class, $escaperMock],
                [SecureHtmlRenderer::class, $secureHtmlRendererMock]
            ]);
        
        $this->_qtyMock = $this->createPartialMockWithReflection(
            Text::class,
            ['setId', 'getId', 'setName', 'getName', 'setLabel', 'getLabel', 'setValue', 'getValue']
        );
        
        // Implement stateful behavior for setName/getName, setValue/getValue, setId/getId, setLabel/getLabel
        $name = null;
        $value = null;
        $id = null;
        $label = null;
        
        $qtyMock = $this->_qtyMock;
        
        $this->_qtyMock->method('setName')->willReturnCallback(function ($val) use (&$name, $qtyMock) {
            $name = $val;
            return $qtyMock;
        });
        
        $this->_qtyMock->method('getName')->willReturnCallback(function () use (&$name) {
            return $name;
        });
        
        $this->_qtyMock->method('setValue')->willReturnCallback(function ($val) use (&$value, $qtyMock) {
            $value = $val;
            return $qtyMock;
        });
        $this->_qtyMock->method('getValue')->willReturnCallback(function () use (&$value) {
            return $value;
        });
        
        $this->_qtyMock->method('setId')->willReturnCallback(function ($val) use (&$id, $qtyMock) {
            $id = $val;
            return $qtyMock;
        });
        $this->_qtyMock->method('getId')->willReturnCallback(function () use (&$id) {
            return $id;
        });
        
        $this->_qtyMock->method('setLabel')->willReturnCallback(function ($val) use (&$label, $qtyMock) {
            $label = $val;
            return $qtyMock;
        });
        $this->_qtyMock->method('getLabel')->willReturnCallback(function () use (&$label) {
            return $label;
        });
        
        $this->_factoryTextMock = $this->createMock(TextFactory::class);

        $coreRegistryMock = $this->createMock(Registry::class);
        
        // Instantiate Stock block directly with mocks
        $this->_block = new Stock(
            $this->_factoryElementMock,
            $this->_collectionFactoryMock,
            $escaperMock,
            $this->_factoryTextMock,
            $coreRegistryMock,
            ['qty' => $this->_qtyMock, 'name' => self::ATTRIBUTE_NAME]
        );
    }

    public function testSetForm()
    {
        $escaperMock = $this->createMock(Escaper::class);
        $formElement = new Text(
            $this->_factoryElementMock,
            $this->_collectionFactoryMock,
            $escaperMock
        );
        
        $this->_block->setForm($formElement);
        
        // Verify that setForm was called on the qty mock
        $this->assertSame($formElement, $this->_qtyMock->getForm());
    }

    public function testSetValue()
    {
        $value = ['qty' => 1, 'is_in_stock' => 0];
        
        $this->_block->setValue($value);
        
        // Verify that setValue was called on the qty mock with the correct value
        $this->assertEquals(1, $this->_qtyMock->getValue());
    }

    public function testSetName()
    {
        $this->_block->setName(self::ATTRIBUTE_NAME);
        
        // Verify that setName was called on the qty mock with the correct value
        $this->assertEquals(self::ATTRIBUTE_NAME . '[qty]', $this->_qtyMock->getName());
    }
}
