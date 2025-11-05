<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Widget\Grid\AbstractTotals;
use Magento\Backend\Model\Widget\Grid\Parser;
use Magento\Framework\DataObject\Factory;

/**
 * Test helper for AbstractTotals with custom methods
 */
class AbstractTotalsTestHelper extends AbstractTotals
{
    /**
     * Constructor
     *
     * @param Factory $factory
     * @param Parser $parser
     */
    public function __construct(Factory $factory, Parser $parser)
    {
        $this->_factory = $factory;
        $this->_parser = $parser;
    }

    /**
     * Count sum method
     *
     * @param mixed $collection
     * @param mixed $expression
     * @return int
     */
    public function _countSum($collection, $expression)
    {
        return 2;
    }

    /**
     * Count average method
     *
     * @param mixed $collection
     * @param mixed $expression
     * @return int
     */
    public function _countAverage($collection, $expression)
    {
        return 2;
    }
}

