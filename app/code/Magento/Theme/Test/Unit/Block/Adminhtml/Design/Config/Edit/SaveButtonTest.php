<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Theme\Test\Unit\Block\Adminhtml\Design\Config\Edit;

use Magento\Theme\Block\Adminhtml\Design\Config\Edit\SaveButton;
use PHPUnit\Framework\TestCase;

class SaveButtonTest extends TestCase
{
    /**
     * @var SaveButton
     */
    protected $block;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->block = new SaveButton();
    }

    public function testGetButtonData()
    {
        $result = $this->block->getButtonData();

        $this->assertArrayHasKey('label', $result);
        $this->assertEquals($result['label'], __('Save Configuration'));
        $this->assertArrayHasKey('data_attribute', $result);
        $this->assertIsArray($result['data_attribute']);
    }
}
