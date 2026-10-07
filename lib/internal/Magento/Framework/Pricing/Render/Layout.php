<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */

namespace Magento\Framework\Pricing\Render;

use Magento\Framework\ObjectManager\ResetAfterRequestInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;

/**
 * Pricing render's layout model
 */
class Layout implements ResetAfterRequestInterface
{
    /**
     * Layout Interface, created on first use in each request
     *
     * @var LayoutInterface|null
     */
    protected $layout;

    /**
     * @var LayoutFactory
     */
    private LayoutFactory $layoutFactory;

    /**
     * @var LayoutInterface
     */
    private LayoutInterface $generalLayout;

    /**
     * Constructor
     *
     * @param LayoutFactory $layoutFactory
     * @param LayoutInterface $generalLayout
     */
    public function __construct(
        LayoutFactory $layoutFactory,
        \Magento\Framework\View\LayoutInterface $generalLayout
    ) {
        // The inner layout is created lazily per request: in a long-lived worker a
        // constructor-time copy of the page's cacheable flag goes stale and makes
        // PageCache LayoutPlugin mark private pages (cart, login) public.
        $this->layoutFactory = $layoutFactory;
        $this->generalLayout = $generalLayout;
    }

    /**
     * Get the inner layout, created with the current page's cacheable flag
     *
     * @return LayoutInterface
     */
    private function getLayout(): LayoutInterface
    {
        return $this->layout ??= $this->layoutFactory->create(['cacheable' => $this->generalLayout->isCacheable()]);
    }

    /**
     * @inheritDoc
     */
    public function _resetState(): void
    {
        $this->layout = null;
    }

    /**
     * Add handle(s) to layout
     *
     * @param string|string[] $handle
     * @return void
     */
    public function addHandle($handle)
    {
        $this->getLayout()->getUpdate()->addHandle($handle);
    }

    /**
     * Load layout
     *
     * @return void
     */
    public function loadLayout()
    {
        $this->getLayout()->getUpdate()->load();
        $this->getLayout()->generateXml();
        $this->getLayout()->generateElements();
    }

    /**
     * Obtain block object
     *
     * @param string $name
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getBlock($name)
    {
        return $this->getLayout()->getBlock($name);
    }
}
