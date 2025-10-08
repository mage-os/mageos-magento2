<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
     */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Test helper for Block classes with custom methods
 * 
 */
class BlockTestHelper extends AbstractBlock
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor - clean initialization
        $this->data = [];
    }

    /**
     * Set index for testing
     *
     * @param mixed $index
     * @return self
     */
    public function setIndex($index): self
    {
        $this->data['index'] = $index;
        return $this;
    }

    /**
     * Get index for testing
     *
     * @return mixed
     */
    public function getIndex()
    {
        return $this->data['index'] ?? null;
    }

    /**
     * Set HTML result for testing
     *
     * @param string $result
     * @return self
     */
    public function setHtmlResult(string $result): self
    {
        $this->data['html_result'] = $result;
        return $this;
    }

    /**
     * Get HTML result for testing
     *
     * @return string
     */
    public function getHtmlResult(): string
    {
        return $this->data['html_result'] ?? '';
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->data['html_result'] ?? '';
    }

    /**
     * Set HTML output for testing
     *
     * @param string $html
     * @return self
     */
    public function setHtml(string $html): self
    {
        $this->data['html'] = $html;
        return $this;
    }

    /**
     * Get HTML output for testing
     *
     * @return string
     */
    public function getHtml(): string
    {
        return $this->data['html'] ?? '';
    }

    /**
     * Custom render method for testing (used in pricing renderers)
     *
     * @param string $type
     * @param mixed $product
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render($type, $product, $arguments = [])
    {
        return $this->data['render_result'] ?? '';
    }

    /**
     * Set render result for testing
     *
     * @param string $result
     * @return self
     */
    public function setRenderResult(string $result): self
    {
        $this->data['render_result'] = $result;
        return $this;
    }

    /**
     * Get render result for testing
     *
     * @return string
     */
    public function getRenderResult(): string
    {
        return $this->data['render_result'] ?? '';
    }

    /**
     * Set test data for flexible state management
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setTestData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Get test data
     *
     * @param string $key
     * @return mixed
     */
    public function getTestData(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Set first show flag for testing
     *
     * @param mixed $firstShow
     * @return self
     */
    public function setFirstShow($firstShow): self
    {
        $this->data['first_show'] = $firstShow;
        return $this;
    }

    /**
     * Get first show flag for testing
     *
     * @return mixed
     */
    public function getFirstShow()
    {
        return $this->data['first_show'] ?? null;
    }

    /**
     * Override _toHtml to work with our data array
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        return $this->toHtml();
    }
}
