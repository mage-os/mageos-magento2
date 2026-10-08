<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Translation\Test\Unit\Model\Inline;

use Laminas\Filter\FilterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\State;
use Magento\Framework\Escaper;
use Magento\Framework\Filter\Input\MaliciousCode;
use Magento\Framework\Filter\Input\PurifierInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Translate\InlineInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Translation\Model\Inline\CacheManager;
use Magento\Translation\Model\Inline\Parser;
use Magento\Translation\Model\ResourceModel\StringUtils;
use Magento\Translation\Model\ResourceModel\StringUtilsFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    private $model;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var InlineInterface|MockObject
     */
    private $translateInlineMock;

    /**
     * @var TypeListInterface|MockObject
     */
    private $appCacheMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var StoreInterface|MockObject
     */
    private $storeMock;

    /**
     * @var FilterInterface|MockObject
     */
    private $inputFilterMock;

    /**
     * @var StringUtilsFactory|MockObject
     */
    private $resourceFactoryMock;

    /**
     * @var State|MockObject
     */
    private $appStateMock;

    /**
     * @var StringUtils|MockObject
     */
    private $resourceMock;

    /**
     * @var CacheManager|MockObject
     */
    private $cacheManagerMock;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $objects = [
            [
                \Magento\Framework\Translate\InlineInterface::class,
                $this->createMock(\Magento\Framework\Translate\InlineInterface::class)
            ]
        ];
        $this->objectManager->prepareObjectManager($objects);

        $this->translateInlineMock = $this->createMock(InlineInterface::class);
        $this->appCacheMock = $this->createMock(TypeListInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->storeMock = $this->createMock(StoreInterface::class);
        $this->storeManagerMock->method('getStore')
            ->willReturn($this->storeMock);
        $this->resourceFactoryMock = $this->getMockBuilder(
            StringUtilsFactory::class
        )
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $this->resourceMock = $this->getMockBuilder(StringUtils::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inputFilterMock = $this->createMock(FilterInterface::class);

        $this->resourceFactoryMock->method('create')
            ->willReturn($this->resourceMock);
        $this->cacheManagerMock = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->appStateMock = $this->getMockBuilder(State::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $this->objectManager->getObject(
            Parser::class,
            [
                'resource' => $this->resourceFactoryMock,
                'storeManager' => $this->storeManagerMock,
                'inputFilter' => $this->inputFilterMock,
                'appState' => $this->appStateMock,
                'appCache' => $this->appCacheMock,
                'translateInline' => $this->translateInlineMock,
                'cacheManager' => $this->cacheManagerMock,
                'escaper' => $this->getMockEscaper()
            ]
        );
    }

    public function testProcessAjaxPostNotAllowed()
    {
        $expected = ['inline' => 'not allowed'];
        $this->translateInlineMock->expects($this->once())
            ->method('isAllowed')
            ->willReturn(false);
        $this->assertEquals($expected, $this->model->processAjaxPost([]));
    }

    public function testProcessAjaxPost()
    {
        $this->translateInlineMock->expects($this->once())
            ->method('isAllowed')
            ->willReturn(true);
        $this->model->processAjaxPost([]);
    }

    public function testProcessAjaxPostKeepsRelativeLinkAndRemovesExternalLink(): void
    {
        $this->translateInlineMock->method('isAllowed')->willReturn(true);
        $this->storeMock->method('getId')->willReturn(1);
        $this->appStateMock->method('getAreaCode')->willReturn('frontend');
        (new \ReflectionProperty(Parser::class, 'normalizer'))->setValue($this->model, new \Normalizer());

        $purifierConfig = \HTMLPurifier_Config::createDefault();
        $purifierConfig->set('Cache.DefinitionImpl', null);
        $purifier = new \HTMLPurifier($purifierConfig);
        $purifierAdapter = $this->createMock(PurifierInterface::class);
        $purifierAdapter->method('purify')->willReturnCallback([$purifier, 'purify']);
        (new \ReflectionProperty(Parser::class, '_inputFilter'))
            ->setValue($this->model, new MaliciousCode($purifierAdapter));

        $saved = [];
        $this->resourceMock->expects($this->exactly(2))
            ->method('saveTranslate')
            ->willReturnCallback(function ($original, $custom, $locale, $storeId) use (&$saved): void {
                $this->assertNull($locale);
                $this->assertSame(1, $storeId);
                $saved[$original] = $custom;
            });

        $this->model->processAjaxPost([
            ['original' => 'relative', 'custom' => '<a href="page.html">Guide</a>', 'perstore' => 1],
            ['original' => 'external', 'custom' => '<a href="https://outside.example">Guide</a>', 'perstore' => 1],
        ]);

        $this->assertSame(
            ['relative' => '<a href="page.html">Guide</a>', 'external' => 'Guide'],
            $saved
        );
    }

    public function testRelativeFilenameLinkIsPreserved(): void
    {
        $result = $this->removeExternalLinks('<a href="page.html">Guide</a>');

        $this->assertSame('<a href="page.html">Guide</a>', $result);
    }

    public function testExternalLinkIsStillRemoved(): void
    {
        $result = $this->removeExternalLinks('<a href="https://example.com">Guide</a>');

        $this->assertSame('Guide', $result);
    }

    public function testHtmlParsingRestoresLibxmlErrorMode(): void
    {
        $previousMode = libxml_use_internal_errors(false);
        try {
            $this->removeExternalLinks('<a href="page.html">Guide</a>');

            $this->assertFalse(libxml_use_internal_errors());
        } finally {
            libxml_use_internal_errors($previousMode);
        }
    }

    /**
     * @return void
     */
    public function testProcessResponseBodyString(): void
    {
        $html = file_get_contents(__DIR__ . '/_files/input.html');
        $expectedOutput = file_get_contents(__DIR__ . '/_files/output.html');
        $actualOutput = $this->model->processResponseBodyString($html);
        $this->assertEquals($expectedOutput, $actualOutput);
    }

    private function removeExternalLinks(string $html): string
    {
        (new \ReflectionProperty(Parser::class, 'normalizer'))->setValue($this->model, null);

        return (new \ReflectionMethod(Parser::class, 'removeExternalLinks'))->invoke($this->model, $html);
    }

    /**
     * @return Escaper
     */
    private function getMockEscaper(): Escaper
    {
        $escaper = new Escaper();
        $reflection = new \ReflectionClass($escaper);
        $reflectionProperty = $reflection->getProperty('escaper');
        $reflectionProperty->setValue($escaper, new \Magento\Framework\ZendEscaper());
        return $escaper;
    }
}
