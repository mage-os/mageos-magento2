<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Link;

class LinkTestHelper extends Link
{
    /**
     * @var mixed
     */
    private $crossSellLinkCollection = null;

    /**
     * @var mixed
     */
    private $attributes = null;

    /**
     * @var mixed
     */
    private $useCrossSellLinksResult = null;

    /**
     * @var mixed
     */
    private $relatedLinkCollection = null;

    /**
     * @var mixed
     */
    private $useRelatedLinksResult = null;

    /**
     * @var mixed
     */
    private $upSellLinkCollection = null;

    /**
     * @var mixed
     */
    private $useUpSellLinksResult = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getCrossSellLinkCollection()
    {
        return $this->crossSellLinkCollection;
    }

    /**
     * @param mixed $collection
     * @return $this
     */
    public function setCrossSellLinkCollection($collection)
    {
        $this->crossSellLinkCollection = $collection;
        return $this;
    }

    /**
     * @param mixed $type
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAttributes($type = null)
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function useCrossSellLinks()
    {
        return $this->useCrossSellLinksResult ?: $this;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setUseCrossSellLinksResult($result)
    {
        $this->useCrossSellLinksResult = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelatedLinkCollection()
    {
        return $this->relatedLinkCollection;
    }

    /**
     * @param mixed $collection
     * @return $this
     */
    public function setRelatedLinkCollection($collection)
    {
        $this->relatedLinkCollection = $collection;
        return $this;
    }

    /**
     * @return mixed
     */
    public function useRelatedLinks()
    {
        return $this->useRelatedLinksResult ?: $this;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setUseRelatedLinksResult($result)
    {
        $this->useRelatedLinksResult = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpSellLinkCollection()
    {
        return $this->upSellLinkCollection;
    }

    /**
     * @param mixed $collection
     * @return $this
     */
    public function setUpSellLinkCollection($collection)
    {
        $this->upSellLinkCollection = $collection;
        return $this;
    }

    /**
     * @return mixed
     */
    public function useUpSellLinks()
    {
        return $this->useUpSellLinksResult ?: $this;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setUseUpSellLinksResult($result)
    {
        $this->useUpSellLinksResult = $result;
        return $this;
    }
}

