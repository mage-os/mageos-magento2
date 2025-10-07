<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Indexer\Test\Unit\Helper;

use Magento\Framework\Indexer\IndexerInterface;

/**
 * Test helper for IndexerInterface
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class IndexerInterfaceTestHelper implements IndexerInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get ID
     *
     * @return string
     */
    public function getId()
    {
        return 'test_indexer';
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Test Indexer';
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Test Indexer Description';
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function getFields()
    {
        return [];
    }

    /**
     * Get sources
     *
     * @return array
     */
    public function getSources()
    {
        return [];
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [];
    }

    /**
     * Get view ID
     *
     * @return string
     */
    public function getViewId()
    {
        return 'test_view';
    }

    /**
     * Get action class
     *
     * @return string
     */
    public function getActionClass()
    {
        return 'TestAction';
    }

    /**
     * Get is scheduled
     *
     * @return bool
     */
    public function getIsScheduled()
    {
        return false;
    }

    /**
     * Get is valid
     *
     * @return bool
     */
    public function getIsValid()
    {
        return true;
    }

    /**
     * Get is shared
     *
     * @return bool
     */
    public function getIsShared()
    {
        return false;
    }

    /**
     * Get is visible
     *
     * @return bool
     */
    public function getIsVisible()
    {
        return true;
    }

    /**
     * Get is user defined
     *
     * @return bool
     */
    public function getIsUserDefined()
    {
        return false;
    }

    /**
     * Get handlers
     *
     * @return array
     */
    public function getHandlers()
    {
        return [];
    }

    /**
     * Load
     *
     * @param int $id
     * @return $this
     */
    public function load($id)
    {
        return $this;
    }

    /**
     * Get view
     *
     * @return mixed
     */
    public function getView()
    {
        return null;
    }

    /**
     * Get state
     *
     * @return mixed
     */
    public function getState()
    {
        return null;
    }

    /**
     * Set state
     *
     * @param mixed $state
     * @return $this
     */
    public function setState($state)
    {
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return 'valid';
    }

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this;
    }

    /**
     * Is scheduled
     *
     * @return bool
     */
    public function isScheduled()
    {
        return false;
    }

    /**
     * Set scheduled
     *
     * @param bool $scheduled
     * @return $this
     */
    public function setScheduled($scheduled)
    {
        return $this;
    }

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Set valid
     *
     * @param bool $valid
     * @return $this
     */
    public function setValid($valid)
    {
        return $this;
    }

    /**
     * Is shared
     *
     * @return bool
     */
    public function isShared()
    {
        return false;
    }

    /**
     * Set shared
     *
     * @param bool $shared
     * @return $this
     */
    public function setShared($shared)
    {
        return $this;
    }

    /**
     * Is visible
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * Set visible
     *
     * @param bool $visible
     * @return $this
     */
    public function setVisible($visible)
    {
        return $this;
    }

    /**
     * Is user defined
     *
     * @return bool
     */
    public function isUserDefined()
    {
        return false;
    }

    /**
     * Set user defined
     *
     * @param bool $userDefined
     * @return $this
     */
    public function setUserDefined($userDefined)
    {
        return $this;
    }

    /**
     * Is invalid
     *
     * @return bool
     */
    public function isInvalid()
    {
        return false;
    }

    /**
     * Is working
     *
     * @return bool
     */
    public function isWorking()
    {
        return false;
    }

    /**
     * Invalidate
     *
     * @return $this
     */
    public function invalidate()
    {
        return $this;
    }

    /**
     * Reindex all
     *
     * @return $this
     */
    public function reindexAll()
    {
        return $this;
    }

    /**
     * Reindex row
     *
     * @param int $id
     * @return $this
     */
    public function reindexRow($id)
    {
        return $this;
    }

    /**
     * Reindex list
     *
     * @param array $ids
     * @return $this
     */
    public function reindexList($ids)
    {
        return $this;
    }

    /**
     * Get latest updated
     *
     * @return mixed
     */
    public function getLatestUpdated()
    {
        return null;
    }
}

