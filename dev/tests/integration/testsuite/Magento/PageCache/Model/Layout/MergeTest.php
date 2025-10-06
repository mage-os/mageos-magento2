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
        $this->expectExceptionMessage('must not contain blocks with \'ttl\' attribute specified');

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

        // Register test layout with ttl attribute
        $testHandle = 'test_entity_specific_handle';

        // Add this handle to entity-specific list to trigger validation
        $entitySpecificHandleList->addHandle($testHandle);

        // Layout XML with ttl attribute (without XML declaration to avoid parsing issues)
        $layoutXml = <<<XML
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <block class="Magento\Framework\View\Element\Template" name="test.block.with.ttl" template="Magento_Theme::html/notices.phtml" ttl="3600"/>
    </body>
</page>
XML;

        $layoutMerge->addUpdate($layoutXml);

        // This should throw exception when loading because test_entity_specific_handle 
        // is marked as entity-specific and contains a block with ttl
        $layoutMerge->load([$testHandle]);
    }
}
