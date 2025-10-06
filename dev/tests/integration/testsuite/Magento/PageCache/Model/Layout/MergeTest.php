<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
namespace Magento\PageCache\Model\Layout;

use Magento\Framework\View\EntitySpecificHandlesList;

class MergeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @magentoAppArea frontend
     */
    public function testLoadEntitySpecificHandleWithEsiBlock()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Handle \'default\' must not contain blocks with \'ttl\' attribute specified');

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        // Mock cache to avoid layout being read from existing cache
        $cacheMock = $this->createMock(\Magento\Framework\Cache\FrontendInterface::class);
        /** @var \Magento\Framework\View\Model\Layout\Merge $layoutMerge */
        $layoutMerge = $objectManager->create(
            \Magento\Framework\View\Model\Layout\Merge::class,
            ['cache' => $cacheMock]
        );

        /** @var EntitySpecificHandlesList $entitySpecificHandleList */
        $entitySpecificHandleList = $objectManager->get(EntitySpecificHandlesList::class);

        // Register test layout file with ttl attribute
        $testHandle = 'test_layout_with_ttl';

        // Add this handle to entity-specific list to trigger validation
        $entitySpecificHandleList->addHandle($testHandle);

        // Manually add the layout XML from test fixture
        $layoutXml = file_get_contents(__DIR__ . '/../../_files/test_layout_with_ttl.xml');
        $layoutMerge->addUpdate($layoutXml);

        // This throws exception when loading
        // because test_layout_with_ttl is marked as entity-specific
        // and contains a block with ttl
        $layoutMerge->load([$testHandle]);
    }
}
