<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CustomerImportExport\Test\Unit\Helper;

class CustomerEntityTestHelper
{
    /**
     * Filter entity collection
     *
     * @param mixed $collection
     * @return mixed
     */
    public function filterEntityCollection($collection)
    {
        return $collection;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setParameters(array $parameters): self
    {
        return $this;
    }
}
