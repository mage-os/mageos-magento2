<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\TestFramework\Unit\Helper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use ReflectionClass;

/**
 * Trait for creating partial mocks with reflection for PHPUnit 12 migration
 *
 * Provides helper methods for creating mocks when standard PHPUnit methods are insufficient
 */
trait MockCreationTrait
{
    /**
     * Create a partial mock with reflection.
     *
     * Use this when you need to mock methods that don't exist in the class/interface
     * and cannot use standard createPartialMock() which would throw CannotUseOnlyMethodsException.
     *
     * @param string $className
     * @param array $methods Methods to mock
     * @return MockObject
     */
    protected function createPartialMockWithReflection(string $className, array $methods): MockObject
    {
        $reflection = new ReflectionClass($this);
        $getMockBuilderMethod = $reflection->getMethod('getMockBuilder');
        $getMockBuilderMethod->setAccessible(true);
        $mockBuilder = $getMockBuilderMethod->invoke($this, $className);

        $builderReflection = new ReflectionClass($mockBuilder);
        $methodsProperty = $builderReflection->getProperty('methods');
        $methodsProperty->setAccessible(true);
        $methodsProperty->setValue($mockBuilder, $methods);

        $mockBuilder->disableOriginalConstructor();
        return $mockBuilder->getMock();
    }

    /**
     * Convert a string matcher specification to a PHPUnit invocation matcher.
     *
     * This is useful in data providers where you cannot call PHPUnit matcher methods
     * directly (since they are non-static). Instead, pass a string representation
     * and convert it to the actual matcher in your test method.
     *
     * Supported string formats:
     * - 'never'         → $this->never()
     * - 'once'          → $this->once()
     * - 'any'           → $this->any()
     * - 'atLeastOnce'   → $this->atLeastOnce()
     * - 'exactly_N'     → $this->exactly(N)     e.g., 'exactly_3'
     * - 'atLeast_N'     → $this->atLeast(N)     e.g., 'atLeast_2'
     * - 'atMost_N'      → $this->atMost(N)      e.g., 'atMost_5'
     *
     * @param string $matcherSpec The string specification of the matcher
     * @return InvocationOrder The PHPUnit invocation matcher
     * @throws \InvalidArgumentException If the matcher specification is not recognized
     *
     * @example
     * // In data provider:
     * public static function dataProvider(): array
     * {
     *     return [
     *         ['methodName', 'returnValue', 'once'],
     *         ['methodName', 'returnValue', 'exactly_3'],
     *         ['methodName', 'returnValue', 'never'],
     *     ];
     * }
     *
     * // In test method:
     * public function testMethod(string $method, string $returnValue, string $matcherSpec): void
     * {
     *     $matcher = $this->createInvocationMatcher($matcherSpec);
     *     $mock->expects($matcher)->method($method)->willReturn($returnValue);
     * }
     */
    protected function createInvocationMatcher(string $matcherSpec): InvocationOrder
    {
        // Handle simple string matchers
        return match ($matcherSpec) {
            'never' => $this->never(),
            'once' => $this->once(),
            'any' => $this->any(),
            'atLeastOnce' => $this->atLeastOnce(),
            default => $this->parseParameterizedMatcher($matcherSpec),
        };
    }

    /**
     * Parse parameterized matcher specifications like 'exactly_3', 'atLeast_2', 'atMost_5'.
     *
     * @param string $matcherSpec The parameterized matcher specification
     * @return InvocationOrder
     * @throws \InvalidArgumentException If the matcher specification is not recognized
     */
    private function parseParameterizedMatcher(string $matcherSpec): InvocationOrder
    {
        // Handle 'exactly_N' format
        if (str_starts_with($matcherSpec, 'exactly_')) {
            $count = (int)substr($matcherSpec, 8);
            return $this->exactly($count);
        }

        // Handle 'atLeast_N' format
        if (str_starts_with($matcherSpec, 'atLeast_')) {
            $count = (int)substr($matcherSpec, 8);
            return $this->atLeast($count);
        }

        // Handle 'atMost_N' format
        if (str_starts_with($matcherSpec, 'atMost_')) {
            $count = (int)substr($matcherSpec, 7);
            return $this->atMost($count);
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Unrecognized matcher specification: "%s". ' .
                'Supported: never, once, any, atLeastOnce, exactly_N, atLeast_N, atMost_N',
                $matcherSpec
            )
        );
    }
}
