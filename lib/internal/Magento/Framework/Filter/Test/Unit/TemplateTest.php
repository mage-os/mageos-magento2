<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Filter\Test\Unit;

use Magento\Framework\DataObject;
use Magento\Framework\Filter\DirectiveProcessor\DependDirective;
use Magento\Framework\Filter\DirectiveProcessor\IfDirective;
use Magento\Framework\Filter\DirectiveProcessor\LegacyDirective;
use Magento\Framework\Filter\DirectiveProcessor\TemplateDirective;
use Magento\Framework\Filter\DirectiveProcessorInterface;
use Magento\Framework\Filter\Template;
use Magento\Framework\Filter\Template\DirectiveOutputNeutralizer;
use Magento\Framework\Filter\Template\FilteringDepthMeter;
use Magento\Framework\Filter\Template\SignatureProvider;
use Magento\Framework\Filter\VariableResolverInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;

/**
 * Template Filter test.
 */
class TemplateTest extends TestCase
{
    use MockCreationTrait;

    /**
     * @var Template
     */
    private $templateFilter;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var \Magento\Framework\Filter\Template\SignatureProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $signatureProvider;

    /**
     * @var \Magento\Framework\Filter\Template\FilteringDepthMeter|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filteringDepthMeter;

    /**
     * @var array
     */
    private $listClasses = [
        DependDirective::class,
        IfDirective::class,
        TemplateDirective::class,
        LegacyDirective::class
    ];

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $objects = [];
        foreach ($this->listClasses as $className) {
            $classMock = $this->getMockBuilder($className)
                ->disableOriginalConstructor()
                ->onlyMethods([])
                ->getMock();
            $objects[] = [$className,$classMock];
        }
        $objectManager->prepareObjectManager($objects);

        $this->store = $objectManager->getObject(Store::class);

        $this->signatureProvider = $this->createPartialMock(
            \Magento\Framework\Filter\Template\SignatureProvider::class,
            ['get']
        );

        $this->signatureProvider->expects($this->any())
            ->method('get')
            ->willReturn('Z0FFbeCU2R8bsVGJuTdkXyiiZBzsaceV');

        $this->filteringDepthMeter = $this->createPartialMock(
            \Magento\Framework\Filter\Template\FilteringDepthMeter::class,
            ['showMark']
        );

        $this->templateFilter = $objectManager->getObject(
            \Magento\Framework\Filter\Template::class,
            [
                'signatureProvider' => $this->signatureProvider,
                'filteringDepthMeter' => $this->filteringDepthMeter,
                'directiveOutputNeutralizer' => new DirectiveOutputNeutralizer($this->signatureProvider)
            ]
        );
    }

    /**
     * @covers \Magento\Framework\Filter\Template::afterFilter
     * @covers \Magento\Framework\Filter\Template::addAfterFilterCallback
     */
    public function testAfterFilter()
    {
        $value = 'test string';
        $expectedResult = 'TEST STRING';

        $this->filteringDepthMeter->expects($this->any())
            ->method('showMark')
            ->willReturn(1);

        // Build arbitrary object to pass into the addAfterFilterCallback method
        $callbackObject = $this->createPartialMockWithReflection(
            \stdClass::class,
            ['afterFilterCallbackMethod']
        );

        $callbackObject->expects($this->once())
            ->method('afterFilterCallbackMethod')
            ->with($value)
            ->willReturn($expectedResult);

        // Add callback twice to ensure that the check in addAfterFilterCallback prevents the callback from being called
        // more than once
        $this->templateFilter->addAfterFilterCallback([$callbackObject, 'afterFilterCallbackMethod']);
        $this->templateFilter->addAfterFilterCallback([$callbackObject, 'afterFilterCallbackMethod']);

        $this->assertEquals($expectedResult, $this->templateFilter->filter($value));
    }

    /**
     * @covers \Magento\Framework\Filter\Template::afterFilter
     * @covers \Magento\Framework\Filter\Template::addAfterFilterCallback
     * @covers \Magento\Framework\Filter\Template::resetAfterFilterCallbacks
     */
    public function testAfterFilterCallbackReset()
    {
        $value = 'test string';
        $expectedResult = 'TEST STRING';

        $this->filteringDepthMeter->expects($this->any())
            ->method('showMark')
            ->willReturn(1);

        // Build arbitrary object to pass into the addAfterFilterCallback method
        $callbackObject = $this->createPartialMockWithReflection(
            \stdClass::class,
            ['afterFilterCallbackMethod']
        );

        $callbackObject->expects($this->once())
            ->method('afterFilterCallbackMethod')
            ->with($value)
            ->willReturn($expectedResult);

        $this->templateFilter->addAfterFilterCallback([$callbackObject, 'afterFilterCallbackMethod']);

        // Callback should run and filter content
        $this->assertEquals($expectedResult, $this->templateFilter->filter($value));

        // Callback should *not* run as callbacks should be reset
        $this->assertEquals($value, $this->templateFilter->filter($value));
    }

    /**
     * @param $type
     * @return array
     */
    public function getTemplateAndExpectedResults($type)
    {
        switch ($type) {
            case 'noLoopTag':
                $template = $expected = '';
                break;
            case 'noBodyTag':
                $template = <<<TEMPLATE
<ul>
{{for item in order.all_visible_items}}{{/for}}
</ul>
TEMPLATE;
                $expected = <<<TEMPLATE
<ul>
{{for item in order.all_visible_items}}{{/for}}
</ul>
TEMPLATE;
                break;
            case 'noItemTag':
                $template = <<<TEMPLATE
<ul>
{{for in order.all_visible_items}}
    <li>
        {{var loop.index}} name: {{var thing.name}}, lastname: {{var thing.lastname}}, age: {{var thing.age}}
    </li>
{{/for}}
</ul>
TEMPLATE;
                $expected = <<<TEMPLATE
<ul>
{{for in order.all_visible_items}}
    <li>
         name: , lastname: , age:
    </li>
{{/for}}
</ul>
TEMPLATE;
                break;
            case 'noItemNoBodyTag':
                $template = <<<TEMPLATE
<ul>
{{for in order.all_visible_items}}

{{/for}}
</ul>
TEMPLATE;
                $expected = <<<TEMPLATE
<ul>
{{for in order.all_visible_items}}

{{/for}}
</ul>
TEMPLATE;
                break;
            case 'noItemNoDataNoBodyTag':
                $template = <<<TEMPLATE
<ul>
{{for in }}

{{/for}}
</ul>
TEMPLATE;
                $expected = <<<TEMPLATE
<ul>
{{for in }}

{{/for}}
</ul>
TEMPLATE;
                break;
            default:
                $template = <<<TEMPLATE
<ul>
    {{for item in order.all_visible_items}}
    <li>
        index: {{var loop.index}} sku: {{var item.sku}}
        name: {{var item.name}} price: {{var item.price}} quantity: {{var item.ordered_qty}}
    </li>
    {{/for}}
</ul>
TEMPLATE;
                $expected = <<<TEMPLATE
<ul>

    <li>
        index: 0 sku: ABC123
        name: Product ABC price: 123 quantity: 2
    </li>

    <li>
        index: 1 sku: DOREMI
        name: Product DOREMI price: 456 quantity: 1
    </li>

</ul>
TEMPLATE;
        }
        return [$template, ['order' => $this->getObjectData()], $expected];
    }

    /**
     * @return object
     */
    private function getObjectData()
    {
        $objectManager = new ObjectManager($this);
        $dataObject = $objectManager->getObject(DataObject::class);

        /* $var @dataObject \Magento\Framework\DataObject */

        $visibleItems = [
            [
                'sku' => 'ABC123',
                'name' => 'Product ABC',
                'price' => '123',
                'ordered_qty' => '2'
            ],
            [
                'sku' => 'DOREMI',
                'name' => 'Product DOREMI',
                'price' => '456',
                'ordered_qty' => '1'
            ]
        ];

        $dataObject->setAllVisibleItems($visibleItems);
        return $dataObject;
    }

    /**
     * Resolved directive output is data: a "{{" inside it must not be parsed as a directive by a
     * later top-level filter() call, while directives in the template text itself still resolve
     * (the Catalog\Helper\Output case, where an attribute value is the template).
     */
    public function testResolvedOutputIsNotReparsedByLaterFilter(): void
    {
        $filter = $this->createFilter(
            [$this->createVarProcessor()],
            new FilteringDepthMeter(),
            new DirectiveOutputNeutralizer($this->signatureProvider)
        );
        $filter->setVariables(['greeting' => 'Hi', 'payload' => '{{var secret}}', 'secret' => 'LEAKED']);

        $first = $filter->filter('{{var greeting}} {{var payload}}');

        $this->assertSame('Hi &#123;&#123;var secret}}', $first);
        $this->assertSame($first, $filter->filter($first));
    }

    /**
     * Within a single filter() call, applyDirectivesResults() replaces every occurrence of a
     * directive's text. A value that repeats a sibling directive must not have that copy resolved.
     */
    public function testSiblingDirectiveCopiedThroughResolvedOutputIsNotResolvedInSameCall(): void
    {
        $filter = $this->createFilter(
            [$this->createVarProcessor()],
            new FilteringDepthMeter(),
            new DirectiveOutputNeutralizer($this->signatureProvider)
        );
        $filter->setVariables(['payload' => '{{var secret}}', 'secret' => 'LEAKED']);

        $this->assertSame('&#123;&#123;var secret}} LEAKED', $filter->filter('{{var payload}} {{var secret}}'));
    }

    /**
     * Two adjacent substitutions must not be able to assemble a "{{" opener between them.
     */
    public function testEdgeBracesFromAdjacentDirectivesCannotFormDirective(): void
    {
        $filter = $this->createFilter(
            [$this->createVarProcessor()],
            new FilteringDepthMeter(),
            new DirectiveOutputNeutralizer($this->signatureProvider)
        );
        $filter->setVariables(['a' => 'x{', 'b' => '{var secret}}', 'secret' => 'LEAKED']);

        $first = $filter->filter('{{var a}}{{var b}}');

        $this->assertSame('x&#123;&#123;var secret}}', $first);
        $this->assertSame($first, $filter->filter($first));
    }

    /**
     * A deferred directive returned by a child scope inside a resolved include (the {{template}}
     * plus {{inlinecss}} shape) must stay intact and be processed by the parent, while "}}" in
     * legitimate CSS output and "{{" from resolved data are handled as data.
     */
    public function testSignedDeferredDirectiveInsideResolvedOutputIsStillProcessed(): void
    {
        $depthMeter = new FilteringDepthMeter();
        $neutralizer = new DirectiveOutputNeutralizer($this->signatureProvider);

        $child = $this->createFilter(
            [$this->createVarProcessor(), $this->createDeferProcessor(null)],
            $depthMeter,
            $neutralizer
        );
        $child->setVariables(['payload' => '{{var secret}}', 'secret' => 'LEAKED']);
        $childText = '<style>.a{color:red}}</style>{{defer}}{{var payload}}';

        $parent = $this->createFilter(
            [
                $this->createIncludeProcessor($child, $childText),
                $this->createVarProcessor(),
                $this->createDeferProcessor('DEFERRED-OK'),
            ],
            $depthMeter,
            $neutralizer
        );
        $parent->setVariables(['secret' => 'LEAKED']);

        $result = $parent->filter('[{{include}}]');

        $this->assertSame('[<style>.a{color:red}}</style>DEFERRED-OK&#123;&#123;var secret}}]', $result);
        $this->assertSame(0, $depthMeter->showMark());
    }

    /**
     * With the neutralizer disabled the previous behavior is restored: resolved output is
     * substituted verbatim and a second top-level filter() re-parses it.
     */
    public function testDisabledNeutralizerRestoresPriorBehavior(): void
    {
        $filter = $this->createFilter(
            [$this->createVarProcessor()],
            new FilteringDepthMeter(),
            new DirectiveOutputNeutralizer($this->signatureProvider, false)
        );
        $filter->setVariables(['payload' => '{{var secret}}', 'secret' => 'LEAKED']);

        $first = $filter->filter('{{var payload}}');

        $this->assertSame('{{var secret}}', $first);
        $this->assertSame('LEAKED', $filter->filter($first));
        $this->assertSame('LEAKED LEAKED', $filter->filter('{{var payload}} {{var secret}}'));
    }

    /**
     * A directive that merely comes back unchanged from a child scope is not deferred: it must
     * not be signed, and the parent must not execute it.
     */
    public function testUndeclaredUnchangedDirectiveIsNotSigned(): void
    {
        $depthMeter = new FilteringDepthMeter();
        $neutralizer = new DirectiveOutputNeutralizer($this->signatureProvider);

        $child = $this->createFilter(
            [$this->createPassThroughProcessor()],
            $depthMeter,
            $neutralizer
        );

        $parent = $this->createFilter(
            [
                $this->createIncludeProcessor($child, 'a {{passthrough}} b'),
                $this->createPassThroughProcessor('EXECUTED'),
            ],
            $depthMeter,
            $neutralizer
        );

        $result = $parent->filter('[{{include}}]');

        $this->assertStringNotContainsString('EXECUTED', $result);
        $this->assertStringNotContainsString($this->signatureProvider->get(), $result);
        $this->assertSame(0, $depthMeter->showMark());
    }

    /**
     * Declared deferrals recorded by a child invocation must not leak into the parent
     * invocation's own signing pass.
     */
    public function testDeclaredDeferralsDoNotLeakAcrossInvocations(): void
    {
        $depthMeter = new FilteringDepthMeter();
        $neutralizer = new DirectiveOutputNeutralizer($this->signatureProvider);

        $inner = $this->createFilter(
            [$this->createDeferProcessor(null)],
            $depthMeter,
            $neutralizer
        );
        $middle = $this->createFilter(
            [
                $this->createIncludeProcessor($inner, '{{defer}}'),
                $this->createPassThroughProcessor(),
            ],
            $depthMeter,
            $neutralizer
        );
        $outer = $this->createFilter(
            [
                $this->createIncludeProcessor($middle, '{{include}} {{passthrough}}'),
                $this->createDeferProcessor('DEFERRED-OK'),
                $this->createPassThroughProcessor('EXECUTED'),
            ],
            $depthMeter,
            $neutralizer
        );

        $result = $outer->filter('[{{include}}]');

        $this->assertStringContainsString('DEFERRED-OK', $result);
        $this->assertStringNotContainsString('EXECUTED', $result);
        $this->assertSame(0, $depthMeter->showMark());
    }

    /**
     * {{passthrough}} processor: returns the directive unchanged without declaring deferral
     * when $resolved is null, otherwise resolves to $resolved.
     *
     * @param string|null $resolved
     * @return DirectiveProcessorInterface
     */
    private function createPassThroughProcessor(?string $resolved = null): DirectiveProcessorInterface
    {
        return new class ($resolved) implements DirectiveProcessorInterface {
            /**
             * @var string|null
             */
            private $resolved;

            public function __construct(?string $resolved)
            {
                $this->resolved = $resolved;
            }

            public function process(array $construction, Template $filter, array $templateVariables): string
            {
                return $this->resolved ?? $construction[0];
            }

            public function getRegularExpression(): string
            {
                return '/{{passthrough}}/';
            }
        };
    }

    /**
     * @param DirectiveProcessorInterface[] $processors
     * @param FilteringDepthMeter $depthMeter
     * @param DirectiveOutputNeutralizer $neutralizer
     * @return Template
     */
    private function createFilter(
        array $processors,
        FilteringDepthMeter $depthMeter,
        DirectiveOutputNeutralizer $neutralizer
    ): Template {
        return new Template(
            new StringUtils(),
            [],
            $processors,
            $this->createMock(VariableResolverInterface::class),
            $this->signatureProvider,
            $depthMeter,
            $neutralizer
        );
    }

    /**
     * Minimal {{var name}} processor that returns the variable verbatim, like VarDirective.
     *
     * @return DirectiveProcessorInterface
     */
    private function createVarProcessor(): DirectiveProcessorInterface
    {
        return new class implements DirectiveProcessorInterface {
            public function process(array $construction, Template $filter, array $templateVariables): string
            {
                return (string)($templateVariables[$construction[1]] ?? '');
            }

            public function getRegularExpression(): string
            {
                return '/{{var (\w+)}}/';
            }
        };
    }

    /**
     * {{defer}} processor: declares deferral and passes the directive through unchanged (to be
     * signed and deferred to the parent) when $resolved is null, otherwise resolves to $resolved.
     *
     * @param string|null $resolved
     * @return DirectiveProcessorInterface
     */
    private function createDeferProcessor(?string $resolved): DirectiveProcessorInterface
    {
        return new class ($resolved) implements DirectiveProcessorInterface {
            /**
             * @var string|null
             */
            private $resolved;

            public function __construct(?string $resolved)
            {
                $this->resolved = $resolved;
            }

            public function process(array $construction, Template $filter, array $templateVariables): string
            {
                if ($this->resolved === null) {
                    $filter->deferToParent($construction[0]);
                }

                return $this->resolved ?? $construction[0];
            }

            public function getRegularExpression(): string
            {
                return '/{{defer}}/';
            }
        };
    }

    /**
     * {{include}} processor: filters $text through a child filter, like the {{template}} directive.
     *
     * @param Template $child
     * @param string $text
     * @return DirectiveProcessorInterface
     */
    private function createIncludeProcessor(Template $child, string $text): DirectiveProcessorInterface
    {
        return new class ($child, $text) implements DirectiveProcessorInterface {
            /**
             * @var Template
             */
            private $child;

            /**
             * @var string
             */
            private $text;

            public function __construct(Template $child, string $text)
            {
                $this->child = $child;
                $this->text = $text;
            }

            public function process(array $construction, Template $filter, array $templateVariables): string
            {
                return $this->child->filter($this->text);
            }

            public function getRegularExpression(): string
            {
                return '/{{include}}/';
            }
        };
    }
}
