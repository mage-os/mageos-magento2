<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\BundleImportExport\Test\Unit\Helper;

use Magento\Bundle\Model\ResourceModel\Option\Collection as OptionCollection;
use Magento\Bundle\Model\ResourceModel\Selection\Collection as SelectionCollection;

/**
 * Test helper that emulates bundle type instance methods by
 * returning provided option and selection collections.
 */
class TypeInstanceTestHelper
{
    /**
     * Option collection to return for the product under test.
     *
     * @var OptionCollection
     */
    private $optionCollection;
    /**
     * Selection collection to return for the product under test.
     *
     * @var SelectionCollection
     */
    private $selectionCollection;
    /**
     * Option IDs to return from getOptionsIds.
     *
     * @var array
     */
    private $optionIds;

    /**
     * Initialize helper with prebuilt collections and option IDs.
     *
     * @param OptionCollection $optionCollection
     * @param SelectionCollection $selectionCollection
     * @param array $optionIds
     */
    public function __construct(OptionCollection $optionCollection, SelectionCollection $selectionCollection, array $optionIds)
    {
        $this->optionCollection = $optionCollection;
        $this->selectionCollection = $selectionCollection;
        $this->optionIds = $optionIds;
    }

    /**
     * Return the provided option collection.
     *
     * @param mixed $product
     * @return OptionCollection
     */
    public function getOptionsCollection($product)
    {
        return $this->optionCollection;
    }

    /**
     * No-op setter to mirror real type instance API.
     *
     * @param int $storeId
     * @param mixed $product
     * @return $this
     */
    public function setStoreFilter($storeId, $product)
    {
        return $this;
    }

    /**
     * Return the provided selection collection.
     *
     * @param array $optionIds
     * @param mixed $product
     * @return SelectionCollection
     */
    public function getSelectionsCollection($optionIds, $product)
    {
        return $this->selectionCollection;
    }

    /**
     * Return the provided option IDs.
     *
     * @param mixed $product
     * @return array
     */
    public function getOptionsIds($product)
    {
        return $this->optionIds;
    }
}
