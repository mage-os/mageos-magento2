<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Swatches\Test\Unit\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Framework\DataObject;
use Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options\Text;
use Magento\Swatches\Test\Unit\Helper\TextTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    /**
     * @var MockObject|Text
     */
    private $model;

    /**
     * Setup environment for test
     */
    protected function setUp(): void
    {
        $this->model = new TextTestHelper();
    }

    /**
     * Test getJsonConfig with getReadOnly() is true and canManageOptionDefaultOnly() is false
     */
    public function testGetJsonConfigDataSet1()
    {
        $testCase1 = [
            'dataSet' => [
                'read_only' => true,
                'can_manage_option_default_only' => false,
                'option_values' => [
                    new DataObject(['value' => 6, 'label' => 'red']),
                    new DataObject(['value' => 6, 'label' => 'blue']),
                ]
            ],
            'expectedResult' => '{"attributesData":[{"value":6,"label":"red"},{"value":6,"label":"blue"}],' .
                '"isSortable":0,"isReadOnly":1}'

        ];

        $this->executeTest($testCase1);
    }

    /**
     * Test getJsonConfig with getReadOnly() is false and canManageOptionDefaultOnly() is false
     */
    public function testGetJsonConfigDataSet2()
    {
        $testCase2 = [
            'dataSet' => [
                'read_only' => false,
                'can_manage_option_default_only' => false,
                'option_values' => [
                    new DataObject(['value' => 6, 'label' => 'red']),
                    new DataObject(['value' => 6, 'label' => 'blue']),
                ]
            ],
            'expectedResult' => '{"attributesData":[{"value":6,"label":"red"},{"value":6,"label":"blue"}],' .
                '"isSortable":1,"isReadOnly":0}'

        ];

        $this->executeTest($testCase2);
    }

    /**
     * Execute test for getJsonConfig() function
     */
    public function executeTest($testCase)
    {
        // Set read_only property directly
        $this->model->read_only = $testCase['dataSet']['read_only'];
        
        // Override methods for this test
        $this->model = new TextTestHelper($testCase['dataSet']);

        $this->assertEquals($testCase['expectedResult'], $this->model->getJsonConfig());
    }
}
