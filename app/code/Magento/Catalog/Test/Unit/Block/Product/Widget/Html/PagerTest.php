<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Product\Widget\Html;

use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Url;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PagerTest extends TestCase
{
    /**
     * @var Pager
     */
    private Pager $pager;
    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;
    /**
     * @var AbstractCollection|MockObject
     */
    private $collectionMock;
    /**
     * @var Url|MockObject
     */
    private $urlBuilderMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $contextMock = $this->createMock(Context::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->urlBuilderMock = $this->createMock(Url::class);
        $selectMock = $this->createMock(Select::class);
        $this->collectionMock = $this->getMockBuilder(AbstractCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSelect', 'getSize'])
            ->getMock();

        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->collectionMock->method('getSelect')->willReturn($selectMock);

        $this->pager = $objectManager->getObject(
            Pager::class,
            [
                'context' => $contextMock,
            ]
        );
    }

    /**
     * Unit test for getCollectionSize()
     *
     * @dataProvider collectionSizeDataProvider
     * @param int $collectionSize
     * @param int $totalLimit
     * @param int $expectedResult
     * @return void
     */
    public function testGetCollectionSize(
        int $collectionSize,
        int $totalLimit,
        int $expectedResult
    ): void {
        $this->collectionSizeHelper($totalLimit, $collectionSize);
        $this->assertSame($expectedResult, $this->pager->getCollectionSize());
    }

    /**
     * Unit test for getTotalNum()
     *
     * Reuses collectionSizeDataProvider to cover scenarios where total limit affects total number.
     *
     * @dataProvider collectionSizeDataProvider
     * @param int $collectionSize
     * @param int $totalLimit
     * @param int $expectedResult
     * @return void
     */
    public function testGetTotalNum(int $collectionSize, int $totalLimit, int $expectedResult): void
    {
        $this->collectionSizeHelper($totalLimit, $collectionSize);
        $this->assertSame($expectedResult, $this->pager->getTotalNum());
    }

    /**
     * Helper method to set up collection size mock and pager total limit.
     *
     * @param int $totalLimit
     * @param int $collectionSize
     * @return void
     */
    private function collectionSizeHelper(int $totalLimit, int $collectionSize): void
    {
        $this->collectionMock->expects($this->once())->method('getSize')->willReturn($collectionSize);

        $this->pager->setTotalLimit($totalLimit);
        $this->pager->setCollection($this->collectionMock);
    }

    /**
     * Data provider for testGetCollectionSize()
     *
     * @return array
     */
    public static function collectionSizeDataProvider(): array
    {
        return [
            'no_total_limit_cache' => [5, 0, 5],
            'total_limit_smaller' => [10, 6, 6],
            'total_limit_larger' => [4, 6, 4],
        ];
    }

    /**
     * Unit test for getLastNum()
     *
     * @return void
     */
    public function testGetLastNum(): void
    {
        $selectMock = $this->createMock(Select::class);
        $collection = $this->getMockBuilder(AbstractCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSelect', 'setPageSize', 'setCurPage', 'count', 'getSize'])
            ->getMock();

        $collection->method('setPageSize')->willReturnSelf();
        $collection->method('setCurPage')->willReturnSelf();
        $collection->method('getSelect')->willReturn($selectMock);
        $collection->method('count')->willReturn(5);
        $collection->method('getSize')->willReturn(20);
        $this->requestMock->method('getParam')->willReturn(2);

        $this->pager->setLimit(10);
        $this->pager->setCollection($collection);

        $this->assertSame(15, $this->pager->getLastNum());
    }

    /**
     * Unit test for isFirstPage()
     *
     * @return void
     */
    public function testIsFirstPage(): void
    {
        $this->pager->setCollection($this->collectionMock);
        $this->assertTrue($this->pager->isFirstPage());
    }

    /**
     * Unit test for isFirstPage() when current page is not the first
     *
     * @return void
     */
    public function testIsFirstPageReturnsFalseWhenCurrentPageIsTwo(): void
    {
        $this->setProtectedProperty('_currentPage', 2);
        $this->assertFalse($this->pager->isFirstPage());
    }

    /**
     * Unit test for isLastPage()
     *
     * @dataProvider pageSizeDataProvider
     * @param int $pageSize
     * @param bool $expectedResult
     * @return void
     */
    public function testIsLastPage($pageSize, $expectedResult): void
    {
        $this->collectionMock->method('getSize')->willReturn($pageSize);
        $this->pager->setCollection($this->collectionMock);
        $this->assertSame($expectedResult, $this->pager->isLastPage());
    }

    /**
     * Data provider for testIsLastPage()
     *
     * @return array
     */
    public static function pageSizeDataProvider(): array
    {
        return [
            [0, true],
            [10, true],
            [20, false]
        ];
    }

    /**
     * Unit test for getPages()
     *
     * @return void
     */
    public function testGetPagesReturnsSinglePageForSmallCollection(): void
    {
        $this->pager->setCollection($this->collectionMock);
        $result = $this->pager->getPages();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    /**
     * Unit test for getPages()
     *
     * @return void
     */
    public function testGetPagesReturnsMultiplePagesForLargeCollection(): void
    {
        $this->collectionMock->method('getSize')->willReturn(60);

        $this->pager->setCollection($this->collectionMock);
        $result = $this->pager->getPages();

        $this->assertIsArray($result);
        $this->assertCount(5, $result);
    }

    /**
     * Verify _initFrame method with different current page and last page number.
     *
     * @dataProvider initFramesDataProvider
     * @param int $curPage
     * @param int $lastPageNo
     * @param int $frameStart
     * @param int $frameEnd
     * @return void
     */
    public function testInitFrame(int $curPage, int $lastPageNo, int $frameStart, int $frameEnd): void
    {
        $pager = $this->getMockBuilder(Pager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLastPageNum', 'getCurrentPage'])
            ->getMock();

        $pager->expects($this->any())->method('getCurrentPage')->willReturn($curPage);
        $pager->expects($this->any())->method('getLastPageNum')->willReturn($lastPageNo);

        $reflection = new \ReflectionClass($pager);
        $initFrameMethod = $reflection->getMethod('_initFrame');
        $initFrameMethod->setAccessible(true);
        $initFrameMethod->invoke($pager);

        $this->assertEquals($frameStart, $pager->getFrameStart());
        $this->assertEquals($frameEnd, $pager->getFrameEnd());
        $this->assertTrue($pager->isFrameInitialized());
    }

    /**
     * Data provider for testInitFrame()
     *
     * @return array
     */
    public static function initFramesDataProvider(): array
    {
        return [
            'current-page-empty' => [0, 5, 1, 5],
            'current-page-in-middle' => [5, 10, 3, 7],
            'current-page-near-start' => [2, 10, 1, 5],
            'current-page-near-end' => [9, 10, 6, 10],
        ];
    }

    /**
     * Unit test for getCurrentPage() using data provider
     *
     * @dataProvider getCurrentPageDataProvider
     * @param array $paramReturns
     * @param int $collectionSize
     * @param int $limit
     * @param int|float $expectedFirst
     * @param int|null $expectedSecond
     * @return void
     */
    public function testGetCurrentPageWithDataProvider(
        array $paramReturns,
        int   $collectionSize,
        int   $limit,
        int   $expectedFirst,
        ?int  $expectedSecond
    ): void {
        if (count($paramReturns) > 1) {
            $this->requestMock->method('getParam')->willReturnOnConsecutiveCalls(...$paramReturns);
        } else {
            $this->requestMock->method('getParam')->willReturn($paramReturns[0]);
        }

        $this->collectionMock->method('getSize')->willReturn($collectionSize);
        $this->pager->setLimit($limit);
        $this->pager->setCollection($this->collectionMock);

        $firstResult = $this->pager->getCurrentPage();
        $this->assertEquals($expectedFirst, $firstResult);

        if ($expectedSecond !== null) {
            $secondResult = $this->pager->getCurrentPage();
            $this->assertSame($expectedSecond, $secondResult);
        }
    }

    /**
     * Data provider for testGetCurrentPageWithDataProvider()
     *
     * @return array
     */
    public static function getCurrentPageDataProvider(): array
    {
        return [
            'param_invalid_defaults' => [[0], 20, 10, 1, null],
            'param_valid_within_range' => [[2], 100, 10, 2, null],
            'param_greater_clamped_to_last' => [[5], 20, 10, 2, null],
            'cached_between_calls' => [[3, 4], 100, 10, 3, 3],
        ];
    }

    /**
     * Unit test for getLimit() when limit is already set
     *
     * @return void
     */
    public function testGetLimitReturnsSetLimitWhenLimitIsAlreadySet(): void
    {
        // When limit is explicitly set on the pager, getLimit() should return it
        $this->pager->setLimit(25);
        $this->assertSame(25, $this->pager->getLimit());
    }

    /**
     * Unit test for getLimit() when request param corresponds to a valid available limit
     *
     * @return void
     */
    public function testGetLimitReturnsRequestParamIfInAvailableLimits(): void
    {
        // When request param corresponds to a valid available limit, it should be returned
        $available = $this->pager->getAvailableLimit();
        $this->assertNotEmpty($available, 'Available limits should not be empty for this test');

        $keys = array_keys($available);
        $chosenKey = $keys[count($keys) - 1];

        $this->requestMock->method('getParam')->willReturn($chosenKey);

        $this->assertSame($chosenKey, $this->pager->getLimit());
    }

    /**
     * Unit test for getLimit() when request param is invalid
     *
     * @return void
     */
    public function testGetLimitReturnsDefaultWhenRequestParamInvalid(): void
    {
        // When request param is invalid or missing, getLimit() should return the first available limit
        $available = $this->pager->getAvailableLimit();
        $this->assertNotEmpty($available, 'Available limits should not be empty for this test');

        $this->requestMock->method('getParam')->willReturn('non-existing-key');

        $expectedDefault = current(array_keys($available));
        $this->assertSame($expectedDefault, $this->pager->getLimit());
    }

    /**
     * Unit test for getFirstNum() using data provider
     *
     * @dataProvider getFirstNumDataProvider
     * @param int $limit
     * @param int $collectionSize
     * @param int $requestPage
     * @param int $expectedFirstNum
     * @return void
     */
    public function testGetFirstNumWithDataProvider(
        int $limit,
        int $collectionSize,
        int $requestPage,
        int $expectedFirstNum
    ): void {
        $this->requestMock->method('getParam')->willReturn($requestPage);
        $this->collectionMock->method('getSize')->willReturn($collectionSize);

        $this->pager->setLimit($limit);
        $this->pager->setCollection($this->collectionMock);

        $this->assertEquals($expectedFirstNum, $this->pager->getFirstNum());
    }

    /**
     * Data provider for testGetFirstNumWithDataProvider()
     *
     * Each case: [limit, collectionSize, requestPage, expectedFirstNum]
     *
     * @return array
     */
    public static function getFirstNumDataProvider(): array
    {
        return [
            // Normal case: limit 10, page 3 => first = 10*(3-1)+1 = 21
            'normal_middle_page' => [10, 100, 3, 21],
            // Requested page greater than last page:limit 10,collectionSize 15 -> lastPage=2, first = 10*(2-1)+1 = 11
            'clamp_to_last_page' => [10, 15, 5, 11],
            // Zero collection size: should treat last page as 1 -> first = 1
            'zero_collection' => [10, 0, 2, 1],
            // limit 1: page 5 => first = 1*(5-1)+1 = 5
            'limit_one' => [1, 10, 5, 5],
        ];
    }

    /**
     * Unit test for getPreviousPageUrl() with data provider
     *
     * @dataProvider previousPageUrlDataProvider
     * @param int $currentPage
     * @param int|null $expectedQueryValue
     * @return void
     */
    public function testGetPreviousPageUrlWithDataProvider(int $currentPage, ?int $expectedQueryValue): void
    {
        $expectedUrl = 'http://example.com/prev';

        $this->setProtectedProperty('_currentPage', $currentPage);
        $this->mockUrlBuilder($expectedQueryValue, $expectedUrl);

        $this->assertSame($expectedUrl, $this->pager->getPreviousPageUrl());
    }

    /**
     * Data provider for testGetPreviousPageUrlWithDataProvider
     *
     * @return array
     */
    public static function previousPageUrlDataProvider(): array
    {
        return [
            'on_first_page_zero_prev' => [1, null],
            'on_second_page_prev_null' => [2, null],
            'on_third_page_prev_two' => [3, 2],
            'on_fifth_page_prev_four' => [5, 4],
        ];
    }

    /**
     * Data-driven test for getNextPageUrl()
     *
     * @dataProvider nextPageUrlDataProvider
     * @param int $currentPage
     * @param int $expectedQueryValue
     * @return void
     */
    public function testGetNextPageUrlWithDataProvider(int $currentPage, int $expectedQueryValue): void
    {
        $expectedUrl = 'http://example.com/next';

        $this->setProtectedProperty('_currentPage', $currentPage);
        $this->mockUrlBuilder($expectedQueryValue, $expectedUrl);

        $this->assertSame($expectedUrl, $this->pager->getNextPageUrl());
    }

    /**
     * Data provider for testGetNextPageUrlWithDataProvider
     *
     * @return array
     */
    public static function nextPageUrlDataProvider(): array
    {
        return [
            'current_1_next_2' => [1, 2],
            'current_2_next_3' => [2, 3],
            'current_3_next_4' => [3, 4],
            'current_5_next_6' => [5, 6],
        ];
    }

    /**
     * Data-driven test for getLastPageUrl()
     *
     * @dataProvider lastPageUrlDataProvider
     * @param int $lastPage
     * @param int|null $expectedQueryValue
     * @return void
     */
    public function testGetLastPageUrlWithDataProvider(int $lastPage, ?int $expectedQueryValue): void
    {
        $expectedUrl = 'http://example.com/last';

        $this->setProtectedProperty('_lastPage', $lastPage);
        $this->mockUrlBuilder($expectedQueryValue, $expectedUrl);

        $this->assertSame($expectedUrl, $this->pager->getLastPageUrl());
    }

    /**
     * Data provider for testGetLastPageUrlWithDataProvider
     *
     * @return array
     */
    public static function lastPageUrlDataProvider(): array
    {
        return [
            'last_page_one' => [1, null],
            'last_page_two' => [2, 2],
            'last_page_five' => [5, 5],
        ];
    }

    /**
     * Helper method to set protected property on pager instance
     *
     * @param string $property
     * @param mixed $currentPage
     * @return void
     */
    private function setProtectedProperty($property, $currentPage): void
    {
        $reflection = new \ReflectionProperty($this->pager, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($this->pager, $currentPage);
    }

    /**
     * Helper method to mock url builder behavior for pagination urls
     *
     * @param int|null $expectedQueryValue
     * @param string $expectedReturnUrl
     * @return void
     */
    private function mockUrlBuilder(?int $expectedQueryValue, string $expectedReturnUrl): void
    {
        $pageVar = $this->pager->getPageVarName();

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->anything(),
                $this->callback(function ($args) use ($pageVar, $expectedQueryValue) {
                    if (!isset($args['_query']) || !array_key_exists($pageVar, $args['_query'])) {
                        return false;
                    }
                    return $args['_query'][$pageVar] === $expectedQueryValue;
                })
            )
            ->willReturn($expectedReturnUrl);
    }
}
