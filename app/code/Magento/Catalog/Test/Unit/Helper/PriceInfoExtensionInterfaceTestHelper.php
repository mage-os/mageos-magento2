<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductRender\PriceInfoExtensionInterface;
use Magento\Catalog\Api\Data\ProductRender\PriceInfoInterface;
use Magento\Msrp\Api\Data\ProductRender\MsrpPriceInfoInterface;

/**
 * Test helper for PriceInfoExtensionInterface
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PriceInfoExtensionInterfaceTestHelper implements PriceInfoExtensionInterface
{
    /**
     * @var MsrpPriceInfoInterface|null
     */
    private ?MsrpPriceInfoInterface $msrp = null;

    /**
     * @var PriceInfoInterface|null
     */
    private ?PriceInfoInterface $taxAdjustments = null;

    /**
     * @var array|null
     */
    private ?array $weeeAttributes = null;

    /**
     * @var string|null
     */
    private ?string $weeeAdjustment = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get MSRP
     *
     * @return MsrpPriceInfoInterface|null
     */
    public function getMsrp(): ?MsrpPriceInfoInterface
    {
        return $this->msrp;
    }

    /**
     * Set MSRP
     *
     * @param MsrpPriceInfoInterface $msrp
     * @return $this
     */
    public function setMsrp(MsrpPriceInfoInterface $msrp): self
    {
        $this->msrp = $msrp;
        return $this;
    }

    /**
     * Get tax adjustments
     *
     * @return PriceInfoInterface|null
     */
    public function getTaxAdjustments(): ?PriceInfoInterface
    {
        return $this->taxAdjustments;
    }

    /**
     * Set tax adjustments
     *
     * @param PriceInfoInterface $taxAdjustments
     * @return $this
     */
    public function setTaxAdjustments(PriceInfoInterface $taxAdjustments): self
    {
        $this->taxAdjustments = $taxAdjustments;
        return $this;
    }

    /**
     * Set weee attributes
     *
     * @param mixed $attributes
     * @return $this
     */
    public function setWeeeAttributes($attributes): self
    {
        $this->weeeAttributes = $attributes;
        return $this;
    }

    /**
     * Get weee attributes
     *
     * @return array|null
     */
    public function getWeeeAttributes(): ?array
    {
        return $this->weeeAttributes;
    }

    /**
     * Set weee adjustment
     *
     * @param mixed $adjustment
     * @return $this
     */
    public function setWeeeAdjustment($adjustment): self
    {
        $this->weeeAdjustment = $adjustment;
        return $this;
    }

    /**
     * Get weee adjustment
     *
     * @return string|null
     */
    public function getWeeeAdjustment(): ?string
    {
        return $this->weeeAdjustment;
    }
}
