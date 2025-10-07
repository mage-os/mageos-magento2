<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Event\Test\Unit\Helper;

use Magento\Framework\Event\Observer;

/**
 * Test helper for Observer class
 */
class ObserverTestHelper extends Observer
{
    /**
     * @var mixed
     */
    private $grid;

    /**
     * @var mixed
     */
    private $form;

    /**
     * @var mixed
     */
    private $dependencies;

    /**
     * @var mixed
     */
    private $attribute;

    /**
     * @var mixed
     */
    private $event;

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
    }

    /**
     * Get grid
     *
     * @return mixed
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * Set grid
     *
     * @param mixed $grid
     * @return void
     */
    public function setGrid($grid)
    {
        $this->grid = $grid;
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
     * Set form
     *
     * @param mixed $form
     * @return void
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * Get dependencies
     *
     * @return mixed
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Set dependencies
     *
     * @param mixed $dependencies
     * @return void
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Get attribute
     *
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set attribute
     *
     * @param mixed $attribute
     * @return void
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * Get event
     *
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set event
     *
     * @param mixed $event
     * @return void
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }
}
