<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
namespace Magento\Catalog\Block\Adminhtml\Category;

use Magento\Catalog\Test\Fixture\Category as CategoryFixture;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Test for categories in the tree block.
 */
class TreeBlockTest extends AbstractBackendController
{

    /**
     * Test disabled categories in the tree block.
     */
    #[
        AppArea('adminhtml'),
        DataFixture(CategoryFixture::class, ['is_active' => false], as:'new_category'),
    ]
    public function testDisabledCategoriesHtml()
    {
        $this->dispatch("backend/catalog/category/index/");
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
        $layout = $this->_objectManager->get(LayoutInterface::class);
        $categoryTree = $layout->getBlock('category.tree');
        $blockHtml = $categoryTree->toHtml();
        $this->assertStringContainsString('no-active-category', $blockHtml);
    }
}
