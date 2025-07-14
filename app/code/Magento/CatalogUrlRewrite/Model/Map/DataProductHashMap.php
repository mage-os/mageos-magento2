<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogUrlRewrite\Model\Map;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManager\ResetAfterRequestInterface;

/**
 * Map that holds data for products ids from a category and subcategories
 */
class DataProductHashMap implements HashMapInterface, ResetAfterRequestInterface
{
    /**
     * @var int[]
     */
    private $hashMap = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\Map\HashMapPool
     */
    private $hashMapPool;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $connection;

    /**
     * @param CollectionFactory $collectionFactory
     * @param HashMapPool $hashMapPool
     * @param ResourceConnection $connection
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        HashMapPool $hashMapPool,
        ResourceConnection $connection
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->hashMapPool = $hashMapPool;
        $this->connection = $connection;
    }

    /**
     * Returns an array of ids of all visible products and assigned to a category and all its subcategories
     *
     * @param int $categoryId
     * @return array
     */
    public function getAllData($categoryId)
    {
        if (!isset($this->hashMap[$categoryId])) {
            $productsCollection = $this->collectionFactory->create();
            $productsCollection->getSelect()
                ->joinInner(
                    ['cp' => $this->connection->getTableName('catalog_category_product')],
                    'cp.product_id = e.entity_id',
                    []
                )
                ->where(
                    $productsCollection->getConnection()->prepareSqlCondition(
                        'cp.category_id',
                        [
                            'in' => $this->hashMapPool->getDataMap(
                                DataCategoryHashMap::class,
                                $categoryId
                            )->getAllData($categoryId)
                        ]
                    )
                )->group('e.entity_id');
            $this->hashMap[$categoryId] = $productsCollection->getAllIds();
        }
        return $this->hashMap[$categoryId];
    }

    /**
     * @inheritdoc
     */
    public function getData($categoryId, $key)
    {
        $categorySpecificData = $this->getAllData($categoryId);
        if (isset($categorySpecificData[$key])) {
            return $categorySpecificData[$key];
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function resetData($categoryId)
    {
        $this->hashMapPool->resetMap(DataCategoryHashMap::class, $categoryId);
        unset($this->hashMap[$categoryId]);
    }

    /**
     * @inheritDoc
     */
    public function _resetState(): void
    {
        $this->hashMap = [];
    }
}
