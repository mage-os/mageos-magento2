<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\View\Test\Unit;

use Magento\Framework\App\State;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\Design\Theme\ResolverInterface;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\Condition\ConditionFactory;
use Magento\Framework\View\Layout\Data\Structure as LayoutStructure;
use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\Layout\Generator\Context as GeneratorContext;
use Magento\Framework\View\Layout\Generator\ContextFactory as GeneratorContextFactory;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\ProcessorFactory;
use Magento\Framework\View\Layout\Reader\Container as ContainerReader;
use Magento\Framework\View\Layout\Reader\Context as ReaderContext;
use Magento\Framework\View\Layout\Reader\ContextFactory as ReaderContextFactory;
use Magento\Framework\View\Layout\ReaderFactory;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\Layout\ScheduledStructure;
use Magento\Framework\View\Layout\ScheduledStructure\Helper;
use Magento\Framework\View\Model\Layout\Merge;
use Magento\Framework\View\Page\Config\Structure as PageConfigStructure;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Building the same layout instance twice in one request must produce the same structure.
 *
 * @see https://github.com/mage-os/mageos-magento2/issues/330
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LayoutRebuildTest extends TestCase
{
    /**
     * A child is scheduled through a reference before its parent container is declared.
     */
    private const LAYOUT_XML = '<layout>'
        . '<referenceContainer name="head.additional"><container name="head.scripts"/></referenceContainer>'
        . '<container name="root"><container name="head.additional"/></container>'
        . '</layout>';

    public function testRebuildKeepsStructureIntact(): void
    {
        $state = $this->createMock(State::class);
        $logger = $this->createMock(LoggerInterface::class);
        $structure = new LayoutStructure($logger, $state);
        $helper = new Helper($logger, $state);

        $readerFactory = $this->createMock(ReaderFactory::class);
        $readerPool = new ReaderPool($readerFactory, ['container' => ContainerReader::class]);
        $readerFactory->method('create')->willReturn(new ContainerReader($helper, $readerPool));

        $generatorPool = new GeneratorPool(
            $helper,
            $this->createMock(ConditionFactory::class),
            $logger,
            [],
            $state
        );

        $readerContextFactory = $this->createMock(ReaderContextFactory::class);
        $readerContextFactory->method('create')->willReturnCallback(
            fn (array $data = []) => new ReaderContext(
                $data['scheduledStructure'] ?? new ScheduledStructure(),
                $data['pageConfigStructure'] ?? new PageConfigStructure()
            )
        );

        $processor = $this->createMock(Merge::class);
        $processor->method('asSimplexml')
            ->willReturnCallback(fn () => simplexml_load_string(self::LAYOUT_XML, Element::class));
        $processor->method('getCacheId')->willReturn('layout_rebuild_test');
        $processor->method('getHandles')->willReturn([]);
        $processorFactory = $this->createMock(ProcessorFactory::class);
        $processorFactory->method('create')->willReturn($processor);

        $layout = (new ObjectManagerHelper($this))->getObject(
            Layout::class,
            [
                'processorFactory' => $processorFactory,
                'structure' => $structure,
                'themeResolver' => $this->createMock(ResolverInterface::class),
                'readerPool' => $readerPool,
                'generatorPool' => $generatorPool,
                'cache' => $this->createMock(FrontendInterface::class),
                'readerContextFactory' => $readerContextFactory,
                'generatorContextFactory' => $this->createGeneratorContextFactory($structure),
                'appState' => $state,
                'logger' => $logger,
                'serializer' => $this->createMock(SerializerInterface::class),
            ]
        );

        $layout->generateXml();
        $layout->generateElements();
        $this->assertSame('head.additional', $structure->getParentId('head.scripts'));

        $layout->generateXml();
        $layout->generateElements();
        $this->assertSame('head.additional', $structure->getParentId('head.scripts'));
        $this->assertSame('root', $structure->getParentId('head.additional'));
    }

    /**
     * @param LayoutStructure $structure
     * @return GeneratorContextFactory
     */
    private function createGeneratorContextFactory(LayoutStructure $structure): GeneratorContextFactory
    {
        $factory = $this->createMock(GeneratorContextFactory::class);
        $factory->method('create')->willReturnCallback(
            fn (array $data) => new GeneratorContext($structure, $data['layout'])
        );
        return $factory;
    }
}
