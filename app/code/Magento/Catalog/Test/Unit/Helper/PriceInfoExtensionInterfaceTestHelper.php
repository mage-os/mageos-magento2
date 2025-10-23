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
// phpcs:ignore Magento2.PHP.LiteralNamespaces
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

    public function getMsrp(): ?MsrpPriceInfoInterface
    {
        return $this->msrp;
    }

    public function setMsrp(MsrpPriceInfoInterface $msrp): self
    {
        $this->msrp = $msrp;
        return $this;
    }

    public function getTaxAdjustments(): ?PriceInfoInterface
    {
        return $this->taxAdjustments;
    }

    public function setTaxAdjustments(PriceInfoInterface $taxAdjustments): self
    {
        $this->taxAdjustments = $taxAdjustments;
        return $this;
    }

    public function getWeeeAttributes()
    {
        return $this->weeeAttributes;
    }

    public function setWeeeAttributes($attributes): self
    {
        $this->weeeAttributes = $attributes;
        return $this;
    }

    public function getWeeeAdjustment()
    {
        return $this->weeeAdjustment;
    }

    public function setWeeeAdjustment($adjustment): self
    {
        $this->weeeAdjustment = $adjustment;
        return $this;
    }
}
