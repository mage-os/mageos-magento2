<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Product\ProductList;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Block\Product\ProductList\Related;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class RelatedTest extends TestCase
{
    /**
     * @var Related
     */
    protected $block;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->block = $objectManager->getObject(Related::class);
    }

    protected function tearDown(): void
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $productTag = ['compare_item_1'];
        $product = $this->createMock(Product::class);
        $product->expects($this->once())->method('getIdentities')->willReturn($productTag);

        $itemsCollection = new \ReflectionProperty(
            Related::class,
            '_itemCollection'
        );
        $itemsCollection->setAccessible(true);
        $itemsCollection->setValue($this->block, [$product]);

        $this->assertEquals(
            $productTag,
            $this->block->getIdentities()
        );
    }

    /**
     * @param bool $isComposite
     * @param bool $isSaleable
     * @param bool $hasRequiredOptions
     * @param bool $canItemsAddToCart
     */
    #[DataProvider('canItemsAddToCartDataProvider')]
    public function testCanItemsAddToCart($isComposite, $isSaleable, $hasRequiredOptions, $canItemsAddToCart)
    {
        $product = new class extends Product {
            private $isComposite = false;
            private $isSaleable = false;
            private $hasRequiredOptions = false;
            
            public function __construct()
            {
                // Empty constructor for test
            }
            
            public function isComposite()
            {
                return $this->isComposite;
            }
            
            public function isSaleable()
            {
                return $this->isSaleable;
            }
            
            public function getRequiredOptions()
            {
                return $this->hasRequiredOptions;
            }
            
            public function setIsComposite($isComposite)
            {
                $this->isComposite = $isComposite;
                return $this;
            }
            
            public function setIsSaleable($isSaleable)
            {
                $this->isSaleable = $isSaleable;
                return $this;
            }
            
            public function setHasRequiredOptions($hasRequiredOptions)
            {
                $this->hasRequiredOptions = $hasRequiredOptions;
                return $this;
            }
        };
        $product->setIsComposite($isComposite);
        $product->setIsSaleable($isSaleable);
        $product->setHasRequiredOptions($hasRequiredOptions);

        $itemsCollection = new \ReflectionProperty(
            Related::class,
            '_itemCollection'
        );
        $itemsCollection->setAccessible(true);
        $itemsCollection->setValue($this->block, [$product]);

        $this->assertEquals(
            $canItemsAddToCart,
            $this->block->canItemsAddToCart()
        );
    }

    /**
     * @return array
     */
    public static function canItemsAddToCartDataProvider()
    {
        return [
            [false, true, false, true],
            [false, false, false, false],
            [true, false, false, false],
            [true, false, true, false],
        ];
    }
}
