<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Ui\Component\Form\Fieldset;

use Magento\Ui\Component\Form\Fieldset;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 *
 * @api
 */
class Factory
{
    /**
     * @var string
     */
    protected $className = \Magento\Ui\Component\Form\Fieldset::class;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create data provider
     *
     * @param array $arguments
     * @return Fieldset
     */
    public function create(array $arguments = [])
    {
        return $this->objectManager->create($this->className, ['data' => $arguments]);
    }
}
