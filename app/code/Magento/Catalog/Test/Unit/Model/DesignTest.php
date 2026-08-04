<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category\Attribute\LayoutUpdateManager as CategoryLayoutManager;
use Magento\Catalog\Model\Design;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\LayoutUpdateManager as ProductLayoutManager;
use Magento\Catalog\Model\Session;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\TranslateInterface;
use Magento\Framework\View\DesignInterface;
use PHPUnit\Framework\TestCase;

class DesignTest extends TestCase
{
    /**
     * A null last-visited category id must never reach canBeShowInCategory().
     *
     * The method is declared as taking an int, and around-plugins on it are entitled to rely
     * on that. Passing null through made those plugins use null as an array key, which PHP 8.5
     * deprecates. See magento/magento2#40891.
     */
    public function testGetDesignSettingsSkipsCategoryLookupWhenLastVisitedIsNull()
    {
        $product = $this->createProductMock();
        $product->expects($this->never())->method('canBeShowInCategory');

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $categoryRepository->expects($this->never())->method('get');

        $design = $this->createDesign($this->createSessionMock(null), $categoryRepository);

        $this->assertSame('1column', $design->getDesignSettings($product)->getPageLayout());
    }

    /**
     * A non-null last-visited category id is still passed through unchanged.
     */
    public function testGetDesignSettingsResolvesCategoryWhenLastVisitedIsPresent()
    {
        $product = $this->createProductMock();
        $product->expects($this->once())->method('canBeShowInCategory')->with(7)->willReturn(true);

        $category = $this->createMock(Category::class);
        $category->method('getParentDesignCategory')->willReturn(null);

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $categoryRepository->expects($this->once())->method('get')->with(7)->willReturn($category);

        $design = $this->createDesign($this->createSessionMock(7), $categoryRepository);

        $this->assertSame('1column', $design->getDesignSettings($product)->getPageLayout());
    }

    /**
     * A product with no category of its own, so getDesignSettings() falls back to the session.
     */
    private function createProductMock(): Product
    {
        $product = $this->createMock(Product::class);
        $product->method('getCategory')->willReturn(null);
        $product->method('getCustomDesignDate')->willReturn([]);
        // getPageLayout() and getCustomLayoutUpdate() are magic getters.
        $product->method('__call')->willReturnCallback(
            static fn (string $method) => match ($method) {
                'getPageLayout' => '1column',
                default => [],
            }
        );

        return $product;
    }

    /**
     * getLastVisitedCategoryId() is a magic session getter, so it is stubbed through __call().
     */
    private function createSessionMock(?int $lastVisitedCategoryId): Session
    {
        $catalogSession = $this->createMock(Session::class);
        $catalogSession->method('__call')->willReturnCallback(
            static fn (string $method) => $method === 'getLastVisitedCategoryId' ? $lastVisitedCategoryId : null
        );

        return $catalogSession;
    }

    private function createDesign(Session $catalogSession, CategoryRepositoryInterface $categoryRepository): Design
    {
        return (new ObjectManager($this))->getObject(
            Design::class,
            [
                'localeDate' => $this->createMock(TimezoneInterface::class),
                'design' => $this->createMock(DesignInterface::class),
                'translator' => $this->createMock(TranslateInterface::class),
                'categoryLayoutManager' => $this->createMock(CategoryLayoutManager::class),
                'productLayoutManager' => $this->createMock(ProductLayoutManager::class),
                'catalogSession' => $catalogSession,
                'categoryRepository' => $categoryRepository,
            ]
        );
    }
}
