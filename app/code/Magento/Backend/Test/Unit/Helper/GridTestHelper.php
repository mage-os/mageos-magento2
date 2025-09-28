<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget\Grid;

/**
 * Test helper for Magento\Backend\Block\Widget\Grid
 */
class GridTestHelper extends Grid
{
    /**
     * @var string
     */
    private $nameInLayout = '';
    
    /**
     * @var mixed
     */
    private $massactionBlock = null;
    
    /**
     * @var array
     */
    private $childBlocks = [];
    
    /**
     * @var mixed
     */
    private $parentBlock = null;
    
    /**
     * @var bool
     */
    private $canReadPrice = false;
    
    /**
     * @var bool
     */
    private $canEditPrice = false;
    
    /**
     * @var array
     */
    private $tabData = [];
    
    /**
     * @var mixed
     */
    private $defaultProductPrice = null;
    
    /**
     * @var mixed
     */
    private $form = null;
    
    /**
     * @var mixed
     */
    private $group = null;
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Set name in layout
     *
     * @param string $name
     * @return $this
     */
    public function setNameInLayout($name)
    {
        $this->nameInLayout = $name;
        return $this;
    }
    
    /**
     * Get name in layout
     *
     * @return string
     */
    public function getNameInLayout()
    {
        return $this->nameInLayout;
    }
    
    /**
     * Set massaction block
     *
     * @param mixed $block
     * @return $this
     */
    public function setMassactionBlock($block)
    {
        $this->massactionBlock = $block;
        return $this;
    }
    
    /**
     * Get massaction block
     *
     * @return mixed
     */
    public function getMassactionBlock()
    {
        return $this->massactionBlock;
    }
    
    /**
     * Set child block
     *
     * @param string $name
     * @param mixed $block
     * @return $this
     */
    public function setChildBlock($name, $block)
    {
        $this->childBlocks[$name] = $block;
        return $this;
    }
    
    /**
     * Get child block
     *
     * @param string $name
     * @return mixed
     */
    public function getChildBlock($name)
    {
        return $this->childBlocks[$name] ?? null;
    }
    
    /**
     * Set parent block
     *
     * @param mixed $block
     * @return $this
     */
    public function setParentBlock($block)
    {
        $this->parentBlock = $block;
        return $this;
    }
    
    /**
     * Get parent block
     *
     * @return mixed
     */
    public function getParentBlock()
    {
        return $this->parentBlock;
    }
    
    /**
     * Set can read price
     *
     * @param bool $canRead
     * @return $this
     */
    public function setCanReadPrice($canRead)
    {
        $this->canReadPrice = $canRead;
        return $this;
    }
    
    /**
     * Is can read price
     *
     * @return bool
     */
    public function isCanReadPrice()
    {
        return $this->canReadPrice;
    }
    
    /**
     * Get can read price (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getCanReadPrice()
    {
        return $this->isCanReadPrice();
    }
    
    /**
     * Set can edit price
     *
     * @param bool $canEdit
     * @return $this
     */
    public function setCanEditPrice($canEdit)
    {
        $this->canEditPrice = $canEdit;
        return $this;
    }
    
    /**
     * Is can edit price
     *
     * @return bool
     */
    public function isCanEditPrice()
    {
        return $this->canEditPrice;
    }
    
    /**
     * Get can edit price (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getCanEditPrice()
    {
        return $this->isCanEditPrice();
    }
    
    /**
     * Set tab data
     *
     * @param array $data
     * @return $this
     */
    public function setTabData($data)
    {
        $this->tabData = $data;
        return $this;
    }
    
    /**
     * Get tab data
     *
     * @return array
     */
    public function getTabData()
    {
        return $this->tabData;
    }
    
    /**
     * Set default product price
     *
     * @param mixed $price
     * @return $this
     */
    public function setDefaultProductPrice($price)
    {
        $this->defaultProductPrice = $price;
        return $this;
    }
    
    /**
     * Get default product price
     *
     * @return mixed
     */
    public function getDefaultProductPrice()
    {
        return $this->defaultProductPrice;
    }
    
    /**
     * Set form
     *
     * @param mixed $form
     * @return $this
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }
    
    /**
     * Get form
     *
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }
    
    /**
     * Set group
     *
     * @param mixed $group
     * @return $this
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }
    
    /**
     * Get group
     *
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }
}
