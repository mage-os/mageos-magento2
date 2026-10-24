<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler;

use Magento\Setup\Module\Di\Compiler\ConstructorSideEffectScanner;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConstructorSideEffectScannerTest extends TestCase
{
    /**
     * @var string[]
     */
    private static array $files = [];

    /**
     * @var int
     */
    private static int $counter = 0;

    public static function tearDownAfterClass(): void
    {
        foreach (self::$files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        self::$files = [];
    }

    #[DataProvider('constructorProvider')]
    public function testConstructorClassification(string $signature, string $body, bool $expected): void
    {
        $class = $this->declareClass(
            "public function __construct($signature)\n    {\n        $body\n    }"
        );

        $this->assertSame(
            $expected,
            (new ConstructorSideEffectScanner())->isSideEffectFree(new \ReflectionClass($class))
        );
    }

    public static function constructorProvider(): array
    {
        return [
            // Side-effect-free
            'empty' => ['', '', true],
            'promoted properties' => ['private array $a = [], private ?string $b = null', '', true],
            'plain assignment' => ['$a', '$this->a = $a;', true],
            'coalesce assignment' => ['$a', '$this->a ??= $a;', true],
            'literals' => ['', '$this->a = 1; $this->b = -1.5; $this->c = \'x\'; $this->d = null;', true],
            'constants' => ['', '$this->a = PHP_EOL; $this->b = self::class; $this->c = \Foo\Bar::BAZ;', true],
            'static class constant' => ['', '$this->a = static::FOO;', true],
            'magic constants' => ['', '$this->a = __DIR__; $this->b = __CLASS__;', true],
            'arrays' => ['$a', '$this->a = [\'k\' => $a, ...$a]; $this->b = array(1, 2,);', true],
            'casts' => ['$a', '$this->a = (int)$a; $this->b = (array)$a; $this->c = (bool)$a;', true],
            'ternary' => ['$a', '$this->a = $a ? $a : []; $this->b = $a ?: null;', true],
            'nested coalesce' => ['$a, $b', '$this->a = $a ?? $b ?? [];', true],
            'property and dim fetch' => ['array $d', '$this->a = $d[\'x\'] ?? $this->b; $this->c = $this->a;', true],
            'parenthesised' => ['$a', '$this->a = ($a ?? []);', true],
            'ObjectManager fallback (FQ)' => [
                '$a = null',
                '$this->a = $a ?? \Magento\Framework\App\ObjectManager::getInstance()->get(\Foo::class);',
                true,
            ],
            'ObjectManager fallback create' => [
                '$a = null',
                '$this->a = $a ?? \Magento\Framework\App\ObjectManager::getInstance()->create(\Foo::class, []);',
                true,
            ],
            'comments are ignored' => ['$a', '// note' . "\n" . '/* x */ $this->a = $a;', true],

            // Side effects or unrecognised constructs
            'function call' => ['', 'define(\'X\', 1);', false],
            'function call in assignment' => ['$a', '$this->a = array_filter($a);', false],
            'method call' => ['', '$this->init();', false],
            'method call in assignment' => ['$a', '$this->a = $a->get();', false],
            'static call' => ['', 'self::init();', false],
            'static:: call' => ['$a', '$this->a = static::make($a);', false],
            'foreign static call' => ['', '\Foo::bar();', false],
            'new in body' => ['', '$this->a = new \ArrayObject();', false],
            'new in default parameter' => ['$a = new \ArrayObject()', '', false],
            'if' => ['$a', 'if ($a) { $this->a = $a; }', false],
            'foreach' => ['array $a', 'foreach ($a as $v) { $this->a = $v; }', false],
            'try' => ['', 'try { $this->a = 1; } catch (\Throwable) { }', false],
            'match' => ['$a', '$this->a = match ($a) { default => 1 };', false],
            'closure' => ['', '$this->a = function () { };', false],
            'arrow function' => ['', '$this->a = fn () => 1;', false],
            'static arrow function' => ['', '$this->a = static fn () => 1;', false],
            'echo' => ['', 'echo 1;', false],
            'global' => ['', 'global $x;', false],
            'include' => ['', 'require __DIR__ . \'/x.php\';', false],
            'exit' => ['', 'exit;', false],
            'return' => ['', 'return;', false],
            'local variable assignment' => ['$a', '$b = $a;', false],
            'chained assignment' => ['$a', '$this->a = $this->b = $a;', false],
            'nested property assignment' => ['$a', '$this->a->b = $a;', false],
            'array append' => ['$a', '$this->a[] = $a;', false],
            'static property assignment' => ['$a', 'self::$a = $a;', false],
            'static property read' => ['', '$this->a = self::$b;', false],
            'interpolated string' => ['$a', '$this->a = "x{$a}";', false],
            'concatenation' => ['$a', '$this->a = $a . \'x\';', false],
            'comparison' => ['$a', '$this->a = $a === 1;', false],
            'object cast' => ['$a', '$this->a = (object)$a;', false],
            'ObjectManager outside coalesce' => [
                '',
                '$this->a = \Magento\Framework\App\ObjectManager::getInstance()->get(\Foo::class);',
                false,
            ],
            'other ObjectManager' => [
                '$a = null',
                '$this->a = $a ?? \Vendor\ObjectManager::getInstance()->get(\Foo::class);',
                false,
            ],
            'ObjectManager other method' => [
                '$a = null',
                '$this->a = $a ?? \Magento\Framework\App\ObjectManager::getInstance()->configure([]);',
                false,
            ],
            'ObjectManager fallback with impure argument' => [
                '$a = null',
                '$this->a = $a ?? \Magento\Framework\App\ObjectManager::getInstance()->get(foo());',
                false,
            ],
        ];
    }

    public function testImportedObjectManagerFallbackIsSideEffectFree(): void
    {
        $class = $this->declareClass(
            "public function __construct(\$a = null)\n    {\n"
            . "        \$this->a = \$a ?? ObjectManager::getInstance()->get(\\Foo::class);\n"
            . "        \$this->b = \$a ?? App\\ObjectManager::getInstance()->get(\\Foo::class);\n    }",
            "use Magento\\Framework\\App\\ObjectManager;\nuse Magento\\Framework\\App;\n"
        );

        $this->assertTrue((new ConstructorSideEffectScanner())->isSideEffectFree(new \ReflectionClass($class)));
    }

    public function testAliasedNonCoreObjectManagerIsRejected(): void
    {
        $class = $this->declareClass(
            "public function __construct(\$a = null)\n    {\n"
            . "        \$this->a = \$a ?? ObjectManager::getInstance()->get(\\Foo::class);\n    }",
            "use Vendor\\Di\\Container as ObjectManager;\n"
        );

        $this->assertFalse((new ConstructorSideEffectScanner())->isSideEffectFree(new \ReflectionClass($class)));
    }

    public function testParentConstructorIsEvaluated(): void
    {
        $safeParent = $this->declareClass("public function __construct(\$a)\n    {\n        \$this->a = \$a;\n    }");
        $unsafeParent = $this->declareClass("public function __construct()\n    {\n        \$this->init();\n    }");
        $child = "public function __construct(\$a)\n    {\n        parent::__construct(\$a);\n    }";

        $scanner = new ConstructorSideEffectScanner();
        $safeChild = $this->declareClass($child, '', $safeParent);
        $unsafeChild = $this->declareClass($child, '', $unsafeParent);
        $this->assertTrue($scanner->isSideEffectFree(new \ReflectionClass($safeChild)));
        $this->assertFalse($scanner->isSideEffectFree(new \ReflectionClass($unsafeChild)));
    }

    public function testParentConstructorCallWithImpureArgumentIsRejected(): void
    {
        $parent = $this->declareClass("public function __construct(\$a)\n    {\n        \$this->a = \$a;\n    }");
        $child = $this->declareClass(
            "public function __construct(\$a)\n    {\n        parent::__construct(strtolower(\$a));\n    }",
            '',
            $parent
        );

        $this->assertFalse((new ConstructorSideEffectScanner())->isSideEffectFree(new \ReflectionClass($child)));
    }

    public function testParentConstructorCallWithoutParentConstructorIsRejected(): void
    {
        $parent = $this->declareClass('');
        $child = $this->declareClass(
            "public function __construct()\n    {\n        parent::__construct();\n    }",
            '',
            $parent
        );

        $this->assertFalse((new ConstructorSideEffectScanner())->isSideEffectFree(new \ReflectionClass($child)));
    }

    public function testInternalAncestorConstructorIsRejected(): void
    {
        $class = $this->declareClass('', '', '\\ArrayObject');

        $this->assertFalse((new ConstructorSideEffectScanner())->isSideEffectFree(new \ReflectionClass($class)));
    }

    public function testTrustedNamespaces(): void
    {
        $scanner = new ConstructorSideEffectScanner();

        $this->assertTrue($scanner->isTrusted('Magento\\Catalog\\Model\\Product'));
        $this->assertTrue($scanner->isTrusted('\\MageOS\\Foo\\Bar'));
        $this->assertFalse($scanner->isTrusted('Vendor\\Magento\\Foo'));
        $this->assertFalse($scanner->isTrusted('MagentoX\\Foo'));
    }

    /**
     * Write a uniquely named class to a temp file and load it, so the scanner can read its source
     *
     * @param string $members
     * @param string $uses
     * @param string|null $parent
     * @return string
     */
    private function declareClass(string $members, string $uses = '', ?string $parent = null): string
    {
        self::$counter++;
        $name = 'ScannerFixture' . getmypid() . '_' . self::$counter;
        $extends = $parent !== null ? ' extends \\' . ltrim($parent, '\\') : '';
        $file = tempnam(sys_get_temp_dir(), 'ctor-scan');
        self::$files[] = $file;
        file_put_contents(
            $file,
            "<?php\nnamespace Vendor\\ScannerFixture;\n\n$uses\n#[\\AllowDynamicProperties]\n"
            . "class $name$extends\n{\n    $members\n}\n"
        );
        require $file;

        return 'Vendor\\ScannerFixture\\' . $name;
    }
}
