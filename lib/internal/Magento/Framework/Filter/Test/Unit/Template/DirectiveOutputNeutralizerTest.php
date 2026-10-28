<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Filter\Test\Unit\Template;

use Magento\Framework\Filter\Template\DirectiveOutputNeutralizer;
use Magento\Framework\Filter\Template\SignatureProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DirectiveOutputNeutralizerTest extends TestCase
{
    private const SIGNATURE = 'Z0FFbeCU2R8bsVGJuTdkXyiiZBzsaceV';

    /**
     * @var DirectiveOutputNeutralizer
     */
    private $neutralizer;

    protected function setUp(): void
    {
        $signatureProvider = $this->createPartialMock(SignatureProvider::class, ['get']);
        $signatureProvider->method('get')->willReturn(self::SIGNATURE);

        $this->neutralizer = new DirectiveOutputNeutralizer($signatureProvider);
    }

    /**
     * @param string $output
     * @param string $expected
     */
    #[DataProvider('neutralizeDataProvider')]
    public function testNeutralize(string $output, string $expected): void
    {
        $this->assertSame($expected, $this->neutralizer->neutralize($output));
    }

    /**
     * @return array
     */
    public static function neutralizeDataProvider(): array
    {
        $signed = self::SIGNATURE . '{{inlinecss file="css/email-inline.css"}}' . self::SIGNATURE;

        return [
            'no braces passes through' => ['plain <b>text</b>', 'plain <b>text</b>'],
            'opener is encoded' => ['{{var secret}}', '&#123;&#123;var secret}}'],
            'every opener is encoded' => ['{{a}} and {{b}}', '&#123;&#123;a}} and &#123;&#123;b}}'],
            'closer alone is untouched (CSS)' => [
                '@media (max-width:600px){.a{color:red}}',
                '@media (max-width:600px){.a{color:red}}',
            ],
            'closer alone is untouched (JSON)' => [
                '{"*":{"Magento_Ui/js/core/app":{"components":{}}}}',
                '&#123;"*":{"Magento_Ui/js/core/app":{"components":{}}}}',
            ],
            'leading edge brace is encoded' => ['{block class="X"}}', '&#123;block class="X"}}'],
            'trailing edge brace is encoded' => ['name{', 'name&#123;'],
            'single brace output' => ['{', '&#123;'],
            'triple brace leaves one inner brace' => ['a{{{b', 'a&#123;&#123;{b'],
            'signed deferred directive is preserved' => [
                'before {{x}} ' . $signed . ' after {{y}}',
                'before &#123;&#123;x}} ' . $signed . ' after &#123;&#123;y}}',
            ],
            'adjacent signed spans are both preserved' => [
                $signed . $signed . '{{z}}',
                $signed . $signed . '&#123;&#123;z}}',
            ],
            'segments adjacent to signed span have edges encoded' => [
                'a{' . $signed . '{b',
                'a&#123;' . $signed . '&#123;b',
            ],
            'unpaired signature is treated as data' => [
                self::SIGNATURE . '{{x}}',
                self::SIGNATURE . '&#123;&#123;x}}',
            ],
        ];
    }

    public function testNeutralizeIsIdempotent(): void
    {
        $once = $this->neutralizer->neutralize('{{var a}}{ {{var b}} }{');

        $this->assertSame($once, $this->neutralizer->neutralize($once));
    }

    public function testDisabledNeutralizerPassesOutputThrough(): void
    {
        $signatureProvider = $this->createPartialMock(SignatureProvider::class, ['get']);
        $signatureProvider->expects($this->never())->method('get');

        $neutralizer = new DirectiveOutputNeutralizer($signatureProvider, false);

        $this->assertFalse($neutralizer->isEnabled());
        $this->assertSame('{{var secret}}', $neutralizer->neutralize('{{var secret}}'));
    }

    public function testEnabledByDefault(): void
    {
        $this->assertTrue($this->neutralizer->isEnabled());
    }
}
