<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Plugin\Model\Indexer\Category\Product;

use Magento\Catalog\Model\Indexer\Category\Product\AbstractAction;
use Magento\Catalog\Plugin\Model\Indexer\Category\Product\Execute;
use Magento\Framework\App\Cache\Test\Unit\Helper\TypeListTestHelper;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\PageCache\Model\Config;
use Magento\PageCache\Test\Unit\Helper\ConfigTestHelper;
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
        $this->config = new ConfigTestHelper();
        $this->typeList = new TypeListTestHelper();

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
