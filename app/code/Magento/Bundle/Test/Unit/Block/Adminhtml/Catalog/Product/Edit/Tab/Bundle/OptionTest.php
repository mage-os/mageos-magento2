<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle;

use Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    public function testGetAddButtonId()
    {
        $button = new DataObject();
        $button->setId(42);

        $itemsBlock = new class extends DataObject {
            private $childBlocks = [];
            
            public function __construct() {}
            
            public function getChildBlock($name) 
            { 
                return $this->childBlocks[$name] ?? null; 
            }
            
            public function setChildBlock($name, $block) 
            { 
                $this->childBlocks[$name] = $block; 
                return $this; 
            }
        };
        
        // Set up the add_button child block BEFORE setting it in the layout
        $itemsBlock->setChildBlock('add_button', $button);

        $layout = new class extends DataObject {
            private $blocks = [];
            
            public function __construct() {}
            
            public function getBlock($name) 
            { 
                return $this->blocks[$name] ?? null; 
            }
            
            public function setBlock($name, $block) 
            { 
                $this->blocks[$name] = $block; 
                return $this; 
            }
        };
        $layout->setBlock('admin.product.bundle.items', $itemsBlock);
            
        $block = $this->createPartialMock(
            Option::class,
            ['getLayout']
        );
        $block->expects($this->atLeastOnce())->method('getLayout')->willReturn($layout);

        // Test that the button ID is correctly retrieved
        $this->assertEquals(42, $block->getAddButtonId());
    }
}
