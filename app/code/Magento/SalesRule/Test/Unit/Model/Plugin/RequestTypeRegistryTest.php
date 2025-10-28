<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Test\Unit\Model\Plugin;

use Magento\SalesRule\Model\Plugin\RequestTypeRegistry;
use PHPUnit\Framework\TestCase;

class RequestTypeRegistryTest extends TestCase
{
    /**
     * @var RequestTypeRegistry
     */
    private $state;

    protected function setUp(): void
    {
        $this->state = new RequestTypeRegistry();
    }

    public function testDefaultStateIsFalse()
    {
        $this->assertFalse($this->state->isGetRequestOrQuery());
    }

    public function testIsGetRequestOrQueryTrue()
    {
        $this->state->setIsGetRequestOrQuery(true);
        $this->assertTrue($this->state->isGetRequestOrQuery());
    }

    public function testIsGetRequestOrQueryFalse()
    {
        $this->state->setIsGetRequestOrQuery(true);
        $this->state->setIsGetRequestOrQuery(false);
        $this->assertFalse($this->state->isGetRequestOrQuery());
    }

    public function testMultipleToggle()
    {
        $this->assertFalse($this->state->isGetRequestOrQuery());

        $this->state->setIsGetRequestOrQuery(true);
        $this->assertTrue($this->state->isGetRequestOrQuery());

        $this->state->setIsGetRequestOrQuery(false);
        $this->assertFalse($this->state->isGetRequestOrQuery());

        $this->state->setIsGetRequestOrQuery(true);
        $this->assertTrue($this->state->isGetRequestOrQuery());
    }
}
