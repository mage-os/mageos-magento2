<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\Framework\ObjectManager\ResetAfterRequestInterface;

class ChildrenCategoriesProvider implements ResetAfterRequestInterface
{
    /**
     * @var array
     */
    protected $childrenIds = [];

    /**
     * Get Children Categories
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param boolean $recursive
     * @return \Magento\Catalog\Model\Category[]
     */
    public function getChildren(Category $category, $recursive = false)
    {
        return $category->isObjectNew() ? [] : $category->getResourceCollection()
            ->addAttributeToSelect('url_path')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addIdFilter($this->getChildrenIds($category, $recursive));
    }

    /**
     * Retrieve category children ids
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param boolean $recursive
     * @return int[]
     */
    public function getChildrenIds(Category $category, $recursive = false)
    {
        $cacheKey = $category->getId() . '_' . (int)$recursive;
        if (!isset($this->childrenIds[$cacheKey])) {
            $connection = $category->getResource()->getConnection();
            $select = $connection->select()
                ->from($category->getResource()->getEntityTable(), 'entity_id')
                ->where($connection->quoteIdentifier('path') . ' LIKE :c_path');
            $bind = ['c_path' => $category->getPath() . '/%'];
            if (!$recursive) {
                $select->where($connection->quoteIdentifier('level') . ' <= :c_level');
                $bind['c_level'] = $category->getLevel() + 1;
            }
            $this->childrenIds[$cacheKey] = $connection->fetchCol($select, $bind);
        }
        return $this->childrenIds[$cacheKey];
    }

    /**
     * @inheritDoc
     */
    public function _resetState(): void
    {
        $this->childrenIds = [];
    }
}
