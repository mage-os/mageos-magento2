<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Swatches\Test\Unit\Helper;

use Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual;

/**
 * Test helper for Visual class
 */
class VisualTestHelper extends Visual
{
    /**
     * @var bool
     */
    public $read_only;

    /**
     * @var array
     */
    private $testData;

    /**
     * Constructor
     *
     * @param array $testData
     */
    public function __construct($testData = [])
    {
        $this->testData = $testData;
        if (isset($testData['read_only'])) {
            $this->read_only = $testData['read_only'];
        }
    }

    /**
     * Can manage option default only
     *
     * @return bool
     */
    public function canManageOptionDefaultOnly()
    {
        return $this->testData['can_manage_option_default_only'] ?? false;
    }

    /**
     * Get option values
     *
     * @return array
     */
    public function getOptionValues()
    {
        return $this->testData['option_values'] ?? [];
    }

    /**
     * Get URL
     *
     * @param string $route
     * @param array $params
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getUrl($route = '', $params = [])
    {
        if (empty($this->testData)) {
            return 'test-url';
        }
        return 'http://magento.com/admin/swatches/iframe/show';
    }

    /**
     * Is read only
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->testData['read_only'] ?? false;
    }

    /**
     * Get read only (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getReadOnly()
    {
        return $this->isReadOnly();
    }
}
