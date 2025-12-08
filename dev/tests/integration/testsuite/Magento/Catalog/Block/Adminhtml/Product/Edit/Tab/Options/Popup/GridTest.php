<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Popup;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Popup\Grid;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for the admin product grid in custom options popup
 *
 * @see \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Popup\Grid
 * @magentoAppArea adminhtml
 */
class GridTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Grid
     */
    private $block;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        Bootstrap::getInstance()->loadArea(FrontNameResolver::AREA_CODE);
        $this->objectManager = Bootstrap::getObjectManager();
        $this->layout = $this->objectManager->get(LayoutInterface::class);
        $this->block = $this->layout->createBlock(Grid::class);
    }

    /**
     * Test that getRowUrl returns null to disable JS click events
     *
     * @return void
     */
    public function testGetRowUrlReturnsNull(): void
    {
        /** @var Product $product */
        $product = $this->objectManager->create(Product::class);
        $product->setId(1);
        $product->setName('Test Product');

        $result = $this->block->getRowUrl($product);

        $this->assertNull($result, 'getRowUrl should return null to disable JS click events');
    }

    /**
     * Test that getRowUrl returns null for DataObject
     *
     * @return void
     */
    public function testGetRowUrlReturnsNullForDataObject(): void
    {
        $dataObject = $this->objectManager->create(\Magento\Framework\DataObject::class);
        $dataObject->setData(['id' => 1, 'name' => 'Test']);

        $result = $this->block->getRowUrl($dataObject);

        $this->assertNull($result);
    }

    /**
     * Test that specific columns are removed from the grid
     *
     * @magentoDbIsolation enabled
     * @return void
     */
    public function testPrepareColumnsRemovesSpecificColumns(): void
    {
        // Trigger column preparation
        $this->block->toHtml();

        // Verify that action, status, and visibility columns are removed
        // getColumn returns false when column doesn't exist
        $this->assertFalse(
            $this->block->getColumn('action'),
            'The action column should be removed'
        );
        $this->assertFalse(
            $this->block->getColumn('status'),
            'The status column should be removed'
        );
        $this->assertFalse(
            $this->block->getColumn('visibility'),
            'The visibility column should be removed'
        );

        // Verify that other essential columns still exist
        $this->assertNotFalse(
            $this->block->getColumn('entity_id'),
            'The entity_id column should exist'
        );
        $this->assertNotFalse(
            $this->block->getColumn('name'),
            'The name column should exist'
        );
        $this->assertNotFalse(
            $this->block->getColumn('sku'),
            'The sku column should exist'
        );
    }

    /**
     * Test that massaction is configured with import action
     *
     * @magentoDbIsolation enabled
     * @return void
     */
    public function testPrepareMassactionAddsImportAction(): void
    {
        // Trigger block preparation
        $this->block->toHtml();

        $massactionBlock = $this->block->getMassactionBlock();

        $this->assertNotNull($massactionBlock, 'Massaction block should exist');
        $this->assertEquals('entity_id', $this->block->getMassactionIdField());

        // Check that import action is available
        $importItem = $massactionBlock->getItem('import');
        $this->assertNotNull($importItem, 'Import action should be available in massaction');
        $this->assertEquals('Import', (string) $importItem->getLabel());
    }

    /**
     * Test that collection joins with options table to filter products
     *
     * @magentoDbIsolation enabled
     * @return void
     */
    public function testPrepareCollectionJoinsWithOptionsTable(): void
    {
        // Trigger block preparation which calls _prepareCollection
        $this->block->toHtml();

        $collection = $this->block->getCollection();

        $this->assertNotNull($collection, 'Collection should be set');

        // Verify the collection has the proper join - check that SELECT contains catalog_product_option
        $selectString = $collection->getSelect()->__toString();
        $this->assertStringContainsString(
            'catalog_product_option',
            $selectString,
            'Collection should join with catalog_product_option table'
        );
    }

    /**
     * Test that current product filter is applied to collection when parameter is set
     *
     * @magentoDbIsolation enabled
     * @return void
     */
    public function testPrepareCollectionAppliesCurrentProductFilter(): void
    {
        $testProductId = 999;

        // Simulate request with current_product_id parameter
        $request = $this->objectManager->get(\Magento\Framework\App\RequestInterface::class);
        $request->setParam('current_product_id', $testProductId);

        // Create a new block instance with the updated request
        $blockWithRequest = $this->layout->createBlock(Grid::class);
        $blockWithRequest->toHtml();

        $collection = $blockWithRequest->getCollection();

        // Verify the WHERE clause excludes the current product
        $selectString = $collection->getSelect()->__toString();
        $this->assertStringContainsString(
            (string)$testProductId,
            $selectString,
            'Collection should filter out current product ID'
        );
    }

    /**
     * Test that getGridUrl returns the correct URL for AJAX updates
     *
     * @return void
     */
    public function testGetGridUrlReturnsCorrectUrl(): void
    {
        $gridUrl = $this->block->getGridUrl();

        $this->assertNotEmpty($gridUrl, 'Grid URL should not be empty');
        $this->assertStringContainsString(
            'optionsimportgrid',
            $gridUrl,
            'Grid URL should contain "optionsimportgrid" action'
        );
    }

    /**
     * Test that grid has correct massaction form field name
     *
     * @magentoDbIsolation enabled
     * @return void
     */
    public function testMassactionFormFieldName(): void
    {
        $this->block->toHtml();

        $massactionBlock = $this->block->getMassactionBlock();

        $this->assertEquals(
            'product',
            $massactionBlock->getFormFieldName(),
            'Massaction form field name should be "product"'
        );
    }

    /**
     * Test grid block rendering does not throw exceptions
     *
     * @magentoDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoDbIsolation disabled
     * @return void
     */
    public function testGridBlockRendersWithoutException(): void
    {
        // This should not throw any exception
        $html = $this->block->toHtml();

        // Verify that HTML is generated
        $this->assertIsString($html);
        $this->assertNotEmpty($html);
    }

    /**
     * Test that grid collection is distinct (no duplicate products)
     *
     * @magentoDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoDbIsolation disabled
     * @return void
     */
    public function testCollectionIsDistinct(): void
    {
        $this->block->toHtml();
        $collection = $this->block->getCollection();

        $productIds = [];
        $hasDuplicates = false;

        foreach ($collection as $product) {
            if (in_array($product->getId(), $productIds)) {
                $hasDuplicates = true;
                break;
            }
            $productIds[] = $product->getId();
        }

        $this->assertFalse($hasDuplicates, 'Collection should not contain duplicate products');
    }
}
