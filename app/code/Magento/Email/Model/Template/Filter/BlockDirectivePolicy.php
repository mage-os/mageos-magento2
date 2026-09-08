<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Email\Model\Template\Filter;

/**
 * Decides which block classes the {{block}} template directive may instantiate.
 *
 * Restriction patterns and exact-class exemptions are supplied through di.xml; a pattern
 * starting with "^" matches the beginning of the class name, any other pattern matches
 * anywhere in it. All matching is case-insensitive against a canonicalized class name.
 */
class BlockDirectivePolicy
{
    /**
     * @var string[]
     */
    private $restrictedPatterns;

    /**
     * @var string[]
     */
    private $allowedClasses;

    /**
     * @param string[] $restrictedPatterns
     * @param string[] $allowedClasses
     */
    public function __construct(
        array $restrictedPatterns = [],
        array $allowedClasses = []
    ) {
        $this->restrictedPatterns = $restrictedPatterns;
        $this->allowedClasses = $allowedClasses;
    }

    /**
     * Whether a class is off limits to the {{block}} directive.
     *
     * @param string $class
     * @return bool
     */
    public function isRestricted(string $class): bool
    {
        $normalized = $this->normalize($class);

        if ($normalized === '') {
            return false;
        }

        foreach ($this->allowedClasses as $allowed) {
            if (strcasecmp($normalized, $this->normalize((string)$allowed)) === 0) {
                return false;
            }
        }

        foreach ($this->restrictedPatterns as $pattern) {
            $pattern = (string)$pattern;

            if ($pattern === '' || $pattern === '^') {
                continue;
            }

            if (strncmp($pattern, '^', 1) === 0) {
                if (stripos($normalized, substr($pattern, 1)) === 0) {
                    return true;
                }
            } elseif (stripos($normalized, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Canonicalize separator spelling so alternate forms cannot evade matching.
     *
     * @param string $class
     * @return string
     */
    private function normalize(string $class): string
    {
        $normalized = str_replace('/', '\\', trim($class));
        while (strpos($normalized, '\\\\') !== false) {
            $normalized = str_replace('\\\\', '\\', $normalized);
        }

        return ltrim($normalized, '\\');
    }
}
