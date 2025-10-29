<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Test\Unit\Model\Plugin;

use Magento\SalesRule\Model\Plugin\TriggerRecollectState;
use PHPUnit\Framework\TestCase;

class TriggerRecollectStateTest extends TestCase
{
    /**
     * @var TriggerRecollectState
     */
    private $state;

    protected function setUp(): void
    {
        $this->state = new TriggerRecollectState();
    }

    public function testDefaultStateIsZero()
    {
        $this->assertEquals(0, $this->state->canRecollect());
    }

    public function testCanRecollectOne()
    {
        $this->state->setTriggerRecollect(1);
        $this->assertEquals(1, $this->state->canRecollect());
    }

    public function testIsGetRequestOrQueryFalse()
    {
        $this->state->setTriggerRecollect(1);
        $this->state->setTriggerRecollect(0);
        $this->assertEquals(0, $this->state->canRecollect());
    }

    public function testMultipleToggle()
    {
        $this->assertEquals(0, $this->state->canRecollect());

        $this->state->setTriggerRecollect(1);
        $this->assertEquals(1, $this->state->canRecollect());

        $this->state->setTriggerRecollect(0);
        $this->assertEquals(0, $this->state->canRecollect());

        $this->state->setTriggerRecollect(1);
        $this->assertEquals(1, $this->state->canRecollect());
    }
}
