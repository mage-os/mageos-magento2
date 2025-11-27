<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
namespace Magento\Catalog\Model\ResourceModel\Category;

use Magento\Catalog\Model\Category;

/**
 * Aggregate count for parent category after deleting child category
 *
 * Class AggregateCount
 */
class AggregateCount
{
    /**
     * Reduces children count for parent categories
     *
     * @param Category $category
     * @return void
     */
    public function processDelete(Category $category)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category $resourceModel */
        $resourceModel = $category->getResource();
        /**
         * Update children count for all parent categories
         */
        $parentIds = $category->getParentIds();
        $childrenCount = $this->getCategoryRowCount($resourceModel, $category->getId());
        if ($parentIds) {
            $data = ['children_count' => new \Zend_Db_Expr('children_count - '.$childrenCount)];
            $where = ['entity_id IN(?)' => $parentIds];
            $resourceModel->getConnection()->update($resourceModel->getEntityTable(), $data, $where);
        }
    }

    /**
     * To get count of rows (category count) for a specific entity ID
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category $resourceModel
     * @param int $categoryId
     * @return int
     */
    public function getCategoryRowCount(
        \Magento\Catalog\Model\ResourceModel\Category $resourceModel,
        $categoryId
    ): int {
        $connection = $resourceModel->getConnection();
        $table      = $resourceModel->getEntityTable();

        // Tell staging NOT to add created_in / updated_in filters
        $select = $connection->select();
        $select->setPart('disable_staging_preview', true);

        $select->from($table, ['cnt' => new \Zend_Db_Expr('COUNT(*)')])
            ->where('entity_id = ?', (int)$categoryId);

        return (int)$connection->fetchOne($select);
    }


}
