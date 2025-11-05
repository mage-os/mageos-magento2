<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Block\Widget\Grid\Column\Renderer\Radio;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\Radio\Extended;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Magento\Backend\Test\Unit\Helper\ColumnTestHelper;

class ExtendedTest extends TestCase
{
    /**
     * @var Extended
     */
    protected $_object;

    /**
     * @var MockObject
     */
    protected $_converter;

    /**
     * @var MockObject
     */
    protected $_column;

    protected function setUp(): void
    {
        $context = $this->createMock(Context::class);
        $this->_converter = $this->createPartialMock(
            Converter::class,
            ['toFlatArray']
        );
        $this->_column = new \Magento\Backend\Test\Unit\Helper\ColumnTestHelper();
        $this->_object = new Extended($context, $this->_converter);
        $this->_object->setColumn($this->_column);
    }

    /**
     * @param array $rowData
     * @param string $expectedResult
     */
    #[DataProvider('renderDataProvider')]
    public function testRender(array $rowData, $expectedResult)
    {
        $selectedFlatArray = [1 => 'One'];
        $this->_column->setValues($selectedFlatArray);
        $this->_column->setIndex('label');
        $this->_column->setData('html_name', 'test[]');
        $this->_converter->expects($this->never())->method('toFlatArray');
        $this->assertEquals($expectedResult, $this->_object->render(new DataObject($rowData)));
    }

    /**
     * @return array
     */
    public static function renderDataProvider()
    {
        return [
            'checked' => [
                ['id' => 1, 'label' => 'One'],
                '<input type="radio" name="test[]" value="1" class="radio" checked="checked"/>',
            ],
            'not checked' => [
                ['id' => 2, 'label' => 'Two'],
                '<input type="radio" name="test[]" value="2" class="radio"/>',
            ]
        ];
    }
}
