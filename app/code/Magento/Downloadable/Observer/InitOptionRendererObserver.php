<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Downloadable\Observer;

use Magento\Framework\Event\ObserverInterface;

class InitOptionRendererObserver implements ObserverInterface
{
    /**
     * Initialize product options renderer with downloadable specific params
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getBlock();
        $block->addOptionsRenderCfg('downloadable', \Magento\Downloadable\Helper\Catalog\Product\Configuration::class);

        return $this;
    }
}
