<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\Concat;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Magento\Backend\Test\Unit\Helper\ColumnTestHelper;

class ConcatTest extends TestCase
{
    /** @var ObjectManager  */
    protected $objectManagerHelper;

    /** @var Concat */
    protected $renderer;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManager($this);
        $this->renderer = $this->objectManagerHelper->getObject(
            Concat::class
        );
    }

    /**
     * @return array
     */
    public static function typeProvider()
    {
        return [
            ['getGetter', ['getTest', 'getBest']],
            ['getIndex', ['test', 'best', 'nothing']],
        ];
    }

    /**
     */
    #[DataProvider('typeProvider')]
    public function testRender($method, $getters)
    {
        $object = new DataObject(['test' => 'a', 'best' => 'b']);
        $column = new \Magento\Backend\Test\Unit\Helper\ColumnTestHelper();
        // Configure getSeparator
        $column->setSeparator('-');
        if ($method == 'getGetter') {
            $column->setGetter(['getTest', 'getBest']);
        } else {
            $column->setIndex($getters);
        }
        $this->renderer->setColumn($column);
        $this->assertEquals('a-b', $this->renderer->render($object));
    }
}
