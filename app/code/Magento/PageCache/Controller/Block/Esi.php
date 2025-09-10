<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\PageCache\Controller\Block;

class Esi extends \Magento\PageCache\Controller\Block
{
    /**
     * Returns block content as part of ESI request from Varnish
     *
     * @return void
     */
    public function execute()
    {
        $response = $this->getResponse();
        $blocks = $this->_getBlocks();
        $html = '';
        $ttl = 0;

        if (!empty($blocks)) {
            $blockInstance = array_shift($blocks);
            $html = $blockInstance->toHtml();
            $ttl = $blockInstance->getTtl();
            if ($blockInstance instanceof \Magento\Framework\DataObject\IdentityInterface) {
                $response->setHeader('X-Magento-Tags', implode(',', $blockInstance->getIdentities()));
            }
        }
        $this->translateInline->processResponseBody($html);
        $response->appendBody($html);
        $response->setPublicHeaders($ttl);
    }
}
