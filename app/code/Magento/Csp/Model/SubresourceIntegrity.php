<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Csp\Model;

/**
 * Subresource Integrity data model.
 */
class SubresourceIntegrity extends \Magento\Framework\DataObject
{
    /**
     * Gets an integrity Path.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->getData("path");
    }

    /**
     * Gets an integrity hash.
     *
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->getData("hash");
    }
}
