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
 * `<pure>` is a variable, `$this->prop`, `$var['key']`, a literal, a constant or class constant, an
 * array, cast, ternary, `?:` or `??` of those, or the BC fallback
 * `$x ?? \Magento\Framework\App\ObjectManager::getInstance()->get(<pure>)` (or `->create(...)`).
 *
 * Constructors declared in Magento\ or MageOS\ classes are trusted without scanning. Core
 * constructors commonly call the overridable `_construct()` hook, so a class whose constructor
 * chain reaches a trusted constructor is unsafe if `_construct()` is declared outside Magento\ or
 * MageOS\ (in the class itself or any third-party ancestor or trait).
 */
class ConstructorSideEffectScanner
{
    /**
     * Namespaces whose constructors are trusted without scanning
     */
    public const TRUSTED_NAMESPACES = ['Magento\\', 'MageOS\\'];

    private const OBJECT_MANAGER_CLASS = \Magento\Framework\App\ObjectManager::class;

    private const UNSAFE = 0;
    private const SAFE = 1;
    private const SAFE_VIA_TRUSTED = 2;

    private const SKIPPED_TOKENS = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];

    private const NAME_TOKENS = [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE, T_STATIC];

    private const LITERAL_TOKENS = [
        T_LNUMBER, T_DNUMBER, T_CONSTANT_ENCAPSED_STRING, T_LINE, T_FILE, T_DIR, T_CLASS_C, T_TRAIT_C,
        T_METHOD_C, T_FUNC_C, T_NS_C,
    ];

    private const CAST_TOKENS = [
        T_INT_CAST, T_DOUBLE_CAST, T_STRING_CAST, T_ARRAY_CAST, T_BOOL_CAST,
    ];

    /**
     * @var array<string, array> Filtered tokens per file path
     */
    private array $tokenCache = [];

    /**
     * @var array<string, array<string, string>> Use-import aliases (lowercase alias => FQCN) per file path
     */
    private array $importCache = [];

    /**
     * @var array<string, int> Evaluation result per constructor-declaring class
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
     * Whether constructing the class has no side effects, so skipping its constructor is safe
     *
     * @param \ReflectionClass $class
     * @return bool
     */
    public function isSideEffectFree(\ReflectionClass $class): bool
    {
        try {
            $result = $this->evaluate($class);
            if ($result === self::SAFE_VIA_TRUSTED) {
                return !$this->hasUntrustedConstructHook($class);
            }
            return $result === self::SAFE;
        } catch (\Throwable) {
            return false;
        }
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
     * Evaluate the constructor that runs when the given class is instantiated
     *
     * @param \ReflectionClass $class
     * @return int
     */
    private function evaluate(\ReflectionClass $class): int
    {
        $constructor = $class->getConstructor();
        if ($constructor === null) {
            return self::SAFE;
        }

        $declaring = $constructor->getDeclaringClass();
        $key = $declaring->getName();
        if (!isset($this->resultCache[$key])) {
            $this->resultCache[$key] = self::UNSAFE;
            $this->resultCache[$key] = $this->isTrusted($key)
                ? self::SAFE_VIA_TRUSTED
                : $this->evaluateConstructor($constructor, $declaring);
        }
        return $this->resultCache[$key];
    }

    /**
     * Whether `_construct()` is declared outside the trusted namespaces
     *
     * @param \ReflectionClass $class
     * @return bool
     */
    private function hasUntrustedConstructHook(\ReflectionClass $class): bool
    {
        return $class->hasMethod('_construct')
            && !$this->isTrusted($class->getMethod('_construct')->getDeclaringClass()->getName());
    }

    /**
     * Parse an untrusted constructor and, when it calls parent::__construct(), evaluate the parent
     *
     * @param \ReflectionMethod $constructor
     * @param \ReflectionClass $declaring
     * @return int
     */
    private function evaluateConstructor(\ReflectionMethod $constructor, \ReflectionClass $declaring): int
    {
        $file = $constructor->getFileName();
        if (!is_string($file) || $file === '') {
            return self::UNSAFE;
        }

        $this->tokens = $this->findConstructorTokens(
            $this->getTokens($file),
            (int)$constructor->getStartLine(),
            (int)$constructor->getEndLine()
        );
        $this->imports = $this->importCache[$file];
        $this->pos = 0;

        $callsParent = false;
        if (!$this->parseConstructor($callsParent)) {
            return self::UNSAFE;
        }
        if (!$callsParent) {
            return self::SAFE;
        }

        $parent = $declaring->getParentClass();
        if ($parent === false || $parent->getConstructor() === null) {
            return self::UNSAFE;
        }
        return $this->evaluate($parent);
    }

    /**
     * Tokenize a file once, dropping whitespace and comments, and collect its use-imports
     *
     * @param string $file
     * @return array
     */
    private function getTokens(string $file): array
    {
        if (!isset($this->tokenCache[$file])) {
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
            $this->tokenCache[$file] = $tokens;
            $this->importCache[$file] = $this->collectImports($tokens);
        }
        return $this->tokenCache[$file];
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
                $slice = [];
                $depth = 0;
                for ($j = $i; $j < $count; $j++) {
                    $slice[] = $tokens[$j];
                    $id = $tokens[$j][0];
                    if ($id === '{' || $id === T_CURLY_OPEN || $id === T_DOLLAR_OPEN_CURLY_BRACES) {
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
        $depth = 1;
        while ($depth > 0) {
            $id = $this->peek();
            if ($id === null || $id === T_NEW) {
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
        $this->pos += 3;
        if (!$this->accept('=') && !$this->accept(T_COALESCE_EQUAL)) {
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
            return $this->parseOperand();
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
     * Parse `$var`, `$this->prop` (repeatable) and `[<pure>]` dimension fetches; no calls
     *
     * @return bool
     */
    private function parseVariable(): bool
    {
        $this->pos++;
        while (true) {
            $id = $this->peek();
            if (($id === T_OBJECT_OPERATOR || $id === T_NULLSAFE_OBJECT_OPERATOR)
                && $this->peek(1) === T_STRING
                && $this->peek(2) !== '('
            ) {
                $this->pos += 2;
            } elseif ($id === '[') {
                $this->pos++;
                if (!$this->parseExpression() || !$this->accept(']')) {
                    return false;
                }
            } else {
                return true;
            }
        }
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
            $this->accept(T_ELLIPSIS);
            if (!$this->parseExpression()) {
                return false;
            }
            if ($this->accept(T_DOUBLE_ARROW) && !$this->parseExpression()) {
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
            $this->accept(T_ELLIPSIS);
            if (!$this->parseExpression()) {
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
