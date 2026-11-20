<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager\Test\Unit\Factory\Fixture\Compiled\NativeProxySubject;

use Magento\Framework\ObjectManager\Test\Unit\Factory\Fixture\Compiled\NativeProxySubject;
use Magento\Framework\ObjectManagerInterface;

/**
 * Generated-proxy equivalent for testing the compiled factory fallback.
 */
class Proxy extends NativeProxySubject
{
    /**
     * @var NativeProxySubject|null
     */
    private ?NativeProxySubject $subject = null;

    /**
     * Store generated proxy constructor arguments without constructing the subject.
     */
    public function __construct(
        private ObjectManagerInterface $objectManager,
        private string $instanceName = NativeProxySubject::class,
        private bool $shared = true
    ) {
    }

    /**
     * Delegate to the shared or non-shared subject on first use.
     */
    public function ping(): string
    {
        $this->subject ??= $this->shared
            ? $this->objectManager->get($this->instanceName)
            : $this->objectManager->create($this->instanceName);
        return $this->subject->ping();
    }
}
