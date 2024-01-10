<?php
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained from
 * Adobe.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Fixture;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\TestFramework\Fixture\Api\DataMerger;
use Magento\TestFramework\Fixture\DataFixtureInterface;

class ProductStock implements DataFixtureInterface
{
    private const DEFAULT_DATA = [
        'prod_id' => null,
        'prod_qty' => 1
    ];

    /**
     * @var DataObjectFactory
     */
    protected DataObjectFactory $dataObjectFactory;

    /**
     * @var StockRegistryInterface
     */
    protected StockRegistryInterface $stockRegistry;

    /**
     * @var DataMerger
     */
    protected DataMerger $dataMerger;

    /**
     * @param DataObjectFactory $dataObjectFactory
     * @param StockRegistryInterface $stockRegistry
     * @param DataMerger $dataMerger
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        StockRegistryInterface $stockRegistry,
        DataMerger $dataMerger
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->stockRegistry = $stockRegistry;
        $this->dataMerger = $dataMerger;
    }

    /**
     * {@inheritdoc}
     * @param array $data Parameters. Same format as ProductStock::DEFAULT_DATA
     */
    public function apply(array $data = []): ?DataObject
    {
        $data = $this->dataMerger->merge(self::DEFAULT_DATA, $data);
        $stockItem = $this->stockRegistry->getStockItem($data['prod_id']);
        $stockItem->setData('is_in_stock', 1);
        $stockItem->setData('qty', 90);
        $stockItem->setData('manage_stock', 1);
        $stockItem->save();

        return $this->dataObjectFactory->create(['data' => [$data]]);
    }
}
