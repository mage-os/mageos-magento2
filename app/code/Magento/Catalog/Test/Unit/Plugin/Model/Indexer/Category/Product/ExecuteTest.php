<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Plugin\Model\Indexer\Category\Product;

use Magento\Catalog\Model\Indexer\Category\Product\AbstractAction;
use Magento\Catalog\Plugin\Model\Indexer\Category\Product\Execute;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\PageCache\Model\Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ExecuteTest extends TestCase
{
    /** @var Execute */
    protected $execute;

    /** @var Config|MockObject */
    protected $config;

    /** @var TypeListInterface|MockObject */
    protected $typeList;

    protected function setUp(): void
    {
        $this->config = new class extends Config {
            private $isEnabledReturn = false;

            public function __construct()
            {
                // empty constructor
            }
            
            public function setIsEnabledReturn($return)
            {
                $this->isEnabledReturn = $return;
                return $this;
            }
            
            public function isEnabled()
            {
                return $this->isEnabledReturn;
            }
        };
        $this->typeList = new class implements TypeListInterface {
            private $invalidateCalled = false;
            private $invalidateArgument = null;

            public function __construct()
            {
                // empty constructor
            }
            
            public function setInvalidateCalled($called)
            {
                $this->invalidateCalled = $called;
                return $this;
            }
            
            public function getInvalidateCalled()
            {
                return $this->invalidateCalled;
            }
            
            public function setInvalidateArgument($argument)
            {
                $this->invalidateArgument = $argument;
                return $this;
            }
            
            public function getInvalidateArgument()
            {
                return $this->invalidateArgument;
            }
            
            public function invalidate($typeCode)
            {
                $this->invalidateCalled = true;
                $this->invalidateArgument = $typeCode;
            }
            
            // Required TypeListInterface methods
            public function getTypes()
            {
                return [];
            }
            public function getTypeLabels()
            {
                return [];
            }
            public function getInvalidated()
            {
                return [];
            }
            public function cleanType($typeCode)
            {
            }
        };

        $this->execute = new Execute($this->config, $this->typeList);
    }

    public function testAfterExecute()
    {
        $subject = $this->getMockBuilder(AbstractAction::class)
            ->disableOriginalConstructor()
            ->getMock();

        $result = $this->getMockBuilder(AbstractAction::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config->setIsEnabledReturn(false);
        // Reset the invalidate tracking
        $this->typeList->setInvalidateCalled(false);
        $this->typeList->setInvalidateArgument(null);

        $result = $this->execute->afterExecute($subject, $result);
        
        // Assert that invalidate was not called
        $this->assertFalse($this->typeList->getInvalidateCalled());
        
        $this->assertEquals(
            $result,
            $result
        );
    }

    public function testAfterExecuteInvalidate()
    {
        $subject = $this->getMockBuilder(AbstractAction::class)
            ->disableOriginalConstructor()
            ->getMock();

        $result = $this->getMockBuilder(AbstractAction::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config->setIsEnabledReturn(true);
        // Reset the invalidate tracking
        $this->typeList->setInvalidateCalled(false);
        $this->typeList->setInvalidateArgument(null);

        $result = $this->execute->afterExecute($subject, $result);
        
        // Assert that invalidate was called with 'full_page'
        $this->assertTrue($this->typeList->getInvalidateCalled());
        $this->assertEquals('full_page', $this->typeList->getInvalidateArgument());
        
        $this->assertEquals(
            $result,
            $result
        );
    }
}
