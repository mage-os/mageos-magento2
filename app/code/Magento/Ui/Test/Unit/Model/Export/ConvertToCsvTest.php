<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Ui\Test\Unit\Model\Export;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface as DirectoryWriteInterface;
use Magento\Framework\Filesystem\File\WriteInterface as FileWriteInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\ConvertToCsv;
use Magento\Ui\Model\Export\MetadataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConvertToCsvTest extends TestCase
{
    /**
     * @var ConvertToCsv
     */
    protected $model;

    /**
     * @var DirectoryWriteInterface|MockObject
     */
    protected $directory;

    /**
     * @var Filesystem|MockObject
     */
    protected $filesystem;

    /**
     * @var Filter|MockObject
     */
    protected $filter;

    /**
     * @var MetadataProvider|MockObject
     */
    protected $metadataProvider;

    /**
     * @var FileWriteInterface|MockObject
     */
    protected $stream;

    /**
     * @var UiComponentInterface|MockObject
     */
    protected $component;

    protected function setUp(): void
    {
        $this->directory = $this->getMockBuilder(\Magento\Framework\Filesystem\Directory\WriteInterface::class)
            ->getMockForAbstractClass();

        $this->filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->filesystem->expects($this->any())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($this->directory);

        $this->filter = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadataProvider = $this->getMockBuilder(MetadataProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->component = $this->getMockBuilder(UiComponentInterface::class)
            ->getMockForAbstractClass();

        $this->stream = $this->getMockBuilder(\Magento\Framework\Filesystem\File\WriteInterface::class)
            ->onlyMethods([
                'lock',
                'unlock',
                'close',
            ])
            ->getMockForAbstractClass();

        $this->model = new ConvertToCsv(
            $this->filesystem,
            $this->filter,
            $this->metadataProvider
        );
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function testGetCsvFile()
    {
        $componentName = 'component_name';
        $data = ['data_value'];

        $document1 = $this->getMockBuilder(DocumentInterface::class)
            ->getMockForAbstractClass();

        $document2 = $this->getMockBuilder(DocumentInterface::class)
            ->getMockForAbstractClass();

        $this->mockComponent($componentName, [$document1], [$document2]);
        $this->mockFilter();
        $this->mockDirectory();

        $this->stream->expects($this->once())
            ->method('lock')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('unlock')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('close')
            ->willReturnSelf();
        $this->stream->expects($this->any())
            ->method('writeCsv')
            ->with($data)
            ->willReturnSelf();

        $this->metadataProvider->expects($this->once())
            ->method('getOptions')
            ->willReturn([]);
        $this->metadataProvider->expects($this->once())
            ->method('getHeaders')
            ->with($this->component)
            ->willReturn($data);
        $this->metadataProvider->expects($this->once())
            ->method('getFields')
            ->with($this->component)
            ->willReturn([]);
        $this->metadataProvider->expects($this->any())
            ->method('getRowData')
            ->willReturnCallback(
                function ($arg1, $arg2, $arg3) use ($document1, $document2, $data) {
                    if ($arg1 === $document1 && empty($arg2) && empty($arg3)) {
                        return $data;
                    } elseif ($arg1 === $document2 && empty($arg2) && empty($arg3)) {
                        return $data;
                    }
                }
            );
        $this->metadataProvider->expects($this->any())
            ->method('convertDate')
            ->willReturnCallback(
                function ($arg1, $arg2) use ($document1, $document2, $componentName) {
                    if ($arg1 === $document1 && $arg2 === $componentName) {
                        return null;
                    } elseif ($arg1 === $document2 && $arg2 === $componentName) {
                        return null;
                    }
                }
            );

        $result = $this->model->getCsvFile();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('rm', $result);
        $this->assertStringContainsString($componentName, $result['value']);
        $this->assertStringContainsString('.csv', $result['value']);
    }

    /**
     * @param array $expected
     */
    protected function mockStream($expected)
    {
        $this->stream = $this->getMockBuilder(\Magento\Framework\Filesystem\File\WriteInterface::class)
            ->onlyMethods([
                'lock',
                'unlock',
                'close',
            ])
            ->getMockForAbstractClass();

        $this->stream->expects($this->once())
            ->method('lock')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('unlock')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('close')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('writeCsv')
            ->with($expected)
            ->willReturnSelf();
    }

    /**
     * @param string $componentName
     * @param array $page1Items
     * @param array $page2Items
     */
    private function mockComponent(string $componentName, array $page1Items, array $page2Items): void
    {
        $context = $this->getMockBuilder(ContextInterface::class)
            ->onlyMethods(['getDataProvider'])
            ->getMockForAbstractClass();

        $dataProvider = $this->getMockBuilder(
            DataProviderInterface::class
        )
            ->onlyMethods(['getSearchResult'])
            ->getMockForAbstractClass();

        $searchResult0 = $this->getMockBuilder(SearchResultInterface::class)
            ->onlyMethods(['getItems'])
            ->getMockForAbstractClass();

        $searchResult1 = $this->getMockBuilder(SearchResultInterface::class)
            ->onlyMethods(['getItems'])
            ->getMockForAbstractClass();

        $searchResult2 = $this->getMockBuilder(SearchResultInterface::class)
            ->onlyMethods(['getItems'])
            ->getMockForAbstractClass();

        $searchCriteria = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->onlyMethods(['setPageSize', 'setCurrentPage'])
            ->getMockForAbstractClass();
        $this->component->expects($this->any())
            ->method('getName')
            ->willReturn($componentName);
        $this->component->expects($this->once())
            ->method('getContext')
            ->willReturn($context);

        $context->expects($this->once())
            ->method('getDataProvider')
            ->willReturn($dataProvider);

        $dataProvider->expects($this->exactly(2))
            ->method('getSearchResult')
            ->willReturnOnConsecutiveCalls($searchResult0, $searchResult1, $searchResult2);

        $dataProvider->expects($this->once())
            ->method('getSearchCriteria')
            ->willReturn($searchCriteria);

        $searchResult1->expects($this->any())
            ->method('setTotalCount');

        $searchResult2->expects($this->any())
            ->method('setTotalCount');

        $searchResult1->expects($this->any())
            ->method('getItems')
            ->willReturn($page1Items);

        $searchResult2->expects($this->any())
            ->method('getItems')
            ->willReturn($page2Items);

        $searchResult0->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(201);

        $searchCriteria->expects($this->exactly(3))
            ->method('setCurrentPage')
            ->willReturnCallback(
                function ($arg) use ($searchCriteria) {
                    if ($arg == 1 || $arg == 2 || $arg == 3) {
                        return $searchCriteria;
                    }
                }
            );

        $searchCriteria->expects($this->once())
            ->method('setPageSize')
            ->with(200)
            ->willReturnSelf();
    }

    protected function mockFilter()
    {
        $this->filter->expects($this->once())
            ->method('getComponent')
            ->willReturn($this->component);
        $this->filter->expects($this->once())
            ->method('prepareComponent')
            ->with($this->component)
            ->willReturnSelf();
        $this->filter->expects($this->once())
            ->method('applySelectionOnTargetProvider')
            ->willReturnSelf();
    }

    protected function mockDirectory()
    {
        $this->directory->expects($this->once())
            ->method('create')
            ->with('export')
            ->willReturnSelf();
        $this->directory->expects($this->once())
            ->method('openFile')
            ->willReturn($this->stream);
    }
}
