<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Ui\DataProvider\Product\Form;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\Pool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductDataProviderTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var CollectionFactory|MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var Collection|MockObject
     */
    protected $collectionMock;

    /**
     * @var ModifierInterface|MockObject
     */
    protected $modifierMockOne;

    /**
     * @var Pool|MockObject
     */
    protected $poolMock;

    /**
     * @var ProductDataProvider
     */
    protected $model;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var CollectionFactory $this->collectionFactoryMock */
        $this->collectionFactoryMock = new class {
            private $createResult = null;
            
            public function __construct() {}
            
            public function create() { 
                return $this->createResult; 
            }
            public function setCreateResult($value) { 
                $this->createResult = $value; 
                return $this; 
            }
        };
        $this->collectionFactoryMock->setCreateResult($this->collectionMock);
        $this->poolMock = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ModifierInterface $this->modifierMockOne */
        $this->modifierMockOne = new class implements ModifierInterface {
            private $data = [];
            private $meta = [];
            
            public function __construct() {}
            
            public function getData() { 
                return $this->data; 
            }
            public function setData($value) { 
                $this->data = $value; 
                return $this; 
            }
            
            public function getMeta() { 
                return $this->meta; 
            }
            public function setMeta($value) { 
                $this->meta = $value; 
                return $this; 
            }
            
            // Required ModifierInterface methods
            public function modifyData(array $data) { 
                return $this->data; 
            }
            public function modifyMeta(array $meta) { 
                return $this->meta; 
            }
        };

        $this->model = $this->objectManager->getObject(ProductDataProvider::class, [
            'name' => 'testName',
            'primaryFieldName' => 'testPrimaryFieldName',
            'requestFieldName' => 'testRequestFieldName',
            'collectionFactory' => $this->collectionFactoryMock,
            'pool' => $this->poolMock,
        ]);
    }

    public function testGetMeta()
    {
        $expectedMeta = ['meta_key' => 'meta_value'];

        $this->poolMock->expects($this->once())
            ->method('getModifiersInstances')
            ->willReturn([$this->modifierMockOne]);
        $this->modifierMockOne->setMeta($expectedMeta);

        $this->assertSame($expectedMeta, $this->model->getMeta());
    }

    public function testGetData()
    {
        $expectedMeta = ['data_key' => 'data_value'];

        $this->poolMock->expects($this->once())
            ->method('getModifiersInstances')
            ->willReturn([$this->modifierMockOne]);
        $this->modifierMockOne->setData($expectedMeta);

        $this->assertSame($expectedMeta, $this->model->getData());
    }
}
