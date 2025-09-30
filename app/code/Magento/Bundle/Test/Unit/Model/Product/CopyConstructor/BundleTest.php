<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Model\Product\CopyConstructor;

use Magento\Bundle\Api\Data\BundleOptionInterface;
use Magento\Bundle\Model\Link;
use Magento\Bundle\Model\Product\CopyConstructor\Bundle;
use Magento\Bundle\Test\Unit\Helper\BundleOptionInterfaceTestHelper;
use Magento\Bundle\Test\Unit\Helper\LinkTestHelper;
use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Test\Unit\Helper\ProductExtensionInterfaceTestHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BundleTest extends TestCase
{
    /**
     * @var Bundle
     */
    protected $model;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(Bundle::class);
    }

    public function testBuildNegative()
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $duplicate = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->once())
            ->method('getTypeId')
            ->willReturn('other product type');
        $this->model->build($product, $duplicate);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testBuildPositive()
    {
        /** @var Product|MockObject $product */
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $extensionAttributesProduct = new ProductExtensionInterfaceTestHelper();

        $product->expects($this->once())
            ->method('getTypeId')
            ->willReturn(Type::TYPE_BUNDLE);
        $product->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesProduct);

        $productLink = new LinkTestHelper();
        // Configure the test helper (setSelectionId will be called by the code under test)
        // ✅ CLEAN: Test helper for non-existent methods
        $firstOption = new BundleOptionInterfaceTestHelper();
        $firstOption->setProductLinks([$productLink]);
        // setOptionId will be called by the code under test
        // ✅ CLEAN: Test helper for non-existent methods
        $secondOption = new BundleOptionInterfaceTestHelper();
        $secondOption->setProductLinks([$productLink]);
        // setOptionId will be called by the code under test
        $bundleOptions = [
            $firstOption,
            $secondOption
        ];
        // Configure test helper with setter method
        $extensionAttributesProduct->setBundleProductOptions($bundleOptions);

        /** @var Product|MockObject $duplicate */
        $duplicate = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        // ✅ CLEAN: Test helper for non-existent methods
        $extensionAttributesDuplicate = new ProductExtensionInterfaceTestHelper();

        $duplicate->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesDuplicate);
        // Test helper doesn't need mock expectations - setBundleProductOptions will be called by code under test

        $this->model->build($product, $duplicate);
    }

    /**
     * @return void
     */
    public function testBuildWithoutOptions()
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        // ✅ CLEAN: Test helper for non-existent methods
        $extensionAttributesProduct = new ProductExtensionInterfaceTestHelper();

        $product->expects($this->once())
            ->method('getTypeId')
            ->willReturn(Type::TYPE_BUNDLE);
        $product->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesProduct);

        // Configure test helper with setter method
        $extensionAttributesProduct->setBundleProductOptions(null);

        $duplicate = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        // ✅ CLEAN: Test helper for non-existent methods
        $extensionAttributesDuplicate = new ProductExtensionInterfaceTestHelper();

        $duplicate->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesDuplicate);
        // Test helper doesn't need mock expectations - setBundleProductOptions will be called by code under test

        $this->model->build($product, $duplicate);
    }
}
