<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Email\Test\Unit\Model\Template\Filter;

use Magento\Email\Model\Template\Filter\BlockDirectivePolicy;
use PHPUnit\Framework\TestCase;

class BlockDirectivePolicyTest extends TestCase
{
    /**
     * Mirrors the default configuration in Magento/Email/etc/di.xml.
     */
    private const DEFAULT_PATTERNS = [
        '\\Block\\Adminhtml\\',
        '\\Block\\Backend\\',
        '\\Block\\System\\Config\\',
        '^Magento\\Backend\\Block\\',
        '^Magento\\User\\Block\\',
    ];

    public function testRestrictsDefaultPatternsInAllSeparatorForms()
    {
        $policy = new BlockDirectivePolicy(self::DEFAULT_PATTERNS);
        $bs = chr(92);

        $restricted = [
            "Magento{$bs}Backend{$bs}Block{$bs}Widget{$bs}Grid{$bs}ColumnSet",
            "{$bs}Magento{$bs}Backend{$bs}Block{$bs}Widget{$bs}Grid{$bs}ColumnSet",
            "Magento{$bs}{$bs}Backend{$bs}{$bs}Block{$bs}{$bs}Widget{$bs}{$bs}Grid{$bs}{$bs}ColumnSet",
            "Magento/Backend/Block/Widget/Grid/ColumnSet",
            "  Magento{$bs}Email{$bs}Block{$bs}Adminhtml{$bs}Template{$bs}Preview  ",
            "Magento{$bs}User{$bs}Block{$bs}Role{$bs}Grid{$bs}User",
            "Magento{$bs}Indexer{$bs}Block{$bs}Backend{$bs}Container",
            "Vendor{$bs}Module{$bs}Block{$bs}Backend{$bs}Anything",
            "Magento{$bs}Config{$bs}Block{$bs}System{$bs}Config{$bs}Edit",
            "Magento{$bs}Ups{$bs}Block{$bs}Backend{$bs}System{$bs}CarrierConfig",
            "magento{$bs}backend{$bs}block{$bs}widget{$bs}grid{$bs}columnset",
        ];
        foreach ($restricted as $class) {
            $this->assertTrue($policy->isRestricted($class), $class);
        }

        $allowed = [
            "Magento{$bs}Cms{$bs}Block{$bs}Block",
            "Magento{$bs}Framework{$bs}View{$bs}Element{$bs}Template",
            "Vendor{$bs}Module{$bs}Block{$bs}SystemStatus",
            "Vendor{$bs}Module{$bs}Block{$bs}BackendCompat{$bs}Widget",
            '',
            '   ',
        ];
        foreach ($allowed as $class) {
            $this->assertFalse($policy->isRestricted($class), var_export($class, true));
        }
    }

    public function testPrefixPatternDoesNotMatchMidName()
    {
        $policy = new BlockDirectivePolicy(['^Magento\\Backend\\Block\\']);

        $this->assertFalse($policy->isRestricted('Vendor\\Magento\\Backend\\Block\\Foo'));
        $this->assertTrue($policy->isRestricted('Magento\\Backend\\Block\\Foo'));
    }

    public function testExactAllowedClassIsExemptFromPatterns()
    {
        $policy = new BlockDirectivePolicy(
            self::DEFAULT_PATTERNS,
            ['Vendor\\CheckoutFields\\Block\\Adminhtml\\Order\\View\\Fields']
        );

        $this->assertFalse(
            $policy->isRestricted('Vendor\\CheckoutFields\\Block\\Adminhtml\\Order\\View\\Fields')
        );
        $this->assertFalse(
            $policy->isRestricted('Vendor/CheckoutFields/Block/Adminhtml/Order/View/Fields'),
            'Exemption must match the same separator forms the deny patterns do'
        );
        $this->assertTrue(
            $policy->isRestricted('Vendor\\CheckoutFields\\Block\\Adminhtml\\Order\\View\\FieldsExtra'),
            'Exemption must not extend beyond the exact class'
        );
        $this->assertTrue(
            $policy->isRestricted('Magento\\Backend\\Block\\Widget\\Grid\\ColumnSet')
        );
    }

    public function testExemptionAppliesToGeneratedInterceptor()
    {
        $policy = new BlockDirectivePolicy(
            self::DEFAULT_PATTERNS,
            ['Vendor\\CheckoutFields\\Block\\Adminhtml\\Order\\View\\Fields']
        );

        $this->assertFalse(
            $policy->isRestricted('Vendor\\CheckoutFields\\Block\\Adminhtml\\Order\\View\\Fields\\Interceptor'),
            'A block with plugins is instantiated as its interceptor; the exemption must still apply'
        );
        $this->assertFalse(
            $policy->isRestricted('Vendor\\CheckoutFields\\Block\\Adminhtml\\Order\\View\\Fields\\interceptor'),
            'The interceptor suffix is matched case-insensitively'
        );
    }

    public function testInterceptorCannotShedARestriction()
    {
        $policy = new BlockDirectivePolicy(self::DEFAULT_PATTERNS);

        $this->assertTrue(
            $policy->isRestricted('Magento\\Backend\\Block\\Widget\\Grid\\ColumnSet\\Interceptor')
        );
        $this->assertTrue(
            $policy->isRestricted('Vendor\\Module\\Block\\Adminhtml\\Foo\\Interceptor')
        );
        $this->assertTrue(
            $policy->isRestricted('Vendor\\Module\\Block\\Adminhtml\\Interceptor'),
            'Unwrapping must never move a class out of a restricted namespace'
        );
    }

    public function testUnconfiguredPolicyRestrictsNothing()
    {
        $policy = new BlockDirectivePolicy();

        $this->assertFalse($policy->isRestricted('Magento\\Backend\\Block\\Widget\\Grid\\ColumnSet'));
    }
}
