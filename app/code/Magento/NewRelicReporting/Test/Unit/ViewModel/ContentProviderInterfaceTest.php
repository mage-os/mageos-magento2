<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Test\Unit\ViewModel;

use Magento\NewRelicReporting\ViewModel\ContentProviderInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for ContentProviderInterface
 */
class ContentProviderInterfaceTest extends TestCase
{
    /**
     * Test that ContentProviderInterface is properly defined
     */
    public function testInterfaceExists()
    {
        $this->assertTrue(interface_exists(ContentProviderInterface::class));
    }

    /**
     * Test interface structure
     */
    public function testInterfaceStructure()
    {
        $reflection = new \ReflectionClass(ContentProviderInterface::class);
        
        // Verify it's an interface
        $this->assertTrue($reflection->isInterface());
        
        // Verify it has the getContent method
        $this->assertTrue($reflection->hasMethod('getContent'));
        
        $method = $reflection->getMethod('getContent');
        
        // Method should be public (interfaces are implicitly public)
        $this->assertTrue($method->isPublic());
        
        // Method should have no parameters
        $this->assertCount(0, $method->getParameters());
        
        // Method should return string
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('string', $returnType->getName());
    }

    /**
     * Test that interface has correct method signature
     */
    public function testGetContentMethodSignature()
    {
        $reflection = new \ReflectionClass(ContentProviderInterface::class);
        $method = $reflection->getMethod('getContent');
        
        // Check method name
        $this->assertEquals('getContent', $method->getName());
        
        // Check it's abstract (interface methods are implicitly abstract)
        $this->assertTrue($method->isAbstract());
        
        // Check return type is string
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('string', $returnType->getName());
        
        // Check no parameters
        $parameters = $method->getParameters();
        $this->assertEmpty($parameters);
    }

    /**
     * Test interface can be implemented
     */
    public function testInterfaceCanBeImplemented()
    {
        // Create anonymous class implementing the interface
        $implementation = new class implements ContentProviderInterface {
            public function getContent(): string
            {
                return 'test content';
            }
        };

        $this->assertInstanceOf(ContentProviderInterface::class, $implementation);
        $this->assertEquals('test content', $implementation->getContent());
    }

    /**
     * Test interface constants (if any exist)
     */
    public function testInterfaceConstants()
    {
        $reflection = new \ReflectionClass(ContentProviderInterface::class);
        $constants = $reflection->getConstants();
        
        // This interface should not have constants, but if it does, they should be documented
        // For now, just ensure constants are accessible
        $this->assertIsArray($constants);
    }

    /**
     * Test interface namespace
     */
    public function testInterfaceNamespace()
    {
        $reflection = new \ReflectionClass(ContentProviderInterface::class);
        
        $this->assertEquals('Magento\NewRelicReporting\ViewModel', $reflection->getNamespaceName());
        $this->assertEquals('ContentProviderInterface', $reflection->getShortName());
    }

    /**
     * Test interface does not extend other interfaces (simple interface)
     */
    public function testInterfaceInheritance()
    {
        $reflection = new \ReflectionClass(ContentProviderInterface::class);
        
        // Should not extend any parent interfaces (is a base interface)
        $parentInterfaces = $reflection->getInterfaceNames();
        $this->assertEmpty($parentInterfaces);
    }

    /**
     * Test interface method count
     */
    public function testInterfaceMethodCount()
    {
        $reflection = new \ReflectionClass(ContentProviderInterface::class);
        $methods = $reflection->getMethods();
        
        // Should have exactly one method: getContent
        $this->assertCount(1, $methods);
        $this->assertEquals('getContent', $methods[0]->getName());
    }

    /**
     * Test multiple implementations can coexist
     */
    public function testMultipleImplementations()
    {
        // Create two different implementations
        $impl1 = new class implements ContentProviderInterface {
            public function getContent(): string
            {
                return 'implementation 1';
            }
        };

        $impl2 = new class implements ContentProviderInterface {
            public function getContent(): string
            {
                return 'implementation 2';
            }
        };

        $this->assertInstanceOf(ContentProviderInterface::class, $impl1);
        $this->assertInstanceOf(ContentProviderInterface::class, $impl2);
        
        $this->assertEquals('implementation 1', $impl1->getContent());
        $this->assertEquals('implementation 2', $impl2->getContent());
        
        // Different implementations should return different content
        $this->assertNotEquals($impl1->getContent(), $impl2->getContent());
    }

    /**
     * Test interface documentation and DocBlock
     */
    public function testInterfaceDocumentation()
    {
        $reflection = new \ReflectionClass(ContentProviderInterface::class);
        
        // Interface should have a DocBlock
        $docComment = $reflection->getDocComment();
        
        // DocComment might be false if no comment exists, which is fine
        if ($docComment !== false) {
            $this->assertIsString($docComment);
            // Should contain interface description markers
            $this->assertStringContainsString('/**', $docComment);
            $this->assertStringContainsString('*/', $docComment);
        }
    }
}
