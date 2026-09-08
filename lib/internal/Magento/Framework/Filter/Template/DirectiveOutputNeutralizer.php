<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Filter\Template;

/**
 * Encodes directive openers in the output produced by a resolved template directive, so that
 * resolved output is never re-parsed as a directive by a later filtering pass. Signed deferred
 * directives are left untouched.
 *
 * A transformation that decodes HTML entities (such as a DOM round-trip) undoes the encoding;
 * the "enabled" DI argument turns the behavior off for output that cannot tolerate it.
 */
class DirectiveOutputNeutralizer
{
    private const OPENING_DELIMITER = '{{';

    private const ENCODED_OPENING_DELIMITER = '&#123;&#123;';

    private const ENCODED_BRACE = '&#123;';

    /**
     * @var SignatureProvider
     */
    private $signatureProvider;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param SignatureProvider $signatureProvider
     * @param bool $enabled
     */
    public function __construct(SignatureProvider $signatureProvider, bool $enabled = true)
    {
        $this->signatureProvider = $signatureProvider;
        $this->enabled = $enabled;
    }

    /**
     * Whether resolved directive output is neutralized.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Encode directive openers in resolved output, preserving signed deferred directives.
     *
     * @param string $output
     * @return string
     */
    public function neutralize(string $output): string
    {
        if (!$this->enabled || strpos($output, '{') === false) {
            return $output;
        }

        $signature = $this->signatureProvider->get();
        if ($signature === '' || strpos($output, $signature) === false) {
            return $this->encode($output);
        }

        $quotedSignature = preg_quote($signature, '/');
        $parts = preg_split(
            '/(' . $quotedSignature . '.*?' . $quotedSignature . ')/s',
            $output,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );
        if ($parts === false) {
            return $this->encode($output);
        }

        // Odd indexes hold the captured signed spans; even indexes hold the data between them.
        foreach ($parts as $index => $part) {
            if ($index % 2 === 0) {
                $parts[$index] = $this->encode($part);
            }
        }

        return implode('', $parts);
    }

    /**
     * Encode every "{{" and any single "{" at either edge of the segment.
     *
     * @param string $segment
     * @return string
     */
    private function encode(string $segment): string
    {
        if ($segment === '') {
            return $segment;
        }

        $segment = str_replace(self::OPENING_DELIMITER, self::ENCODED_OPENING_DELIMITER, $segment);

        if ($segment[0] === '{') {
            $segment = self::ENCODED_BRACE . substr($segment, 1);
        }
        if (substr($segment, -1) === '{') {
            $segment = substr($segment, 0, -1) . self::ENCODED_BRACE;
        }

        return $segment;
    }
}
