<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Model\Autocomplete;

use Magento\CatalogSearch\Model\Autocomplete\DataProvider;
use Magento\CatalogSearch\Test\Unit\Helper\ItemTestHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Search\Model\Autocomplete\Item;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\Search\Model\Query;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\ResourceModel\Query\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataProviderTest extends TestCase
{
    /**
     * @var DataProvider
     */
    private $model;

    /**
     * @var Query|MockObject
     */
    private $query;

    /**
     * @var ItemFactory|MockObject
     */
    private $itemFactory;

    /**
     * @var Collection|MockObject
     */
    private $suggestCollection;

    /**
     * @var integer
     */
    private $limit = 3;

    protected function setUp(): void
    {
        $helper = new ObjectManager($this);

        $this->suggestCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIterator'])
            ->getMock();

        $this->query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getQueryText', 'getSuggestCollection'])
            ->getMock();
        $this->query->method('getSuggestCollection')->willReturn($this->suggestCollection);

        $queryFactory = $this->getMockBuilder(QueryFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();
        $queryFactory->method('get')->willReturn($this->query);

        $this->itemFactory = $this->getMockBuilder(ItemFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn($this->limit);

        $this->model = $helper->getObject(
            DataProvider::class,
            [
                'queryFactory' => $queryFactory,
                'itemFactory' => $this->itemFactory,
                'scopeConfig' => $scopeConfig
            ]
        );
    }

    public function testGetItems()
    {
        $queryString = 'string';
        $expected = ['title' => $queryString, 'num_results' => 100500];
        $collection = [
            ['query_text' => 'string1', 'num_results' => 1],
            ['query_text' => 'string2', 'num_results' => 2],
            ['query_text' => 'string11', 'num_results' => 11],
            ['query_text' => 'string100', 'num_results' => 100],
            ['query_text' => $queryString, 'num_results' => 100500]
        ];
        $this->buildCollection($collection);
        $this->query->expects($this->once())
            ->method('getQueryText')
            ->willReturn($queryString);

        $itemMock =  $this->getMockBuilder(ItemTestHelper::class)
            ->onlyMethods(['getTitle', 'toArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $itemMock->setTitleSequence([
            $queryString,
            'string1',
            'string2',
            'string11',
            'string100'
        ]);
        $itemMock->method('toArray')->willReturn($expected);

        $this->itemFactory->method('create')->willReturn($itemMock);

        $result = $this->model->getItems();
        $this->assertEquals($expected, $result[0]->toArray());
        $this->assertCount($this->limit, $result);
    }

    /**
     * @param array $data
     */
    private function buildCollection(array $data)
    {
        $collectionData = [];
        foreach ($data as $collectionItem) {
            $collectionData[] = new DataObject($collectionItem);
        }
        $this->suggestCollection->method('getIterator')->willReturn(new \ArrayIterator($collectionData));
    }

    public function testGetItemsWithEmptyQueryText()
    {
        $this->query->expects($this->once())
            ->method('getQueryText')
            ->willReturn('');
        $this->query->expects($this->never())
            ->method('getSuggestCollection');
        $this->itemFactory->expects($this->never())
            ->method('create');
        $result = $this->model->getItems();
        $this->assertEmpty($result);
    }
}
