<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget\Grid\Column;

class ColumnTestHelper extends Column
{
    private $columnData = [];
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    public function getColumnData()
    {
        return $this->columnData;
    }
    
    public function setColumnData($data)
    {
        $this->columnData = $data;
        return $this;
    }
    
    public function getIndex()
    {
        return $this->getData('index');
    }
    
    public function setIndex($index)
    {
        $this->setData('index', $index);
        return $this;
    }
}
