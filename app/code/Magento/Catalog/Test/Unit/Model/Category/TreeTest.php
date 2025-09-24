<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Category;

use Magento\Catalog\Api\Data\CategoryTreeInterface;
use Magento\Catalog\Api\Data\CategoryTreeInterfaceFactory;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\Tree;
use Magento\Catalog\Model\ResourceModel\Category\TreeFactory;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TreeTest extends TestCase
{
    /**
     * @var MockObject|Tree
     */
    protected $categoryTreeMock;

    /**
     * @var MockObject|StoreManagerInterface
     */
    protected $storeManagerMock;

    /**
     * @var MockObject|Collection
     */
    protected $categoryCollection;

    /**
     * @var MockObject|CategoryTreeInterfaceFactory
     */
    protected $treeFactoryMock;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\Category\Tree
     */
    protected $tree;

    /**
     * @var \Magento\Catalog\Model\Category\Tree
     */
    protected $node;

    /**
     * @var TreeFactory
     */
    private $treeResourceFactoryMock;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->categoryTreeMock = $this->getMockBuilder(
            Tree::class
        )->disableOriginalConstructor()
            ->getMock();

        $this->categoryCollection = $this->getMockBuilder(
            Collection::class
        )->disableOriginalConstructor()
            ->getMock();

        $this->storeManagerMock = $this->getMockBuilder(
            StoreManagerInterface::class
        )->disableOriginalConstructor()
            ->getMock();

        $this->treeResourceFactoryMock = $this->createMock(
            TreeFactory::class
        );
        $this->treeResourceFactoryMock->method('create')
            ->willReturn($this->categoryTreeMock);

        $methods = ['create'];
        $this->treeFactoryMock =
            $this->createPartialMock(CategoryTreeInterfaceFactory::class, $methods);

        $this->tree = $this->objectManager
            ->getObject(
                \Magento\Catalog\Model\Category\Tree::class,
                [
                    'categoryCollection' => $this->categoryCollection,
                    'categoryTree' => $this->categoryTreeMock,
                    'storeManager' => $this->storeManagerMock,
                    'treeFactory' => $this->treeFactoryMock,
                    'treeResourceFactory' => $this->treeResourceFactoryMock,
                ]
            );
    }

    public function testGetNode()
    {
        $category = $this->getMockBuilder(
            Category::class
        )->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->exactly(2))->method('getId')->willReturn(1);

        $node = $this->getMockBuilder(
            Node::class
        )->disableOriginalConstructor()
            ->getMock();

        $node->expects($this->once())->method('loadChildren');
        $this->categoryTreeMock->expects($this->once())->method('loadNode')
            ->with(1)
            ->willReturn($node);

        $store = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $store->expects($this->once())->method('getId')->willReturn(1);
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($store);

        $this->categoryCollection->expects($this->any())->method('addAttributeToSelect')->willReturnSelf();
        $this->categoryCollection->expects($this->once())->method('setProductStoreId')->willReturnSelf();
        $this->categoryCollection->expects($this->once())->method('setLoadProductCount')->willReturnSelf();
        $this->categoryCollection->expects($this->once())->method('setStoreId')->willReturnSelf();

        $this->categoryTreeMock->expects($this->once())->method('addCollectionData')
            ->with($this->categoryCollection);
        $this->tree->getRootNode($category);
    }

    public function testGetRootNode()
    {
        $store = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $store->expects($this->once())->method('getRootCategoryId')->willReturn(2);
        $store->expects($this->once())->method('getId')->willReturn(1);
        $this->storeManagerMock->method('getStore')->willReturn($store);

        $this->categoryCollection->expects($this->any())->method('addAttributeToSelect')->willReturnSelf();
        $this->categoryCollection->expects($this->once())->method('setProductStoreId')->willReturnSelf();
        $this->categoryCollection->expects($this->once())->method('setLoadProductCount')->willReturnSelf();
        $this->categoryCollection->expects($this->once())->method('setStoreId')->willReturnSelf();

        $node = $this->getMockBuilder(
            Tree::class
        )->disableOriginalConstructor()
            ->getMock();
        $node->expects($this->once())->method('addCollectionData')
            ->with($this->categoryCollection);
        $node->expects($this->once())->method('getNodeById')->with(2);
        $this->categoryTreeMock->expects($this->once())->method('load')
            ->with(null)
            ->willReturn($node);
        $this->tree->getRootNode();
    }

    public function testGetTree()
    {
        $depth = 2;
        $currentLevel = 1;

        $treeNodeMock1 = $this->createMock(CategoryTreeInterface::class);
        $treeNodeMock1->expects($this->once())->method('setId')->with($currentLevel)->willReturnSelf();
        $treeNodeMock1->expects($this->once())->method('setParentId')->with($currentLevel - 1)->willReturnSelf();
        $treeNodeMock1->expects($this->once())->method('setName')->with('Name' . $currentLevel)->willReturnSelf();
        $treeNodeMock1->expects($this->once())->method('setPosition')->with($currentLevel)->willReturnSelf();
        $treeNodeMock1->expects($this->once())->method('setLevel')->with($currentLevel)->willReturnSelf();
        $treeNodeMock1->expects($this->once())->method('setIsActive')->with(true)->willReturnSelf();
        $treeNodeMock1->expects($this->once())->method('setProductCount')->with(4)->willReturnSelf();
        $treeNodeMock1->expects($this->once())->method('setChildrenData')->willReturnSelf();

        $treeNodeMock2 = $this->createMock(CategoryTreeInterface::class);
        $treeNodeMock2->expects($this->once())->method('setId')->with($currentLevel)->willReturnSelf();
        $treeNodeMock2->expects($this->once())->method('setParentId')->with($currentLevel - 1)->willReturnSelf();
        $treeNodeMock2->expects($this->once())->method('setName')->with('Name' . $currentLevel)->willReturnSelf();
        $treeNodeMock2->expects($this->once())->method('setPosition')->with($currentLevel)->willReturnSelf();
        $treeNodeMock2->expects($this->once())->method('setLevel')->with($currentLevel)->willReturnSelf();
        $treeNodeMock2->expects($this->once())->method('setIsActive')->with(true)->willReturnSelf();
        $treeNodeMock2->expects($this->once())->method('setProductCount')->with(4)->willReturnSelf();
        $treeNodeMock2->expects($this->once())->method('setChildrenData')->willReturnSelf();

        $this->treeFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls($treeNodeMock1, $treeNodeMock2);
        $node = new class extends Node {
            private $parentId = 0;
            private $position = 0;
            private $level = 0;
            private $productCount = 0;
            private $hasChildren = false;
            private $children = [];
            private $id = 0;
            private $name = '';
            private $isActive = false;
            
            public function __construct()
            {
            }
            
            public function getParentId()
            {
                return $this->parentId;
            }
            
            public function setParentId($parentId)
            {
                $this->parentId = $parentId;
                return $this;
            }
            
            public function getPosition()
            {
                return $this->position;
            }
            
            public function setPosition($position)
            {
                $this->position = $position;
                return $this;
            }
            
            public function getLevel()
            {
                return $this->level;
            }
            
            public function setLevel($level)
            {
                $this->level = $level;
                return $this;
            }
            
            public function getProductCount()
            {
                return $this->productCount;
            }
            
            public function setProductCount($productCount)
            {
                $this->productCount = $productCount;
                return $this;
            }
            
            public function hasChildren()
            {
                return $this->hasChildren;
            }
            
            public function setHasChildren($hasChildren)
            {
                $this->hasChildren = $hasChildren;
                return $this;
            }
            
            public function getChildren()
            {
                return $this->children;
            }
            
            public function setChildren($children)
            {
                $this->children = $children;
                return $this;
            }
            
            public function getId()
            {
                return $this->id;
            }
            
            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }
            
            public function getName()
            {
                return $this->name;
            }
            
            public function setName($name)
            {
                $this->name = $name;
                return $this;
            }
            
            public function getIsActive()
            {
                return $this->isActive;
            }
            
            public function setIsActive($isActive)
            {
                $this->isActive = $isActive;
                return $this;
            }
        };
        $node->setHasChildren(true);
        $node->setChildren([$node]);
        $node->setId($currentLevel);
        $node->setParentId($currentLevel - 1);
        $node->setName('Name' . $currentLevel);
        $node->setPosition($currentLevel);
        $node->setLevel($currentLevel);
        $node->setIsActive(true);
        $node->setProductCount(4);
        $this->tree->getTree($node, $depth, $currentLevel);
    }

    public function testGetTreeWhenChildrenAreNotExist()
    {
        $currentLevel = 1;
        $treeNodeMock = $this->createMock(CategoryTreeInterface::class);
        $this->treeFactoryMock->method('create')->willReturn($treeNodeMock);
        $treeNodeMock->expects($this->once())->method('setId')->with($currentLevel)->willReturnSelf();
        $treeNodeMock->expects($this->once())->method('setParentId')->with($currentLevel - 1)->willReturnSelf();
        $treeNodeMock->expects($this->once())->method('setName')->with('Name' . $currentLevel)->willReturnSelf();
        $treeNodeMock->expects($this->once())->method('setPosition')->with($currentLevel)->willReturnSelf();
        $treeNodeMock->expects($this->once())->method('setLevel')->with($currentLevel)->willReturnSelf();
        $treeNodeMock->expects($this->once())->method('setIsActive')->with(true)->willReturnSelf();
        $treeNodeMock->expects($this->once())->method('setProductCount')->with(4)->willReturnSelf();
        $treeNodeMock->expects($this->once())->method('setChildrenData')->willReturnSelf();

        $node = new class extends Node {
            private $parentId = 0;
            private $position = 0;
            private $level = 0;
            private $productCount = 0;
            private $hasChildren = false;
            private $children = [];
            private $id = 0;
            private $name = '';
            private $isActive = false;
            
            public function __construct()
            {
            }
            
            public function getParentId()
            {
                return $this->parentId;
            }
            
            public function setParentId($parentId)
            {
                $this->parentId = $parentId;
                return $this;
            }
            
            public function getPosition()
            {
                return $this->position;
            }
            
            public function setPosition($position)
            {
                $this->position = $position;
                return $this;
            }
            
            public function getLevel()
            {
                return $this->level;
            }
            
            public function setLevel($level)
            {
                $this->level = $level;
                return $this;
            }
            
            public function getProductCount()
            {
                return $this->productCount;
            }
            
            public function setProductCount($productCount)
            {
                $this->productCount = $productCount;
                return $this;
            }
            
            public function hasChildren()
            {
                return $this->hasChildren;
            }
            
            public function setHasChildren($hasChildren)
            {
                $this->hasChildren = $hasChildren;
                return $this;
            }
            
            public function getChildren()
            {
                return $this->children;
            }
            
            public function setChildren($children)
            {
                $this->children = $children;
                return $this;
            }
            
            public function getId()
            {
                return $this->id;
            }
            
            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }
            
            public function getName()
            {
                return $this->name;
            }
            
            public function setName($name)
            {
                $this->name = $name;
                return $this;
            }
            
            public function getIsActive()
            {
                return $this->isActive;
            }
            
            public function setIsActive($isActive)
            {
                $this->isActive = $isActive;
                return $this;
            }
        };
        $node->setHasChildren(false);
        $node->setId($currentLevel);
        $node->setParentId($currentLevel - 1);
        $node->setName('Name' . $currentLevel);
        $node->setPosition($currentLevel);
        $node->setLevel($currentLevel);
        $node->setIsActive(true);
        $node->setProductCount(4);
        $this->tree->getTree($node);
    }
}
