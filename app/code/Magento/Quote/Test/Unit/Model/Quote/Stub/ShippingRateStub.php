<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Model\Quote\Stub;

/**
 * Minimal stub representing a shipping rate item for unit testing.
 *
 * Magento\Quote\Model\Quote\Address\Rate exposes getCode() only via __call() magic
 * (backed by DataObject::getData()), which means PHPUnit cannot mock it without the
 * deprecated addMethods(). This hand-rolled stub avoids that limitation while keeping
 * the tests free of any DI or model infrastructure.
 */
class ShippingRateStub
{
    public function __construct(
        private readonly string $code,
        private bool $deleted = false
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Mirrors the DataObject::isDeleted() signature used by Quote\Address.
     *
     * @param bool|null $isDeleted Pass true/false to set; omit to read.
     * @return bool
     */
    public function isDeleted(?bool $isDeleted = null): bool
    {
        if ($isDeleted !== null) {
            $this->deleted = $isDeleted;
        }
        return $this->deleted;
    }
}
