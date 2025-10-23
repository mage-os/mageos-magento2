<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\Framework\Indexer\IndexerInterface;

/**
 * Mock class for IndexerInterface with additional methods
 */
class IndexerInterfaceMock implements IndexerInterface
{
    private $id = null;
    private $state = null;

    /**
     * Mock method for __wakeup
     *
     * @return void
     */
    public function __wakeup()
    {
        // Mock implementation
    }

    /**
     * Mock method for getId
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id value
     *
     * @param string|null $value
     * @return $this
     */
    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Mock method for getState
     *
     * @return \Magento\Framework\Indexer\StateInterface|null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the state value
     *
     * @param \Magento\Framework\Indexer\StateInterface|null $value
     * @return $this
     */
    public function setState($value)
    {
        $this->state = $value;
        return $this;
    }

    // Required methods from IndexerInterface
    public function load($indexId)
    {
        return $this;
    }
    public function reindexAll()
    {
        return $this;
    }
    public function reindexRow($id)
    {
        return $this;
    }
    public function reindexList($ids)
    {
        return $this;
    }
    public function getTitle()
    {
        return null;
    }
    public function getDescription()
    {
        return null;
    }
    public function getFieldsByEntity()
    {
        return [];
    }
    public function getSource()
    {
        return null;
    }
    public function getView()
    {
        return null;
    }
    public function getActionClass()
    {
        return null;
    }
    public function isScheduled()
    {
        return false;
    }
    public function setScheduled($scheduled)
    {
        return $this;
    }
    public function getViewId()
    {
        return null;
    }
    public function getFields()
    {
        return [];
    }
    public function getSources()
    {
        return [];
    }
    public function getViews()
    {
        return [];
    }
    public function getDimensionProvider()
    {
        return null;
    }
    public function getDimensionMode()
    {
        return null;
    }
    public function setDimensionMode($dimensionMode)
    {
        return $this;
    }
    public function getDimensionModeConfiguration()
    {
        return [];
    }
    public function getHandlers()
    {
        return [];
    }
    public function isValid()
    {
        return true;
    }
    public function isInvalid()
    {
        return false;
    }
    public function invalidate()
    {
        return $this;
    }
    public function getStatus()
    {
        return null;
    }
    public function setStatus($status)
    {
        return $this;
    }
    public function isWorking()
    {
        return false;
    }
    public function getLatestUpdated()
    {
        return null;
    }
}
