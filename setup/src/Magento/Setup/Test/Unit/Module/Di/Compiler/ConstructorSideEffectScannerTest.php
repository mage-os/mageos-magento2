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
    private const NAMESPACE = 'Vendor\\ScannerFixture';

    private const PROPERTIES = "public \$a;\n    public \$b;\n    public \$c;\n    public \$d;\n"
        . "    public static \$s;\n    public const X = 'x';\n    ";

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
            'promoted properties' => ['private array $p = [], private ?string $q = null', '', true],
            'plain assignment' => ['$a', '$this->a = $a;', true],
            'coalesce assignment' => ['$a', '$this->a ??= $a;', true],
            'literals' => ['', '$this->a = 1; $this->b = -1.5; $this->c = \'x\'; $this->d = null;', true],
            'constants' => ['', '$this->a = PHP_EOL; $this->b = self::class; $this->c = \Foo\Bar::BAZ;', true],
            'static class constant' => ['', '$this->a = static::FOO;', true],
            'magic constants' => ['', '$this->a = __DIR__; $this->b = __CLASS__;', true],
            'arrays' => ['array $a', '$this->a = [\'k\' => $a, ...$a]; $this->b = array(1, 2,);', true],
            'spread of variadic' => ['...$a', '$this->a = [...$a];', true],
            'casts of scalar parameters' => [
                '?int $a, int|string $b, bool $c',
                '$this->a = (string)$a; $this->b = (int)$b; $this->c = (array)$c;',
                true,
            ],
            'casts of literals and constants' => ['', '$this->a = (int)\'5\'; $this->b = (string)self::X;', true],
            'nullable array dim fetch' => ['?array $a', '$this->a = $a[\'k\'] ?? null;', true],
            'promoted property read' => ['private array $p', '$this->a = $this->p;', true],
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
            'cast of untyped parameter' => ['$a', '$this->a = (string)$a;', false],
            'cast of object parameter' => ['\Stringable $a', '$this->a = (string)$a;', false],
            'cast of mixed parameter' => ['mixed $a', '$this->a = (int)$a;', false],
            'cast of property' => ['', '$this->a = (string)$this->b;', false],
            'dim fetch on untyped parameter' => ['$a', '$this->a = $a[\'k\'];', false],
            'dim fetch on iterable parameter' => ['iterable $a', '$this->a = $a[\'k\'] ?? null;', false],
            'nested dim fetch' => ['array $a', '$this->a = $a[\'k\'][\'j\'];', false],
            'dim fetch on property' => ['', '$this->a = $this->b[\'k\'];', false],
            'dim fetch on $this' => ['', '$this->a = $this[\'k\'];', false],
            'property read on parameter' => ['$a', '$this->a = $a->b;', false],
            'nullsafe property read on parameter' => ['$a', '$this->a = $a?->b;', false],
            'nested property read' => ['', '$this->a = $this->b->c;', false],
            'undefined variable' => ['', '$this->a = $b;', false],
            'spread of untyped parameter' => ['$a', '$this->a = [...$a];', false],
            'undeclared property assignment' => ['$a', '$this->undeclared = $a;', false],
            'undeclared property read' => ['', '$this->a = $this->undeclared;', false],
            'static property via $this' => ['$a', '$this->s = $a;', false],
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

    public function testMagicSetRejectsAssignmentAndMagicGetRejectsRead(): void
    {
        $assign = "public function __construct(\$a)\n    {\n        \$this->a = \$a;\n    }";
        $read = "public function __construct()\n    {\n        \$this->a = \$this->b;\n    }";
        $set = "\n    public function __set(\$n, \$v)\n    {\n    }";
        $get = "\n    public function __get(\$n)\n    {\n    }";

        $scanner = new ConstructorSideEffectScanner();
        $this->assertFalse($scanner->isSideEffectFree(new \ReflectionClass($this->declareClass($assign . $set))));
        $this->assertTrue($scanner->isSideEffectFree(new \ReflectionClass($this->declareClass($assign . $get))));
        $this->assertFalse($scanner->isSideEffectFree(new \ReflectionClass($this->declareClass($read . $get))));

        $parentWithSet = $this->declareClass(ltrim($set));
        $child = $this->declareClass($assign, '', $parentWithSet, self::NAMESPACE, '');
        $this->assertFalse($scanner->isSideEffectFree(new \ReflectionClass($child)));
    }

    public function testParentPrivatePropertyIsUndeclaredForChild(): void
    {
        $parent = $this->declareClass('private $p;', '', null, self::NAMESPACE, '');
        $child = $this->declareClass(
            "public function __construct(\$a)\n    {\n        \$this->p = \$a;\n    }",
            '',
            $parent,
            self::NAMESPACE,
            ''
        );

        $this->assertFalse((new ConstructorSideEffectScanner())->isSideEffectFree(new \ReflectionClass($child)));
    }

    public function testHookedPropertiesAreRejected(): void
    {
        if (PHP_VERSION_ID < 80400) {
            $this->markTestSkipped('Property hooks need PHP 8.4.');
        }
        $hooked = "public \$h { set => \$value; }\n    ";
        $scanner = new ConstructorSideEffectScanner();
        foreach (['$this->h = $a;', '$this->a = $this->h;'] as $body) {
            $class = $this->declareClass(
                $hooked . "public function __construct(\$a)\n    {\n        $body\n    }",
                '',
                null,
                self::NAMESPACE,
                self::PROPERTIES
            );
            $this->assertFalse($scanner->isSideEffectFree(new \ReflectionClass($class)), $body);
        }

        // A subclass can add a hook to a property that an inherited constructor assigns
        $parent = $this->declareClass("public function __construct(\$a)\n    {\n        \$this->a = \$a;\n    }");
        $child = $this->declareClass('public $a { set => $value; }', '', $parent, self::NAMESPACE, '');
        $this->assertTrue($scanner->isSideEffectFree(new \ReflectionClass($parent)));
        $this->assertFalse($scanner->isSideEffectFree(new \ReflectionClass($child)));
    }

    #[DataProvider('trustedConstructorCallProvider')]
    public function testMethodsCalledByTrustedConstructorMustNotBeOverridden(
        string $coreBody,
        string $members,
        bool $expected
    ): void {
        $core = $this->declareClass(
            "public function __construct()\n    {\n        $coreBody\n    }\n"
            . "    protected function _initSelect()\n    {\n    }\n"
            . "    protected static function make()\n    {\n    }",
            '',
            null,
            'Magento\\ScannerFixture'
        );

        $class = $this->declareClass($members, '', $core);
        $scanner = new ConstructorSideEffectScanner();
        $this->assertSame($expected, $scanner->isSideEffectFree(new \ReflectionClass($class)));
    }

    public static function trustedConstructorCallProvider(): array
    {
        $override = "protected function _initSelect()\n    {\n    }";
        $overrideStatic = "protected static function make()\n    {\n    }";
        $magicCall = "public function __call(\$n, \$a)\n    {\n    }";
        return [
            'not overridden' => ['$this->_initSelect();', '', true],
            'overridden' => ['$this->_initSelect();', $override, false],
            'other method overridden' => ['$this->_initSelect();', $overrideStatic, true],
            'static:: call overridden' => ['static::make();', $overrideStatic, false],
            'self:: call is not virtual' => ['self::make();', $overrideStatic, true],
            'dynamic method name' => ['$this->$name();', '', false],
            'undefined method reaches third-party __call' => ['$this->missing();', $magicCall, false],
        ];
    }

    public function testTrustedParentConstructorChainIsWalked(): void
    {
        $grandparent = $this->declareClass(
            "public function __construct()\n    {\n        \$this->_construct();\n    }\n"
            . "    protected function _construct()\n    {\n    }",
            '',
            null,
            'Magento\\ScannerFixture'
        );
        $parent = $this->declareClass(
            "public function __construct()\n    {\n        parent::__construct();\n    }",
            '',
            $grandparent,
            'Magento\\ScannerFixture'
        );
        $override = "protected function _construct()\n    {\n    }";

        $scanner = new ConstructorSideEffectScanner();
        $this->assertTrue($scanner->isSideEffectFree(new \ReflectionClass($this->declareClass('', '', $parent))));
        $overriding = $this->declareClass($override, '', $parent);
        $this->assertFalse($scanner->isSideEffectFree(new \ReflectionClass($overriding)));
    }

    /**
     * Write a uniquely named class to a temp file and load it, so the scanner can read its source
     *
     * @param string $members
     * @param string $uses
     * @param string|null $parent
     * @param string $namespace
     * @param string $properties
     * @return string
     */
    private function declareClass(
        string $members,
        string $uses = '',
        ?string $parent = null,
        string $namespace = self::NAMESPACE,
        string $properties = self::PROPERTIES
    ): string {
        self::$counter++;
        $name = 'ScannerFixture' . getmypid() . '_' . self::$counter;
        $extends = $parent !== null ? ' extends \\' . ltrim($parent, '\\') : '';
        $file = tempnam(sys_get_temp_dir(), 'ctor-scan');
        self::$files[] = $file;
        file_put_contents(
            $file,
            "<?php\nnamespace $namespace;\n\n$uses\nclass $name$extends\n{\n    $properties$members\n}\n"
        );
        require $file;

        return $namespace . '\\' . $name;
    }
}
