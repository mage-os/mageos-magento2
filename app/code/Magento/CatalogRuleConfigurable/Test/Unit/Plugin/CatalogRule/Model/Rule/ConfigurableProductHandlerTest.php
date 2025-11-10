<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRuleConfigurable\Test\Unit\Plugin\CatalogRule\Model\Rule;

use Magento\CatalogRule\Model\Rule;
use Magento\CatalogRuleConfigurable\Plugin\CatalogRule\Model\Rule\ConfigurableProductHandler;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Magento\CatalogRuleConfigurable\Plugin\CatalogRule\Model\Rule\ConfigurableProductHandler
 */
class ConfigurableProductHandlerTest extends TestCase
{
    /**
     * @var ConfigurableProductHandler
     */
    private $configurableProductHandler;

    /**
     * @var Configurable|MockObject
     */
    private $configurableMock;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var AdapterInterface|MockObject
     */
    private $connectionMock;

    /**
     * @var Select|MockObject
     */
    private $selectMock;

    /** @var Rule|MockObject */
    private $ruleMock;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->configurableMock = $this->createPartialMock(
            Configurable::class,
            ['getChildrenIds', 'getParentIdsByChild']
        );
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);
        $this->selectMock = $this->createMock(Select::class);
        $this->ruleMock = $this->createMock(Rule::class);

        // Set up default resource connection behavior
        $this->resourceConnectionMock->method('getConnection')
            ->willReturn($this->connectionMock);
        $this->resourceConnectionMock->method('getTableName')
            ->willReturnCallback(fn($tableName) => $tableName);

        $this->configurableProductHandler = new ConfigurableProductHandler(
            $this->configurableMock,
            $this->resourceConnectionMock
        );

        // Reset static properties between tests
        $this->resetStaticProperties();
    }

    /**
     * Reset static properties in ConfigurableProductHandler
     *
     * @return void
     */
    private function resetStaticProperties(): void
    {
        $reflection = new \ReflectionClass(ConfigurableProductHandler::class);

        $allConfigurableProductIdsProperty = $reflection->getProperty('allConfigurableProductIds');
        $allConfigurableProductIdsProperty->setAccessible(true);
        $allConfigurableProductIdsProperty->setValue(null, null);

        $childrenProductsProperty = $reflection->getProperty('childrenProducts');
        $childrenProductsProperty->setAccessible(true);
        $childrenProductsProperty->setValue(null, []);
    }

    /**
     * Set up mock for configurable product IDs
     *
     * @param array $configurableProductIds
     * @return void
     */
    private function mockConfigurableProductIds(array $configurableProductIds): void
    {
        $this->selectMock->method('from')->willReturnSelf();
        $this->selectMock->method('where')->willReturnSelf();

        $this->connectionMock->method('select')
            ->willReturn($this->selectMock);
        $this->connectionMock->method('fetchCol')
            ->willReturn($configurableProductIds);
    }

    /**
     * @return void
     */
    public function testAroundGetMatchingProductIdsWithSimpleProduct()
    {
        $this->mockConfigurableProductIds([]);
        $this->configurableMock->expects($this->never())->method('getChildrenIds');
        $this->ruleMock->method('getProductsFilter')
            ->willReturn(null);

        $productIds = ['product' => 'valid results'];
        $this->assertEquals(
            $productIds,
            $this->configurableProductHandler->aroundGetMatchingProductIds(
                $this->ruleMock,
                function () {
                    return ['product' => 'valid results'];
                }
            )
        );
    }

    /**
     * @return void
     */
    public function testAroundGetMatchingProductIdsWithConfigurableProduct()
    {
        $this->mockConfigurableProductIds(['conf1', 'conf2']);
        $this->configurableMock->method('getChildrenIds')->willReturnMap([
            ['conf1', [0 => ['simple1']]],
            ['conf2', [0 => ['simple1', 'simple2']]],
        ]);
        $this->ruleMock->method('getProductsFilter')
            ->willReturn(null);

        $this->assertEquals(
            [
                'simple1' => [
                    0 => true,
                    1 => true,
                    3 => true,
                ],
                'simple2' => [
                    3 => true,
                ]
            ],
            $this->configurableProductHandler->aroundGetMatchingProductIds(
                $this->ruleMock,
                function () {
                    return [
                        'conf1' => [
                            0 => true,
                            1 => true,
                        ],
                        'conf2' => [
                            0 => false,
                            1 => false,
                            3 => true,
                            4 => false,
                        ],
                    ];
                }
            )
        );
    }

    /**
     * @param array $productsFilter
     * @param array $expectedProductsFilter
     * @param array $matchingProductIds
     * @param array $expectedMatchingProductIds
     * @return void
     * @dataProvider aroundGetMatchingProductIdsDataProvider
     */
    public function testAroundGetMatchingProductIdsWithProductsFilter(
        array $productsFilter,
        array $expectedProductsFilter,
        array $matchingProductIds,
        array $expectedMatchingProductIds
    ): void {
        $configurableProducts = [
            'conf1' => ['simple11', 'simple12'],
            'conf2' => ['simple21', 'simple22'],
        ];
        $this->mockConfigurableProductIds(array_keys($configurableProducts));
        $this->configurableMock->method('getChildrenIds')
            ->willReturnCallback(
                function ($id) use ($configurableProducts) {
                    return [0 => $configurableProducts[$id] ?? []];
                }
            );

        $this->configurableMock->method('getParentIdsByChild')
            ->willReturnCallback(
                function ($ids) use ($configurableProducts) {
                    $result = [];
                    foreach ($configurableProducts as $configurableProduct => $childProducts) {
                        if (array_intersect($ids, $childProducts)) {
                            $result[] = $configurableProduct;
                        }
                    }
                    return $result;
                }
            );

        $this->ruleMock->method('getProductsFilter')
            ->willReturn($productsFilter ?: null);

        $parentIds = [];
        foreach ($configurableProducts as $configurableProduct => $childProducts) {
            if (array_intersect($productsFilter, $childProducts)) {
                $parentIds[] = $configurableProduct;
            }
        }

        if (!empty($parentIds)) {
            $this->ruleMock->expects($this->once())
                ->method('setProductsFilter')
                ->with($this->callback(function ($arg) use ($expectedProductsFilter) {
                    sort($arg);
                    $expected = $expectedProductsFilter;
                    sort($expected);
                    return $arg === $expected;
                }));
        } else {
            $this->ruleMock->expects($this->never())
                ->method('setProductsFilter');
        }

        $this->assertEquals(
            $expectedMatchingProductIds,
            $this->configurableProductHandler->aroundGetMatchingProductIds(
                $this->ruleMock,
                function () use ($matchingProductIds) {
                    return $matchingProductIds;
                }
            )
        );
    }

    /**
     * @return array[]
     */
    public static function aroundGetMatchingProductIdsDataProvider(): array
    {
        return [
            [
                ['simple1',],
                ['simple1',],
                ['simple1' => [1 => false]],
                ['simple1' => [1 => false],],
            ],
            [
                ['simple11',],
                ['simple11', 'conf1',],
                ['simple11' => [1 => false], 'conf1' => [1 => true],],
                ['simple11' => [1 => true],],
            ],
            [
                ['simple11', 'simple12',],
                ['simple11', 'simple12', 'conf1',],
                ['simple11' => [1 => false], 'conf1' => [1 => true],],
                ['simple11' => [1 => true], 'simple12' => [1 => true],],
            ],
            [
                ['conf1', 'simple11', 'simple12'],
                ['conf1', 'simple11', 'simple12'],
                ['conf1' => [1 => true], 'simple11' => [1 => false], 'simple12' => [1 => false]],
                ['simple11' => [1 => true], 'simple12' => [1 => true]],
            ],
        ];
    }
}
