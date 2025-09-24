<?php
/**
 * Copyright 2021 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQl\Test\Unit\Model\Resolver\Products\SearchCriteria\CollectionProcessor\FilterProcessor;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Collection\JoinMinimalPosition;
use Magento\CatalogGraphQl\Model\Resolver\Products\SearchCriteria\CollectionProcessor\FilterProcessor\CategoryFilter;
use Magento\Framework\Api\Filter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for Category filter
 */
class CategoryFilterTest extends TestCase
{
    /**
     * @var CategoryFilter
     */
    private $model;

    /**
     * @var CategoryFactory|MockObject
     */
    private $categoryFactory;

    /**
     * @var Category|MockObject
     */
    private $categoryResourceModel;

    /**
     * @var JoinMinimalPosition
     */
    private $joinMinimalPosition;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryFactory = $this->createMock(CategoryFactory::class);
        $this->categoryResourceModel = $this->createMock(Category::class);
        $this->joinMinimalPosition = $this->createMock(JoinMinimalPosition::class);
        $this->model = new CategoryFilter(
            $this->categoryFactory,
            $this->categoryResourceModel,
            $this->joinMinimalPosition
        );
    }

    /**
     * Test that category filter works correctly with condition type "in" and multiple categories
     */
    public function testApplyWithConditionTypeInAndMultipleCategories(): void
    {
        $filter = new Filter();
        $category1 = new class extends \Magento\Catalog\Model\Category {
            private $isAnchor = false;
            private $children = '';
            
            public function __construct() {}
            
            public function getIsAnchor() {
                return $this->isAnchor;
            }
            
            public function setIsAnchor($isAnchor) {
                $this->isAnchor = $isAnchor;
                return $this;
            }
            
            public function getChildren($recursive = true, $isActive = true, $sortByPosition = true) {
                return $this->children;
            }
            
            public function setChildren($children) {
                $this->children = $children;
                return $this;
            }
            
            // CategoryInterface methods
            public function getId() { return null; }
            public function setId($id) { return $this; }
            public function getParentId() { return null; }
            public function setParentId($parentId) { return $this; }
            public function getName() { return null; }
            public function setName($name) { return $this; }
            public function getIsActive() { return null; }
            public function setIsActive($isActive) { return $this; }
            public function getPosition() { return null; }
            public function setPosition($position) { return $this; }
            public function getLevel() { return null; }
            public function setLevel($level) { return $this; }
            public function getCreatedAt() { return null; }
            public function setCreatedAt($createdAt) { return $this; }
            public function getUpdatedAt() { return null; }
            public function setUpdatedAt($updatedAt) { return $this; }
            public function getPath() { return null; }
            public function setPath($path) { return $this; }
            public function getAvailableSortBy() { return null; }
            public function setAvailableSortBy($availableSortBy) { return $this; }
            public function getIncludeInMenu() { return null; }
            public function setIncludeInMenu($includeInMenu) { return $this; }
            public function getProductCount() { return null; }
            public function setProductCount($productCount) { return $this; }
            public function getChildrenData() { return null; }
            public function setChildrenData(?array $childrenData = null) { return $this; }
            public function getExtensionAttributes() { return null; }
            public function setExtensionAttributes($extensionAttributes) { return $this; }
            public function getCustomAttribute($attributeCode) { return null; }
            public function setCustomAttribute($attributeCode, $attributeValue) { return $this; }
            public function getCustomAttributes() { return []; }
            public function setCustomAttributes(array $attributes) { return $this; }
        };
        
        $category3 = new class extends \Magento\Catalog\Model\Category {
            private $isAnchor = false;
            private $children = '';
            
            public function __construct() {}
            
            public function getIsAnchor() {
                return $this->isAnchor;
            }
            
            public function setIsAnchor($isAnchor) {
                $this->isAnchor = $isAnchor;
                return $this;
            }
            
            public function getChildren($recursive = true, $isActive = true, $sortByPosition = true) {
                return $this->children;
            }
            
            public function setChildren($children) {
                $this->children = $children;
                return $this;
            }
        };
        $collection = $this->createMock(Collection::class);
        $filter->setConditionType('in');
        $filter->setValue('1,3');
        $this->categoryFactory->expects($this->exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls($category1, $category3);
        $this->categoryResourceModel->expects($this->exactly(2))
            ->method('load')
            ->willReturnCallback(function (...$args) use ($category1, $category3) {
                static $index = 0;
                $expectedArgs = [
                    [$category1, 1],
                    [$category3, 3]
                ];
                $index++;
                if ($args === $expectedArgs[$index - 1]) {
                    return null;
                }
            });
        $collection->expects($this->never())
            ->method('addCategoryFilter');
        $collection->expects($this->once())
            ->method('addCategoriesFilter')
            ->with(['in' => [1, 2, 3]]);
        $category1->setIsAnchor(true);
        $category1->setChildren('2');
        $category3->setIsAnchor(false);
        $this->joinMinimalPosition->expects($this->once())
            ->method('execute')
            ->with($collection, [1, 3]);
        $this->model->apply($filter, $collection);
    }

    /**
     * @param string $condition
     */
    #[DataProvider('applyWithOtherSupportedConditionTypesDataProvider')]
    public function testApplyWithOtherSupportedConditionTypes(string $condition): void
    {
        $filter = new Filter();
        $category = new class extends \Magento\Catalog\Model\Category {
            private $isAnchor = false;
            private $children = '';
            
            public function __construct() {}
            
            public function getIsAnchor() {
                return $this->isAnchor;
            }
            
            public function setIsAnchor($isAnchor) {
                $this->isAnchor = $isAnchor;
                return $this;
            }
            
            public function getChildren($recursive = true, $isActive = true, $sortByPosition = true) {
                return $this->children;
            }
            
            public function setChildren($children) {
                $this->children = $children;
                return $this;
            }
        };
        $collection = $this->createMock(Collection::class);
        $filter->setConditionType($condition);
        $categoryId = 1;
        $filter->setValue($categoryId);
        $this->categoryFactory->expects($this->once())
            ->method('create')
            ->willReturn($category);
        $this->categoryResourceModel->expects($this->once())
            ->method('load')
            ->with($category, $categoryId);
        $collection->expects($this->never())
            ->method('addCategoryFilter');
        $collection->expects($this->once())
            ->method('addCategoriesFilter')
            ->with([$condition => [1, 2]]);
        $category->setIsAnchor(true);
        $category->setChildren('2');
        $this->model->apply($filter, $collection);
    }

    /**
     * @return array
     */
    public static function applyWithOtherSupportedConditionTypesDataProvider(): array
    {
        return [['neq'], ['nin'],];
    }

    /**
     * @param string $condition
     */
    #[DataProvider('applyWithUnsupportedConditionTypesDataProvider')]
    public function testApplyWithUnsupportedConditionTypes(string $condition): void
    {
        $filter = new Filter();
        $collection = $this->createMock(Collection::class);
        $filter->setConditionType($condition);
        $categoryId = 1;
        $filter->setValue($categoryId);
        $this->categoryFactory->expects($this->never())
            ->method('create');
        $this->categoryResourceModel->expects($this->never())
            ->method('load');
        $collection->expects($this->never())
            ->method('addCategoryFilter');
        $collection->expects($this->never())
            ->method('addCategoriesFilter');
        $this->model->apply($filter, $collection);
    }

    /**
     * @return array
     */
    public static function applyWithUnsupportedConditionTypesDataProvider(): array
    {
        return [['gteq'], ['lteq'], ['gt'], ['lt'], ['like'], ['nlike']];
    }
}
