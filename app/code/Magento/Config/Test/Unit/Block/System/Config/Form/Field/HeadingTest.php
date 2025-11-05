<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

/**
 * Tests for \Magento\Framework\Data\Form\Field\Heading
 */
namespace Magento\Config\Test\Unit\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\Heading;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class HeadingTest extends TestCase
{
    public function testRender()
    {
        $htmlId = 'test_HTML_id';
        $label  = 'test_label';

        $elementMock = $this->createMock(AbstractElement::class);
        $elementMock->method('getHtmlId')->willReturn($htmlId);
        $elementMock->method('getLabel')->willReturn($label);

        $objectManager = new ObjectManager($this);

        $heading = $objectManager->getObject(Heading::class, []);

        $html = $heading->render($elementMock);

        $this->assertEquals(
            '<tr class="system-fieldset-sub-head" id="row_' . $htmlId . '">' .
            '<td colspan="5">' .
            '<h4 id="' . $htmlId . '">' . $label . '</h4>' .
            '</td>' .
            '</tr>',
            $html
        );
    }
}
