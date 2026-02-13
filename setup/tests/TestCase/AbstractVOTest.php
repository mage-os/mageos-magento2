<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\TestCase;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use MageOS\Installer\Model\VO\Attribute\Sensitive;

/**
 * Abstract base test for all Value Objects
 *
 * Provides common test patterns for VO serialization, deserialization, and sensitive data handling
 */
abstract class AbstractVOTest extends TestCase
{
    /**
     * Create a valid instance of the VO for testing
     *
     * @return object
     */
    abstract protected function createValidInstance(): object;

    /**
     * Get array of sensitive field names for this VO
     *
     * @return string[]
     */
    abstract protected function getSensitiveFields(): array;

    /**
     * Test that toArray() excludes sensitive fields by default
     */
    public function testItSerializesWithoutSensitiveData(): void
    {
        $vo = $this->createValidInstance();
        $array = $vo->toArray(includeSensitive: false);

        foreach ($this->getSensitiveFields() as $field) {
            $this->assertArrayNotHasKey(
                $field,
                $array,
                "Sensitive field '{$field}' should not be in array when includeSensitive=false"
            );
        }
    }

    /**
     * Test that toArray() includes sensitive fields when requested
     */
    public function testItSerializesWithSensitiveDataWhenRequested(): void
    {
        $vo = $this->createValidInstance();
        $array = $vo->toArray(includeSensitive: true);

        foreach ($this->getSensitiveFields() as $field) {
            $this->assertArrayHasKey(
                $field,
                $array,
                "Sensitive field '{$field}' should be in array when includeSensitive=true"
            );
        }
    }

    /**
     * Test that fromArray() can reconstruct the VO
     */
    public function testItDeserializesFromArray(): void
    {
        $vo = $this->createValidInstance();
        $array = $vo->toArray(includeSensitive: true);

        $class = get_class($vo);
        $reconstructed = $class::fromArray($array);

        $this->assertInstanceOf($class, $reconstructed);
    }

    /**
     * Test that round-trip serialization preserves data
     */
    public function testRoundTripPreservesData(): void
    {
        $vo = $this->createValidInstance();
        $array = $vo->toArray(includeSensitive: true);

        $class = get_class($vo);
        $reconstructed = $class::fromArray($array);

        $this->assertEquals(
            $vo,
            $reconstructed,
            'Round-trip serialization should preserve all data'
        );
    }

    /**
     * Test that the #[Sensitive] attribute is properly applied
     */
    public function testSensitiveFieldsHaveAttribute(): void
    {
        $vo = $this->createValidInstance();
        $reflection = new ReflectionClass($vo);

        foreach ($this->getSensitiveFields() as $fieldName) {
            $property = $reflection->getProperty($fieldName);
            $attributes = $property->getAttributes(Sensitive::class);

            $this->assertNotEmpty(
                $attributes,
                "Field '{$fieldName}' should have #[Sensitive] attribute"
            );
        }
    }

    /**
     * Test that fromArray() handles missing fields gracefully
     */
    public function testFromArrayHandlesMissingFields(): void
    {
        $class = get_class($this->createValidInstance());

        // Create with minimal/empty data
        $reconstructed = $class::fromArray([]);

        $this->assertInstanceOf($class, $reconstructed);
    }

    /**
     * Helper: Assert that a VO property has expected value
     */
    protected function assertPropertyEquals(object $vo, string $propertyName, mixed $expectedValue): void
    {
        $reflection = new ReflectionProperty($vo, $propertyName);
        $actualValue = $reflection->getValue($vo);

        $this->assertEquals(
            $expectedValue,
            $actualValue,
            "Property '{$propertyName}' should have value: " . var_export($expectedValue, true)
        );
    }

    /**
     * Helper: Get all non-sensitive public properties from VO
     *
     * @return string[]
     */
    protected function getNonSensitiveProperties(object $vo): array
    {
        $reflection = new ReflectionClass($vo);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $sensitiveFields = $this->getSensitiveFields();

        $nonSensitive = [];
        foreach ($properties as $property) {
            if (!in_array($property->getName(), $sensitiveFields, true)) {
                $nonSensitive[] = $property->getName();
            }
        }

        return $nonSensitive;
    }
}
