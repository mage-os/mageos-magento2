<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Sample;

/**
 * Test helper class for Downloadable Sample with custom methods
 *
 * This helper extends Sample and adds custom methods that can be mocked
 * using PHPUnit's createPartialMock() for behavior verification.
 */
class SampleTestHelper extends Sample
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Override load method
     *
     * @param mixed $id
     * @param string|null $field
     * @return self
     */
    public function load($id, $field = null): self
    {
        return $this;
    }

    /**
     * Override getId method
     *
     * @return mixed
     */
    public function getId()
    {
        return 1;
    }

    /**
     * Override getSampleType method
     *
     * @return string
     */
    public function getSampleType(): string
    {
        return 'url';
    }

    /**
     * Override getSampleUrl method
     *
     * @return string
     */
    public function getSampleUrl(): string
    {
        return 'http://example.com/sample';
    }

    /**
     * Override getBasePath method
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return '/base/path';
    }

    /**
     * Override getBaseSamplePath method
     *
     * @return string
     */
    public function getBaseSamplePath(): string
    {
        return '/base/sample/path';
    }

    /**
     * Override getSampleFile method
     *
     * @return string
     */
    public function getSampleFile(): string
    {
        return 'sample.pdf';
    }
}
