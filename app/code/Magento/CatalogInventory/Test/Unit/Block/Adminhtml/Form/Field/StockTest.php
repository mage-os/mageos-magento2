<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Block\Adminhtml\Form\Field;

use Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\Data\Form\Element\TextFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StockTest extends TestCase
{
    const ATTRIBUTE_NAME = 'quantity_and_stock_status';

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
        $objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        \Magento\Framework\App\ObjectManager::setInstance($objectManagerMock);
        
        $this->_factoryElementMock = $this->createMock(Factory::class);
        $this->_collectionFactoryMock = $this->createMock(
            CollectionFactory::class
        );
        $escaperMock = $this->createMock(\Magento\Framework\Escaper::class);
        $secureHtmlRendererMock = $this->createMock(\Magento\Framework\View\Helper\SecureHtmlRenderer::class);
        
        // Configure ObjectManager mock to return the appropriate mocks when requested
        $objectManagerMock->method('get')
            ->willReturnMap([
                [\Magento\Framework\Escaper::class, $escaperMock],
                [\Magento\Framework\View\Helper\SecureHtmlRenderer::class, $secureHtmlRendererMock]
            ]);
        
        $this->_qtyMock = new class($this->_factoryElementMock, $this->_collectionFactoryMock, $escaperMock) extends Text {
            private $value = null;
            private $name = null;
            private $form = null;

            public function __construct($factoryElement, $factoryCollection, $escaper) {
                parent::__construct($factoryElement, $factoryCollection, $escaper);
            }

            public function setValue($value) {
                $this->value = $value;
                return $this;
            }

            public function setName($name) {
                $this->name = $name;
                return $this;
            }

            public function setForm($form) {
                $this->form = $form;
                return $this;
            }

            public function getValue() {
                return $this->value;
            }

            public function getName() {
                return $this->name;
            }

            public function getForm() {
                return $this->form;
            }
        };
        $this->_factoryTextMock = $this->createMock(TextFactory::class);

        $coreRegistryMock = $this->createMock(\Magento\Framework\Registry::class);
        
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
        $escaperMock = $this->createMock(\Magento\Framework\Escaper::class);
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
