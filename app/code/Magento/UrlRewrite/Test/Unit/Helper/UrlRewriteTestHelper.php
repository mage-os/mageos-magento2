<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\UrlRewrite\Test\Unit\Helper;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Mock class for UrlRewrite with consecutive calls support
 */
class UrlRewriteTestHelper extends UrlRewrite
{
    /**
     * @var mixed
     */
    private $setMetadataSequence = [];
    /**
     * @var mixed
     */
    private $setMetadataCallCount = 0;

    /**
     * Mock method for setMetadata with sequence support
     *
     * @param mixed $metadata
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function setMetadata($metadata)
    {
        if (isset($this->setMetadataSequence[$this->setMetadataCallCount])) {
            $result = $this->setMetadataSequence[$this->setMetadataCallCount];
            $this->setMetadataCallCount++;
            return $this;
        }
        return $this;
    }

    /**
     * Set the sequence for setMetadata calls
     *
     * @param array $sequence
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setMetadataSequence(array $sequence)
    {
        $this->setMetadataSequence = $sequence;
        $this->setMetadataCallCount = 0;
        return $this;
    }

    /**
     * Required method from UrlRewrite
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}

