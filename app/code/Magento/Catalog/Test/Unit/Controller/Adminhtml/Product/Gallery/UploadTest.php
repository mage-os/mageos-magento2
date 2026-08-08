<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Controller\Adminhtml\Product\Gallery;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Gallery\Upload;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Image\Adapter\AdapterInterface;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\MediaStorage\Model\File\Uploader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UploadTest extends TestCase
{
    private const BASE_TMP_MEDIA_PATH = 'catalog/product/tmp';

    private const ABSOLUTE_TMP_MEDIA_PATH = '/var/www/html/pub/media/catalog/product/tmp';

    /** @var Upload */
    private $controller;

    /** @var Uploader|MockObject */
    private $uploader;

    /** @var Config|MockObject */
    private $productMediaConfig;

    /** @var Raw|MockObject */
    private $response;

    /** @var string|null */
    private $responseContents;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->uploader = $this->getMockBuilder(Uploader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('create')
            ->with(Uploader::class, ['fileId' => 'image'])
            ->willReturn($this->uploader);

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('getObjectManager')->willReturn($objectManager);
        $context->method('getEventManager')->willReturn($this->createMock(ManagerInterface::class));

        $this->response = $this->getMockBuilder(Raw::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->response->method('setHeader')->willReturnSelf();
        $this->response->method('setContents')
            ->willReturnCallback(function ($contents) {
                $this->responseContents = $contents;
                return $this->response;
            });

        $resultRawFactory = $this->getMockBuilder(RawFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $resultRawFactory->method('create')->willReturn($this->response);

        $adapterFactory = $this->getMockBuilder(AdapterFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adapterFactory->method('create')->willReturn($this->createMock(AdapterInterface::class));

        $directory = $this->createMock(ReadInterface::class);
        $directory->method('getAbsolutePath')
            ->with(self::BASE_TMP_MEDIA_PATH)
            ->willReturn(self::ABSOLUTE_TMP_MEDIA_PATH);

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($directory);

        $this->productMediaConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productMediaConfig->method('getBaseTmpMediaPath')->willReturn(self::BASE_TMP_MEDIA_PATH);

        $this->controller = new Upload(
            $context,
            $resultRawFactory,
            $adapterFactory,
            $filesystem,
            $this->productMediaConfig
        );
    }

    public function testExecuteAcceptsWebpAndAvifExtensions(): void
    {
        $this->uploader->expects($this->once())
            ->method('setAllowedExtensions')
            ->with(['jpg', 'jpeg', 'gif', 'png', 'webp', 'avif']);
        $this->uploader->method('save')
            ->with(self::ABSOLUTE_TMP_MEDIA_PATH)
            ->willReturn([
                'file' => '/m/a/magento_image.webp',
                'tmp_name' => '/tmp/magento_image.webp',
                'path' => self::ABSOLUTE_TMP_MEDIA_PATH,
            ]);
        $this->productMediaConfig->method('getTmpMediaUrl')
            ->with('/m/a/magento_image.webp')
            ->willReturn('http://localhost/media/tmp/catalog/product/m/a/magento_image.webp');

        $result = $this->executeAndDecode();

        $this->assertSame('/m/a/magento_image.webp.tmp', $result['file']);
        $this->assertSame(
            'http://localhost/media/tmp/catalog/product/m/a/magento_image.webp',
            $result['url']
        );
        $this->assertArrayNotHasKey('error', $result);
        $this->assertArrayNotHasKey('tmp_name', $result);
        $this->assertArrayNotHasKey('path', $result);
    }

    public function testExecuteSurfacesImageAdapterMessage(): void
    {
        $this->uploader->method('save')
            ->willThrowException(new \InvalidArgumentException('Wrong file size.'));

        $result = $this->executeAndDecode();

        $this->assertSame('Wrong file size.', $result['error']);
        $this->assertSame(0, $result['errorcode']);
    }

    public function testExecuteKeepsLocalizedExceptionMessageAndCode(): void
    {
        $this->uploader->method('save')
            ->willThrowException(new LocalizedException(new Phrase('File validation failed.'), null, 42));

        $result = $this->executeAndDecode();

        $this->assertSame('File validation failed.', $result['error']);
        $this->assertSame(42, $result['errorcode']);
    }

    public function testExecuteHidesUnexpectedFailures(): void
    {
        $this->uploader->method('save')
            ->willThrowException(new \RuntimeException('Connection to the storage backend was lost'));

        $result = $this->executeAndDecode();

        $this->assertSame('Something went wrong while saving the file(s).', $result['error']);
        $this->assertSame(0, $result['errorcode']);
    }

    /**
     * @return array
     */
    private function executeAndDecode(): array
    {
        $this->assertSame($this->response, $this->controller->execute());

        return json_decode((string)$this->responseContents, true);
    }
}
