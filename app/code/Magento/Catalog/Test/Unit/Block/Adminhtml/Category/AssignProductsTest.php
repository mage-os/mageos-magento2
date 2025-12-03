<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Category;

use Magento\Catalog\Block\Adminhtml\Category\AssignProducts;
use Magento\Catalog\Block\Adminhtml\Category\Tab\Product as ProductGridBlock;
use Magento\Catalog\Model\Category;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Magento\Catalog\Block\Adminhtml\Category\AssignProducts
 */

class AssignProductsTest extends TestCase
{
    /** @var LayoutInterface|MockObject */
    private $layoutMock;

    /** @var Registry|MockObject */
    private $registryMock;

    /** @var EncoderInterface|MockObject */
    private $jsonEncoderMock;

    /** @var AssignProducts|MockObject */
    private $block;

    /**
     * Prepare SUT and collaborators for each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->layoutMock = $this->createMock(LayoutInterface::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->jsonEncoderMock = $this->createMock(EncoderInterface::class);

        // Create the SUT without running the parent constructor to avoid ObjectManager usage.
        $this->block = $this->getMockBuilder(AssignProducts::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLayout'])
            ->getMock();

        // Provide required collaborators to the SUT.
        $this->block->method('getLayout')->willReturn($this->layoutMock);
        $this->setProperty($this->block, 'registry', $this->registryMock);
        $this->setProperty($this->block, 'jsonEncoder', $this->jsonEncoderMock);
    }

    /**
     * Test grid block is created via layout on the first call.
     *
     * @return void
     */
    public function testGetBlockGridCreatesGridBlockOnFirstCall(): void
    {
        // Prepare the grid block returned by the layout.
        $gridBlockMock = $this->createMock(ProductGridBlock::class);
        $this->layoutMock
            ->expects($this->once())
            ->method('createBlock')
            ->with(ProductGridBlock::class, 'category.product.grid')
            ->willReturn($gridBlockMock);

        // Execute the method under test.
        $result = $this->block->getBlockGrid();

        // Verify the created block is returned.
        $this->assertSame($gridBlockMock, $result);
    }

    /**
     * Test grid block instance is cached on subsequent calls.
     *
     * @return void
     */
    public function testGetBlockGridReturnsCachedInstanceOnSubsequentCalls(): void
    {
        // Prepare the grid block returned by the layout.
        $gridBlockMock = $this->createMock(ProductGridBlock::class);
        $this->layoutMock
            ->expects($this->once())
            ->method('createBlock')
            ->with(ProductGridBlock::class, 'category.product.grid')
            ->willReturn($gridBlockMock);

        // Execute the method twice to exercise the cache.
        $first = $this->block->getBlockGrid();
        $second = $this->block->getBlockGrid();

        // Verify the same instance is returned both times.
        $this->assertSame($gridBlockMock, $first);
        $this->assertSame($gridBlockMock, $second);
    }

    /**
     * Test HTML output is delegated to the grid block's toHtml.
     *
     * @return void
     */
    public function testGetGridHtmlReturnsToHtmlOfGridBlock(): void
    {
        // Prepare the grid block and its HTML output.
        $gridBlockMock = $this->createMock(ProductGridBlock::class);
        $this->layoutMock
            ->method('createBlock')
            ->with(ProductGridBlock::class, 'category.product.grid')
            ->willReturn($gridBlockMock);
        $expectedHtml = '<div>grid</div>';
        $gridBlockMock
            ->expects($this->once())
            ->method('toHtml')
            ->willReturn($expectedHtml);

        // Execute the method under test.
        $html = $this->block->getGridHtml();

        // Verify the HTML matches the grid block output.
        $this->assertSame($expectedHtml, $html);
    }

    /**
     * Test products positions are encoded when category has products.
     *
     * @return void
     */
    public function testGetProductsJsonReturnsEncodedPositionsWhenNotEmpty(): void
    {
        // Prepare a category with product positions and expected JSON.
        $categoryMock = $this->createMock(Category::class);
        $positions = [10 => 1, 22 => 3];
        $this->registryMock->method('registry')->with('category')->willReturn($categoryMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn($positions);
        $encoded = '{"10":1,"22":3}';
        $this->jsonEncoderMock
            ->expects($this->once())
            ->method('encode')
            ->with($positions)
            ->willReturn($encoded);

        // Execute the method under test.
        $result = $this->block->getProductsJson();

        // Verify the positions were encoded to JSON.
        $this->assertSame($encoded, $result);
    }

    /**
     * Test empty JSON is returned when category has no product positions.
     *
     * @return void
     */
    public function testGetProductsJsonReturnsEmptyJsonWhenNoPositions(): void
    {
        // Prepare a category without product positions.
        $categoryMock = $this->createMock(Category::class);
        $this->registryMock->method('registry')->with('category')->willReturn($categoryMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn([]);
        $this->jsonEncoderMock->expects($this->never())->method('encode');

        // Execute the method under test.
        $result = $this->block->getProductsJson();

        // Verify an empty JSON object is returned.
        $this->assertSame('{}', $result);
    }

    /**
     * Test category is returned from registry when available.
     *
     * @return void
     */
    public function testGetCategoryReturnsCategoryFromRegistry(): void
    {
        // Prepare the category in the registry.
        $categoryMock = $this->createMock(Category::class);
        $this->registryMock
            ->expects($this->once())
            ->method('registry')
            ->with('category')
            ->willReturn($categoryMock);

        // Execute the method under test.
        $result = $this->block->getCategory();

        // Verify the category instance is returned.
        $this->assertSame($categoryMock, $result);
    }

    /**
     * Test null is returned when category is not present in registry.
     *
     * @return void
     */
    public function testGetCategoryReturnsNullWhenNotInRegistry(): void
    {
        // Prepare the registry to return null.
        $this->registryMock
            ->expects($this->once())
            ->method('registry')
            ->with('category')
            ->willReturn(null);

        // Execute the method under test.
        $result = $this->block->getCategory();

        // Verify null is returned.
        $this->assertNull($result);
    }

    /**
     * Inject a value into a protected/private property using reflection.
     *
     * @param object $object
     * @param string $property
     * @param mixed $value
     * @return void
     */
    private function setProperty(object $object, string $property, $value): void
    {
        $ref = new \ReflectionClass($object);
        $prop = $ref->getProperty($property);
        $prop->setAccessible(true);
        $prop->setValue($object, $value);
    }
}
