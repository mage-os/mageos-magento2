<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Data\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Data\Form\Element\Text;

/**
 * TestHelper for Text form element
 * Provides custom methods not available in parent Text class for testing
 */
class TextTestHelper extends Text
{
    /** @var mixed */
    private $value = null;
    /** @var string|null */
    private $name = null;
    /** @var mixed */
    private $form = null;

    /**
     * Constructor
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $collectionFactory
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $collectionFactory,
        Escaper $escaper,
        array $data = []
    ) {
        parent::__construct($factoryElement, $collectionFactory, $escaper, $data);
    }

    /**
     * Set value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
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
}

