<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache\Test\Unit\Frontend\Adapter;

use Magento\Framework\Cache\Frontend\Adapter\Symfony;
use Magento\Framework\Cache\Frontend\Adapter\SymfonyAdapters\TagAdapterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Unit test for the Symfony cache frontend adapter
 */
class SymfonyTest extends TestCase
{
    /**
     * @var ArrayAdapter
     */
    private ArrayAdapter $pool;

    /**
     * @var Symfony
     */
    private Symfony $frontend;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->pool = new ArrayAdapter();
        $pool = $this->pool;
        $this->frontend = new Symfony(
            static fn () => $pool,
            $this->createStub(TagAdapterInterface::class)
        );
    }

    /**
     * Loaded values must not be retained in memory by the frontend
     */
    public function testLoadDoesNotRetainLoadedValue(): void
    {
        $this->assertTrue($this->frontend->save(new \ArrayObject(['payload']), 'some_id'));

        $loaded = $this->frontend->load('some_id');
        $this->assertInstanceOf(\ArrayObject::class, $loaded);
        $reference = \WeakReference::create($loaded);
        unset($loaded);

        $this->assertNull($reference->get());
    }

    /**
     * load() and test() must reflect the backend pool rather than an in-memory copy
     */
    public function testLoadAndTestReadThroughToPool(): void
    {
        $this->assertTrue($this->frontend->save('value', 'some_id'));
        $this->assertSame('value', $this->frontend->load('some_id'));
        $this->assertNotFalse($this->frontend->test('some_id'));

        $this->pool->clear();

        $this->assertFalse($this->frontend->load('some_id'));
        $this->assertFalse($this->frontend->test('some_id'));
    }
}
