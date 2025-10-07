<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Swatches\Test\Unit\Helper;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\View\Element\Template\Context;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;
use Magento\Swatches\Helper\Data;
use Magento\Swatches\Helper\Media;

/**
 * Test helper for RenderLayered class
 */
class RenderLayeredTestHelper extends RenderLayered
{
    /**
     * @var AbstractFilter
     */
    protected $filter;

    /**
     * @var Attribute
     */
    protected $eavAttribute;

    /**
     * Constructor
     *
     * @param Context $context
     * @param mixed $eavAttribute
     * @param mixed $layerAttributeFactory
     * @param Data $swatchHelper
     * @param Media $mediaHelper
     * @param array $data
     * @param mixed $htmlBlockPager
     */
    public function __construct(
        Context $context,
        $eavAttribute,
        $layerAttributeFactory,
        Data $swatchHelper,
        Media $mediaHelper,
        array $data = [],
        $htmlBlockPager = null
    ) {
        $this->eavAttribute = $eavAttribute;
        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttributeFactory,
            $swatchHelper,
            $mediaHelper,
            $data,
            $htmlBlockPager
        );
    }

    /**
     * Get filter
     *
     * @return AbstractFilter
     */
    public function filter()
    {
        return $this->filter;
    }

    /**
     * Set filter
     *
     * @param AbstractFilter $filter
     * @return void
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * Get EAV attribute
     *
     * @return Attribute
     */
    public function eavAttribute()
    {
        return $this->eavAttribute;
    }

    /**
     * Set EAV attribute
     *
     * @param Attribute $attr
     * @return void
     */
    public function setEavAttribute($attr)
    {
        $this->eavAttribute = $attr;
    }
}
