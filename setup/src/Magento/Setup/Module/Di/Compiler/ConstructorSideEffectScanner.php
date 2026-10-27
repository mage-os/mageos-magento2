<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Module\Di\Compiler;

/**
 * Conservative, token-based check that a class constructor has no side effects, so the class can
 * safely be built as a PHP 8.4 lazy ghost (whose constructor may never run).
 *
 * The scan fails closed: anything it does not recognise makes the constructor unsafe. A constructor
 * is side-effect-free when its signature has no `new` in default values and its body contains only:
 *  - `$this->prop = <pure>;` and `$this->prop ??= <pure>;`
 *  - `parent::__construct(<pure>, ...);`, after which the parent constructor is checked the same way
 *
 * `<pure>` is one of:
 *  - a constructor parameter, `$this`, a literal, a constant or class constant
 *  - `$this->prop`
 *  - `$param['key']` where `$param` is declared `array` or `?array`
 *  - a cast of a literal, a constant, or a parameter declared with scalar types only
 *  - an array, ternary, `?:` or `??` of those, with `...` spread only of `array` parameters
 *  - the BC fallback `$x ?? \Magento\Framework\App\ObjectManager::getInstance()->get(<pure>)` (or `->create(...)`)
 *
 * Every `$this->prop` must be a declared, non-static property without hooks, and the class must not
 * declare `__set()` (when assigning) or `__get()` (when reading), so property access runs no user code.
 * Promoted constructor parameters count as property writes, so a hook added to one by a subclass also
 * makes the class unsafe. A `__destruct()` declared outside Magento\ or MageOS\ makes the class unsafe
 * too: an uninitialized ghost is never destructed, so its side effects would be skipped as well.
 *
 * Constructors declared in Magento\ or MageOS\ classes are trusted without scanning. They often call
 * overridable methods such as `_construct()`, so the class is unsafe if a method that a trusted
 * constructor in its chain calls as `$this->method()` or `static::method()` is declared outside
 * Magento\ or MageOS\ (in the class itself or a third-party ancestor or trait). This check covers
 * only the trusted constructors themselves, not the methods they call.
 *
 * A trusted constructor that delegates into one declared outside Magento\ or MageOS\ does not end
 * the walk: that constructor is scanned like any other untrusted one, so inheriting or extending a
 * core constructor never exempts a third-party ancestor from the scan.
 *
 * A lazy class builds its constructor dependencies only when it is first initialized, so a
 * dependency with side effects (even a #[NonLazy] one, or one created by the ObjectManager fallback)
 * is built at that point rather than at injection. A class whose methods never touch its own state
 * but rely on a dependency's side effects should itself be marked #[NonLazy].
 */
class ConstructorSideEffectScanner
{
    /**
     * Namespaces whose constructors are trusted without scanning
     */
    public const TRUSTED_NAMESPACES = ['Magento\\', 'MageOS\\'];

    private const OBJECT_MANAGER_CLASS = \Magento\Framework\App\ObjectManager::class;

    private const SKIPPED_TOKENS = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];

    private const NAME_TOKENS = [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE, T_STATIC];

    private const LITERAL_TOKENS = [
        T_LNUMBER, T_DNUMBER, T_CONSTANT_ENCAPSED_STRING, T_LINE, T_FILE, T_DIR, T_CLASS_C, T_TRAIT_C,
        T_METHOD_C, T_FUNC_C, T_NS_C,
    ];

    private const CAST_TOKENS = [
        T_INT_CAST, T_DOUBLE_CAST, T_STRING_CAST, T_ARRAY_CAST, T_BOOL_CAST,
    ];

    private const SCALAR_TYPES = ['int', 'float', 'string', 'bool', 'false', 'true', 'null'];

    private const FETCH_TOKENS = ['(', '[', '{', T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON];

    /**
     * @var array<string, array<string, string>> Use-import aliases (lowercase alias => FQCN) per file path
     */
    private array $importCache = [];

    /**
     * @var array<string, array|null> Parsed untrusted constructor per declaring class, null when unsafe
     */
    private array $constructorCache = [];

    /**
     * @var array<string, string[]|null> Methods called on $this by a trusted constructor, null when unknown
     */
    private array $trustedCallCache = [];

    /**
     * @var array<string, bool> Whether a trusted constructor calls parent::__construct(), per declaring class
     */
    private array $trustedParentCallCache = [];

    /**
     * @var array<string, bool> Result per scanned class
     */
    private array $resultCache = [];

    /**
     * @var array Filtered tokens of the constructor currently being parsed
     */
    private array $tokens = [];

    /**
     * @var int Cursor into $tokens
     */
    private int $pos = 0;

    /**
     * @var array<string, string> Use-imports of the file currently being parsed
     */
    private array $imports = [];

    /**
     * @var \ReflectionClass|null Class declaring the constructor currently being parsed
     */
    private ?\ReflectionClass $scope = null;

    /**
     * @var array<string, \ReflectionParameter> Parameters of the constructor currently being parsed
     */
    private array $params = [];

    /**
     * @var array<string, array<string, true>> `$this` properties written and read by the current constructor
     */
    private array $access = [];

    /**
     * Whether constructing the class has no side effects, so skipping its constructor is safe
     *
     * @param \ReflectionClass $class
     * @return bool
     */
    public function isSideEffectFree(\ReflectionClass $class): bool
    {
        $key = $class->getName();
        if (!isset($this->resultCache[$key])) {
            try {
                $this->resultCache[$key] = $this->evaluate($class);
            } catch (\Throwable) {
                $this->resultCache[$key] = false;
            } finally {
                $this->tokens = [];
                $this->params = [];
                $this->scope = null;
            }
        }
        return $this->resultCache[$key];
    }

    /**
     * Whether a class name is in a trusted namespace
     *
     * @param string $class
     * @return bool
     */
    public function isTrusted(string $class): bool
    {
        foreach (self::TRUSTED_NAMESPACES as $namespace) {
            if (str_starts_with(ltrim($class, '\\'), $namespace)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Walk the constructor chain that runs when the given class is instantiated
     *
     * @param \ReflectionClass $class
     * @return bool
     */
    private function evaluate(\ReflectionClass $class): bool
    {
        if ($class->hasMethod('__destruct')
            && !$this->isTrusted($class->getMethod('__destruct')->getDeclaringClass()->getName())
        ) {
            return false;
        }

        $writes = [];
        $reads = [];
        $current = $class;
        while (($constructor = $current->getConstructor()) !== null) {
            $declaring = $constructor->getDeclaringClass();
            if ($this->isTrusted($declaring->getName())) {
                if (!$this->callsOnlyTrustedMethods($class, $this->getTrustedCalls($constructor, $declaring))) {
                    return false;
                }
                $untrusted = $this->getUntrustedAncestorConstructor($declaring);
                if ($untrusted === null) {
                    break;
                }
                // A trusted constructor may still delegate upward into one that is not trusted, so
                // keep walking and scan that constructor rather than assuming the chain is safe.
                $current = $untrusted->getDeclaringClass();
                continue;
            }

            $parsed = $this->getParsedConstructor($constructor, $declaring);
            if ($parsed === null) {
                return false;
            }
            $writes += $parsed['writes'];
            $reads += $parsed['reads'];
            if (!$parsed['callsParent']) {
                break;
            }
            $current = $declaring->getParentClass();
            if ($current === false || $current->getConstructor() === null) {
                return false;
            }
        }

        return $this->isPlainPropertyAccess($class, $writes, $reads);
    }

    /**
     * Whether `$this` property access in the constructors cannot run user code in the instantiated class
     *
     * @param \ReflectionClass $class
     * @param array $writes
     * @param array $reads
     * @return bool
     */
    private function isPlainPropertyAccess(\ReflectionClass $class, array $writes, array $reads): bool
    {
        if (($writes !== [] && $class->hasMethod('__set')) || ($reads !== [] && $class->hasMethod('__get'))) {
            return false;
        }
        foreach (array_keys($writes + $reads) as $name) {
            if ($class->hasProperty((string)$name) && $this->hasHooks($class->getProperty((string)$name))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Whether every method a trusted constructor calls on `$this` is declared in Magento\ or MageOS\
     *
     * @param \ReflectionClass $class
     * @param string[]|null $methods
     * @return bool
     */
    private function callsOnlyTrustedMethods(\ReflectionClass $class, ?array $methods): bool
    {
        if ($methods === null) {
            return false;
        }
        foreach ($methods as $method) {
            $candidates = $class->hasMethod($method) ? [$method] : ['__call', '__callStatic'];
            foreach ($candidates as $candidate) {
                if ($class->hasMethod($candidate)
                    && !$this->isTrusted($class->getMethod($candidate)->getDeclaringClass()->getName())
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Collect the `$this->method()` and `static::method()` calls of a trusted constructor and its trusted parents
     *
     * @param \ReflectionMethod $constructor
     * @param \ReflectionClass $declaring
     * @return string[]|null
     */
    private function getTrustedCalls(\ReflectionMethod $constructor, \ReflectionClass $declaring): ?array
    {
        $key = $declaring->getName();
        if (array_key_exists($key, $this->trustedCallCache)) {
            return $this->trustedCallCache[$key];
        }
        $this->trustedCallCache[$key] = null;

        $tokens = $this->getConstructorTokens($constructor);
        $methods = [];
        $callsParent = false;
        $count = count($tokens);
        for ($i = 0; $i < $count - 3; $i++) {
            [$id, $text] = $tokens[$i];
            $next = $tokens[$i + 1][0];
            if ($id === T_VARIABLE && $text === '$this'
                && ($next === T_OBJECT_OPERATOR || $next === T_NULLSAFE_OBJECT_OPERATOR)
            ) {
                if ($tokens[$i + 2][0] !== T_STRING) {
                    return null;
                }
                if ($tokens[$i + 3][0] === '(') {
                    $methods[strtolower($tokens[$i + 2][1])] = true;
                }
            } elseif ($next === T_DOUBLE_COLON && $tokens[$i + 3][0] === '(') {
                $name = strtolower($tokens[$i + 2][1]);
                if ($id === T_STATIC && !preg_match('/^\w+$/', $name)) {
                    return null;
                }
                if ($id === T_STATIC) {
                    $methods[$name] = true;
                } elseif ($id === T_STRING && strtolower($text) === 'parent' && $name === '__construct') {
                    $callsParent = true;
                }
            }
        }

        $this->trustedParentCallCache[$key] = $callsParent;

        $parent = $declaring->getParentClass();
        $parentConstructor = $parent !== false ? $parent->getConstructor() : null;
        if ($callsParent && $parentConstructor !== null
            && $this->isTrusted($parentConstructor->getDeclaringClass()->getName())
        ) {
            $parentMethods = $this->getTrustedCalls($parentConstructor, $parentConstructor->getDeclaringClass());
            if ($parentMethods === null) {
                return null;
            }
            $methods += array_fill_keys($parentMethods, true);
        }

        return $this->trustedCallCache[$key] = array_map('strval', array_keys($methods));
    }

    /**
     * Whether the constructor chain of a class reaches a constructor outside the trusted namespaces
     *
     * @param \ReflectionClass $class
     * @return bool
     */
    public function chainsToUntrustedConstructor(\ReflectionClass $class): bool
    {
        $constructor = $class->getConstructor();
        if ($constructor === null) {
            return false;
        }
        $declaring = $constructor->getDeclaringClass();
        if (!$this->isTrusted($declaring->getName())) {
            return true;
        }
        // Without an untrusted ancestor that declares a constructor there is nothing for
        // parent::__construct() to reach, so the chain is trusted without reading any source.
        if (!$this->hasUntrustedAncestorConstructor($declaring)) {
            return false;
        }
        if ($this->getTrustedCalls($constructor, $declaring) === null) {
            return true;
        }
        return $this->getUntrustedAncestorConstructor($declaring) !== null;
    }

    /**
     * The first constructor above a trusted one that parent::__construct() reaches and is not trusted
     *
     * @param \ReflectionClass $declaring
     * @return \ReflectionMethod|null
     */
    private function getUntrustedAncestorConstructor(\ReflectionClass $declaring): ?\ReflectionMethod
    {
        $owner = $declaring;
        while (!empty($this->trustedParentCallCache[$owner->getName()])) {
            $parent = $owner->getParentClass();
            $parentConstructor = $parent !== false ? $parent->getConstructor() : null;
            if ($parentConstructor === null) {
                return null;
            }
            $owner = $parentConstructor->getDeclaringClass();
            if (!$this->isTrusted($owner->getName())) {
                return $parentConstructor;
            }
        }
        return null;
    }

    /**
     * Whether any ancestor outside the trusted namespaces declares a constructor of its own
     *
     * @param \ReflectionClass $declaring
     * @return bool
     */
    private function hasUntrustedAncestorConstructor(\ReflectionClass $declaring): bool
    {
        for ($parent = $declaring->getParentClass(); $parent !== false; $parent = $parent->getParentClass()) {
            if (!$this->isTrusted($parent->getName())
                && $parent->getConstructor()?->getDeclaringClass()->getName() === $parent->getName()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Parse an untrusted constructor once, recording the properties it touches and whether it calls the parent
     *
     * @param \ReflectionMethod $constructor
     * @param \ReflectionClass $declaring
     * @return array|null
     */
    private function getParsedConstructor(\ReflectionMethod $constructor, \ReflectionClass $declaring): ?array
    {
        $key = $declaring->getName();
        if (!array_key_exists($key, $this->constructorCache)) {
            $this->constructorCache[$key] = null;
            $this->tokens = $this->getConstructorTokens($constructor);
            $this->imports = $this->importCache[$constructor->getFileName()];
            $this->scope = $declaring;
            $this->params = [];
            $this->access = ['writes' => [], 'reads' => []];
            foreach ($constructor->getParameters() as $param) {
                $this->params[$param->getName()] = $param;
                if ($param->isPromoted()) {
                    $this->access['writes'][$param->getName()] = true;
                }
            }

            $callsParent = false;
            if ($this->parseConstructor($callsParent)) {
                $this->constructorCache[$key] = $this->access + ['callsParent' => $callsParent];
            }
            $this->tokens = [];
        }
        return $this->constructorCache[$key];
    }

    /**
     * Tokenize the file declaring a constructor and slice out the constructor's tokens
     *
     * @param \ReflectionMethod $constructor
     * @return array
     */
    private function getConstructorTokens(\ReflectionMethod $constructor): array
    {
        $file = $constructor->getFileName();
        if (!is_string($file) || $file === '') {
            throw new \RuntimeException('Constructor has no source file');
        }
        return $this->findConstructorTokens(
            $this->getTokens($file),
            (int)$constructor->getStartLine(),
            (int)$constructor->getEndLine()
        );
    }

    /**
     * Tokenize a file, dropping whitespace and comments, and cache its use-imports
     *
     * @param string $file
     * @return array
     */
    private function getTokens(string $file): array
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $code = file_get_contents($file);
        if ($code === false) {
            throw new \RuntimeException('Cannot read ' . $file);
        }
        $tokens = [];
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        foreach (token_get_all($code) as $token) {
            if (is_array($token)) {
                if (!in_array($token[0], self::SKIPPED_TOKENS, true)) {
                    $tokens[] = $token;
                }
            } else {
                $tokens[] = [$token, $token, 0];
            }
        }
        $this->importCache[$file] ??= $this->collectImports($tokens);
        return $tokens;
    }

    /**
     * Collect simple `use A\B\C;` and `use A\B\C as D;` class imports declared before the first class-like
     *
     * @param array $tokens
     * @return array<string, string>
     */
    private function collectImports(array $tokens): array
    {
        $imports = [];
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $id = $tokens[$i][0];
            if (in_array($id, [T_CLASS, T_TRAIT, T_INTERFACE, T_ENUM], true)) {
                break;
            }
            if ($id !== T_USE) {
                continue;
            }
            $name = $tokens[$i + 1] ?? null;
            if ($name === null || !in_array($name[0], [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)) {
                continue;
            }
            $fqcn = ltrim($name[1], '\\');
            $next = $tokens[$i + 2] ?? null;
            if ($next !== null && $next[0] === T_AS && isset($tokens[$i + 3]) && $tokens[$i + 3][0] === T_STRING) {
                $imports[strtolower($tokens[$i + 3][1])] = $fqcn;
            } elseif ($next !== null && $next[0] === ';') {
                $imports[strtolower(substr($fqcn, (int)strrpos('\\' . $fqcn, '\\')))] = $fqcn;
            }
        }
        return $imports;
    }

    /**
     * Slice out the tokens of `function __construct(...) {...}` within the given line range
     *
     * @param array $tokens
     * @param int $startLine
     * @param int $endLine
     * @return array
     */
    private function findConstructorTokens(array $tokens, int $startLine, int $endLine): array
    {
        $count = count($tokens);
        for ($i = 0; $i < $count - 1; $i++) {
            $line = $tokens[$i][2];
            if ($line > $endLine) {
                break;
            }
            if ($tokens[$i][0] === T_FUNCTION
                && $line >= $startLine
                && $tokens[$i + 1][0] === T_STRING
                && strtolower($tokens[$i + 1][1]) === '__construct'
            ) {
                // Braces inside the signature (property hooks on promoted parameters) do not open the body
                $slice = [];
                $depth = 0;
                $parens = 0;
                for ($j = $i; $j < $count; $j++) {
                    $slice[] = $tokens[$j];
                    $id = $tokens[$j][0];
                    if ($id === '(') {
                        $parens++;
                    } elseif ($id === ')') {
                        $parens--;
                    } elseif ($parens > 0) {
                        continue;
                    } elseif ($id === '{' || $id === T_CURLY_OPEN || $id === T_DOLLAR_OPEN_CURLY_BRACES) {
                        $depth++;
                    } elseif ($id === '}') {
                        if (--$depth === 0) {
                            return $slice;
                        }
                    }
                }
                break;
            }
        }
        throw new \RuntimeException('Constructor not found');
    }

    /**
     * Parse `function __construct(<params>) { <statements> }`
     *
     * @param bool $callsParent
     * @return bool
     */
    private function parseConstructor(bool &$callsParent): bool
    {
        $this->pos = 2;
        if (!$this->accept('(')) {
            return false;
        }
        // Signature: any tokens except `new` (default values like `= new Foo()` run on instantiation)
        // and `{` (a property hook on a promoted parameter runs when the parameter is assigned)
        $depth = 1;
        while ($depth > 0) {
            $id = $this->peek();
            if ($id === null || $id === T_NEW || $id === '{') {
                return false;
            }
            if ($id === '(') {
                $depth++;
            } elseif ($id === ')') {
                $depth--;
            }
            $this->pos++;
        }
        if (!$this->accept('{')) {
            return false;
        }

        while (!$this->accept('}')) {
            if ($this->accept(';')) {
                continue;
            }
            if ($this->parseParentConstructorCall()) {
                $callsParent = true;
                continue;
            }
            if (!$this->parsePropertyAssignment()) {
                return false;
            }
        }
        return $this->pos === count($this->tokens);
    }

    /**
     * Parse `parent::__construct(<pure>, ...);`
     *
     * @return bool
     */
    private function parseParentConstructorCall(): bool
    {
        if (!$this->isText(0, 'parent') || $this->peek(1) !== T_DOUBLE_COLON || !$this->isText(2, '__construct')) {
            return false;
        }
        $this->pos += 3;
        return $this->accept('(') && $this->parseArguments(')') && $this->accept(';');
    }

    /**
     * Parse `$this->prop = <pure>;` or `$this->prop ??= <pure>;`
     *
     * @return bool
     */
    private function parsePropertyAssignment(): bool
    {
        if (!$this->isThisPropertyFetch()) {
            return false;
        }
        $property = $this->tokens[$this->pos + 2][1];
        if (!$this->isPlainProperty($property)) {
            return false;
        }
        $this->pos += 3;
        $this->access['writes'][$property] = true;
        if ($this->accept(T_COALESCE_EQUAL)) {
            $this->access['reads'][$property] = true;
        } elseif (!$this->accept('=')) {
            return false;
        }
        return $this->parseExpression() && $this->accept(';');
    }

    /**
     * Parse a pure expression
     *
     * @return bool
     */
    private function parseExpression(): bool
    {
        if (!$this->parseOperand()) {
            return false;
        }
        while (true) {
            if ($this->accept(T_COALESCE)) {
                if (!$this->parseObjectManagerFallback() && !$this->parseOperand()) {
                    return false;
                }
            } elseif ($this->accept('?')) {
                // `?:` or `? <expr> :`
                if (!$this->accept(':') && !($this->parseExpression() && $this->accept(':'))) {
                    return false;
                }
                if (!$this->parseOperand()) {
                    return false;
                }
            } else {
                return true;
            }
        }
    }

    /**
     * Parse a single pure operand
     *
     * @return bool
     */
    private function parseOperand(): bool
    {
        $id = $this->peek();

        if (in_array($id, self::CAST_TOKENS, true)) {
            $this->pos++;
            return $this->parseCastOperand();
        }
        if ($id === '-' || $id === '+') {
            $this->pos++;
            return $this->accept(T_LNUMBER) || $this->accept(T_DNUMBER);
        }
        if (in_array($id, self::LITERAL_TOKENS, true)) {
            $this->pos++;
            return true;
        }
        if ($id === '(') {
            $this->pos++;
            return $this->parseExpression() && $this->accept(')');
        }
        if ($id === '[') {
            $this->pos++;
            return $this->parseArrayElements(']');
        }
        if ($id === T_ARRAY) {
            $this->pos++;
            return $this->accept('(') && $this->parseArrayElements(')');
        }
        if ($id === T_VARIABLE) {
            return $this->parseVariable();
        }
        if (in_array($id, self::NAME_TOKENS, true)) {
            return $this->parseConstant();
        }
        return false;
    }

    /**
     * Parse the operand of a cast: a literal, a constant, or a parameter declared with scalar types only
     *
     * @return bool
     */
    private function parseCastOperand(): bool
    {
        $id = $this->peek();
        if ($id === '-' || $id === '+' || in_array($id, self::LITERAL_TOKENS, true)) {
            return $this->parseOperand();
        }
        if (in_array($id, self::NAME_TOKENS, true)) {
            return $this->parseConstant();
        }
        if ($id === T_VARIABLE) {
            $param = $this->getParameter();
            $this->pos++;
            return $param !== null && $this->isScalarParameter($param) && !$this->isFetch();
        }
        return false;
    }

    /**
     * Parse a constructor parameter, `$this`, `$this->prop`, or `$param[<pure>]` on an `array` parameter
     *
     * @return bool
     */
    private function parseVariable(): bool
    {
        if ($this->tokens[$this->pos][1] === '$this') {
            if ($this->isThisPropertyFetch()) {
                $property = $this->tokens[$this->pos + 2][1];
                if (!$this->isPlainProperty($property)) {
                    return false;
                }
                $this->access['reads'][$property] = true;
                $this->pos += 2;
            }
            $this->pos++;
            return !$this->isFetch();
        }

        $param = $this->getParameter();
        if ($param === null) {
            return false;
        }
        $this->pos++;
        if ($this->peek() === '[') {
            if (!$this->isArrayParameter($param)) {
                return false;
            }
            $this->pos++;
            if (!$this->parseExpression() || !$this->accept(']')) {
                return false;
            }
        }
        return !$this->isFetch();
    }

    /**
     * Parse `...$param` where `$param` is an array; spreading a Traversable runs its iterator
     *
     * @return bool
     */
    private function parseSpread(): bool
    {
        $param = $this->peek() === T_VARIABLE ? $this->getParameter() : null;
        if ($param === null || !$this->isArrayParameter($param)) {
            return false;
        }
        $this->pos++;
        return !$this->isFetch();
    }

    /**
     * Parse a constant (`FOO`, `true`, `null`) or class constant (`X::FOO`, `X::class`); no calls
     *
     * @return bool
     */
    private function parseConstant(): bool
    {
        $this->pos++;
        if ($this->peek() === '(') {
            return false;
        }
        if (!$this->accept(T_DOUBLE_COLON)) {
            return true;
        }
        $id = $this->peek();
        if ($id === T_CLASS || ($id === T_STRING && $this->peek(1) !== '(')) {
            $this->pos++;
            return true;
        }
        return false;
    }

    /**
     * Parse `[key =>] [...]<pure>` elements up to the closing token
     *
     * @param string $close
     * @return bool
     */
    private function parseArrayElements(string $close): bool
    {
        while (!$this->accept($close)) {
            if ($this->accept(T_ELLIPSIS)) {
                if (!$this->parseSpread()) {
                    return false;
                }
            } elseif (!$this->parseExpression() || ($this->accept(T_DOUBLE_ARROW) && !$this->parseExpression())) {
                return false;
            }
            if (!$this->accept(',') && $this->peek() !== $close) {
                return false;
            }
        }
        return true;
    }

    /**
     * Parse `[name:] [...]<pure>` call arguments up to the closing token
     *
     * @param string $close
     * @return bool
     */
    private function parseArguments(string $close): bool
    {
        while (!$this->accept($close)) {
            if ($this->peek() === T_STRING && $this->peek(1) === ':') {
                $this->pos += 2;
            }
            if ($this->accept(T_ELLIPSIS) ? !$this->parseSpread() : !$this->parseExpression()) {
                return false;
            }
            if (!$this->accept(',') && $this->peek() !== $close) {
                return false;
            }
        }
        return true;
    }

    /**
     * Parse the BC fallback `\Magento\Framework\App\ObjectManager::getInstance()->get|create(<pure>, ...)`
     *
     * Only allowed as the right-hand side of `??`.
     *
     * @return bool
     */
    private function parseObjectManagerFallback(): bool
    {
        $name = $this->tokens[$this->pos] ?? null;
        if ($name === null
            || !in_array($name[0], [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)
            || $this->resolveClassName($name) !== self::OBJECT_MANAGER_CLASS
            || $this->peek(1) !== T_DOUBLE_COLON
            || !$this->isText(2, 'getInstance')
            || $this->peek(3) !== '('
            || $this->peek(4) !== ')'
            || $this->peek(5) !== T_OBJECT_OPERATOR
            || !($this->isText(6, 'get') || $this->isText(6, 'create'))
            || $this->peek(7) !== '('
        ) {
            return false;
        }
        $this->pos += 8;
        return $this->parseArguments(')');
    }

    /**
     * Resolve a class name token against the file's use-imports
     *
     * @param array $token
     * @return string
     */
    private function resolveClassName(array $token): string
    {
        if ($token[0] === T_NAME_FULLY_QUALIFIED) {
            return ltrim($token[1], '\\');
        }
        $parts = explode('\\', $token[1], 2);
        $alias = strtolower($parts[0]);
        if (!isset($this->imports[$alias])) {
            return '';
        }
        return $this->imports[$alias] . (isset($parts[1]) ? '\\' . $parts[1] : '');
    }

    /**
     * Whether a property is declared, non-static, accessible from the constructor's class, and has no hooks
     *
     * @param string $name
     * @return bool
     */
    private function isPlainProperty(string $name): bool
    {
        if ($this->scope === null || !$this->scope->hasProperty($name)) {
            return false;
        }
        $property = $this->scope->getProperty($name);
        return !$property->isStatic() && !$this->hasHooks($property);
    }

    /**
     * Whether a property declares get or set hooks (PHP 8.4+)
     *
     * @param \ReflectionProperty $property
     * @return bool
     */
    private function hasHooks(\ReflectionProperty $property): bool
    {
        return method_exists($property, 'hasHooks') && $property->hasHooks();
    }

    /**
     * Constructor parameter named by the variable token at the cursor
     *
     * @return \ReflectionParameter|null
     */
    private function getParameter(): ?\ReflectionParameter
    {
        return $this->params[substr($this->tokens[$this->pos][1], 1)] ?? null;
    }

    /**
     * Whether a parameter always holds an array (`array`, `?array`, or variadic)
     *
     * @param \ReflectionParameter $param
     * @return bool
     */
    private function isArrayParameter(\ReflectionParameter $param): bool
    {
        $type = $param->getType();
        return $param->isVariadic() || ($type instanceof \ReflectionNamedType && $type->getName() === 'array');
    }

    /**
     * Whether a parameter is declared with scalar types only (`int`, `?string`, `int|string`, ...)
     *
     * @param \ReflectionParameter $param
     * @return bool
     */
    private function isScalarParameter(\ReflectionParameter $param): bool
    {
        $type = $param->getType();
        if ($param->isVariadic() || $type === null) {
            return false;
        }
        $types = $type instanceof \ReflectionUnionType ? $type->getTypes() : [$type];
        foreach ($types as $member) {
            if (!$member instanceof \ReflectionNamedType || !in_array($member->getName(), self::SCALAR_TYPES, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Whether the token at the cursor would continue the preceding variable into a call or fetch
     *
     * @return bool
     */
    private function isFetch(): bool
    {
        return in_array($this->peek(), self::FETCH_TOKENS, true);
    }

    /**
     * Whether the cursor is at `$this->prop` followed by an assignment-capable token
     *
     * @return bool
     */
    private function isThisPropertyFetch(): bool
    {
        return $this->peek() === T_VARIABLE
            && $this->tokens[$this->pos][1] === '$this'
            && $this->peek(1) === T_OBJECT_OPERATOR
            && $this->peek(2) === T_STRING;
    }

    /**
     * Token id at an offset from the cursor
     *
     * @param int $offset
     * @return int|string|null
     */
    private function peek(int $offset = 0): int|string|null
    {
        return $this->tokens[$this->pos + $offset][0] ?? null;
    }

    /**
     * Whether the token at an offset is a T_STRING with the given text (case-insensitive)
     *
     * @param int $offset
     * @param string $text
     * @return bool
     */
    private function isText(int $offset, string $text): bool
    {
        $token = $this->tokens[$this->pos + $offset] ?? null;
        return $token !== null && $token[0] === T_STRING && strcasecmp($token[1], $text) === 0;
    }

    /**
     * Advance past the current token if it has the given id
     *
     * @param int|string $id
     * @return bool
     */
    private function accept(int|string $id): bool
    {
        if ($this->peek() === $id) {
            $this->pos++;
            return true;
        }
        return false;
    }
}
