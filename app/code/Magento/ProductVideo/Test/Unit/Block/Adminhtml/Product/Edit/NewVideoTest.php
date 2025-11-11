<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ProductVideo\Test\Unit\Block\Adminhtml\Product\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\ElementCreator;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Math\Random;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\ProductVideo\Block\Adminhtml\Product\Edit\NewVideo;
use Magento\ProductVideo\Helper\Media;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NewVideoTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    protected $contextMock;

    /**
     * @var MockObject|UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Random|MockObject
     */
    protected $mathRandom;

    /**
     * @var Registry|MockObject
     */
    protected $registryMock;

    /**
     * @var FormFactory|MockObject
     */
    protected $formFactoryMock;

    /**
     * @var EncoderInterface|MockObject
     */
    protected $jsonEncoderMock;

    /**
     * @var Media|MockObject
     */
    protected $mediaHelper;

    /**
     * @var ElementCreator|MockObject
     */
    protected $elementCreatorMock;

    /**
     * @var JsonHelper|MockObject
     */
    protected $jsonHelperMock;

    /**
     * @var DirectoryHelper|MockObject
     */
    protected $directoryHelperMock;

    /**
     * @var NewVideo|MockObject
     */
    protected $block;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->mediaHelper = $this->createMock(Media::class);
        $this->mathRandom = $this->createMock(Random::class);
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $this->contextMock->method('getMathRandom')->willReturn($this->mathRandom);
        $this->contextMock->method('getUrlBuilder')->willReturn($this->urlBuilder);
        $this->registryMock = $this->createMock(Registry::class);
        $this->formFactoryMock = $this->createMock(FormFactory::class);
        $this->jsonEncoderMock = $this->createMock(EncoderInterface::class);
        $this->elementCreatorMock = $this->createMock(ElementCreator::class);
        $this->jsonHelperMock = $this->createMock(JsonHelper::class);
        $this->directoryHelperMock = $this->createMock(DirectoryHelper::class);

        $this->block = $this->getMockBuilder(NewVideo::class)
            ->onlyMethods(['getHtmlId', 'getWidgetOptions'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetHtmlId()
    {
        $expectedId = 'id_test123';
        $this->block->method('getHtmlId')->willReturn($expectedId);
        $result = $this->block->getHtmlId();
        $this->assertEquals($expectedId, $result);
    }

    public function testGetWidgetOptions()
    {
        $expectedOptions = '{"saveVideoUrl":"test_url","saveRemoteVideoUrl":"test_remote_url","htmlId":"test_id",'
            . '"youTubeApiKey":null,"videoSelector":"#media_gallery_content"}';
        $this->block->method('getWidgetOptions')->willReturn($expectedOptions);
        $result = $this->block->getWidgetOptions();
        $this->assertEquals($expectedOptions, $result);
    }
}
