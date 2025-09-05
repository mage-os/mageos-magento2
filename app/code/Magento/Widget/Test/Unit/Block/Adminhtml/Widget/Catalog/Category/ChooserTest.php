<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Widget\Test\Unit\Block\Adminhtml\Widget\Catalog\Category;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\Tree;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Escaper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\Adminhtml\Widget\Catalog\Category\Chooser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ChooserTest extends TestCase
{
    /**
     * @var Collection|MockObject
     */
    protected $collection;

    /**
     * @var Node|MockObject
     */
    protected $childNode;

    /**
     * @var Node|MockObject
     */
    protected $rootNode;

    /**
     * @var Tree|MockObject
     */
    protected $categoryTree;

    /**
     * @var Store|MockObject
     */
    protected $store;

    /**
     * @var StoreManagerInterface|MockObject
     */
    protected $storeManager;

    /**
     * @var RequestInterface|MockObject
     */
    protected $request;

    /**
     * @var Escaper|MockObject
     */
    protected $escaper;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $eventManager;

    /**
     * @var Context|MockObject
     */
    protected $context;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $objects = [
            [
                \Magento\Framework\View\Element\Template\Context::class,
                $this->createMock(\Magento\Framework\View\Element\Template\Context::class)
            ],
            [
                \Magento\Framework\App\ObjectManager::class,
                $this->createMock(\Magento\Framework\App\ObjectManager::class)
            ],
            [
                \Magento\Framework\View\Element\BlockFactory::class,
                $this->createMock(\Magento\Framework\View\Element\BlockFactory::class)
            ],
            [
                \Magento\Backend\Block\Template::class,
                $this->createMock(\Magento\Backend\Block\Template::class)
            ],
            [
                \Magento\Catalog\Block\Adminhtml\Category\AbstractCategory::class,
                $this->createMock(\Magento\Catalog\Block\Adminhtml\Category\AbstractCategory::class)
            ],
            [
                \Magento\Catalog\Block\Adminhtml\Category\Tree::class,
                $this->createMock(\Magento\Catalog\Block\Adminhtml\Category\Tree::class)
            ]
        ];
        $objectManager->prepareObjectManager($objects);

        $this->collection = $this->createMock(Collection::class);

        $this->childNode = new class extends Node {
            public function __construct()
            {
            }
            public function hasChildren()
            {
                return false;
            }
            public function getIdField()
            {
                return 'id';
            }
            public function getLevel()
            {
                return 3;
            }
        };

        $this->rootNode = new class($this->childNode) extends Node {
            /**
             * @var Node
             */
            private $childNode;
            public function __construct($childNode)
            {
                $this->childNode = $childNode;
                unset($childNode);
            }
            public function hasChildren()
            {
                return true;
            }
            public function getChildren()
            {
                return [$this->childNode];
            }
            public function getIdField()
            {
                return 'id';
            }
            public function getLevel()
            {
                return 1;
            }
        };
        $this->categoryTree = $this->createMock(Tree::class);
        $this->store = $this->createMock(Store::class);
        $this->storeManager = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->request = $this->getMockForAbstractClass(RequestInterface::class);
        $this->escaper = $this->createMock(Escaper::class);
        $this->eventManager = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->context = $this->createMock(Context::class);
    }

    public function testGetTreeHasLevelField()
    {
        $rootId = Category::TREE_ROOT_ID;
        $storeGroups = [];
        $storeId = 1;
        $level = 3;

        $this->collection->expects($this->any())->method('addAttributeToSelect')->willReturnMap(
            [
                ['url_key', false, $this->collection],
                ['is_anchor', false, $this->collection]
            ]
        );

        $this->categoryTree->expects($this->once())->method('load')->with(null, 3)->willReturnSelf();
        $this->categoryTree->expects($this->atLeastOnce())
            ->method('addCollectionData')
            ->with($this->collection)
            ->willReturnSelf();
        $this->categoryTree->expects($this->once())->method('getNodeById')->with($rootId)->willReturn($this->rootNode);

        $this->store->expects($this->atLeastOnce())->method('getId')->willReturn($storeId);

        $this->storeManager->expects($this->once())->method('getGroups')->willReturn($storeGroups);
        $this->storeManager->expects($this->atLeastOnce())->method('getStore')->willReturn($this->store);

        $this->context->expects($this->once())->method('getStoreManager')->willReturn($this->storeManager);
        $this->context->expects($this->once())->method('getRequest')->willReturn($this->request);
        $this->context->expects($this->once())->method('getEscaper')->willReturn($this->escaper);
        $this->context->expects($this->once())->method('getEventManager')->willReturn($this->eventManager);

        /** @var Chooser $chooser */
        $chooser = (new ObjectManager($this))
            ->getObject(
                Chooser::class,
                [
                    'categoryTree' => $this->categoryTree,
                    'context' => $this->context
                ]
            );
        $chooser->setData('category_collection', $this->collection);
        $result = $chooser->getTree();
        $this->assertEquals($level, $result[0]['level']);
    }
}
