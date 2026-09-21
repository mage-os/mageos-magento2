<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Controller\Adminhtml\Address;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Controller\Adminhtml\Address\Viewfile;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Url\DecoderInterface;
use Magento\MediaStorage\Helper\File\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ViewfileTest extends TestCase
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var RequestInterface|MockObject */
    private $requestMock;

    /** @var ReadInterface|MockObject */
    private $directoryMock;

    /** @var Filesystem|MockObject */
    private $filesystemMock;

    /** @var Storage|MockObject */
    private $storageMock;

    /** @var DecoderInterface|MockObject */
    private $urlDecoderMock;

    /** @var IoFile|MockObject */
    private $ioFileMock;

    /** @var Raw|MockObject */
    private $resultRawMock;

    /** @var RawFactory|MockObject */
    private $resultRawFactoryMock;

    /** @var FileFactory|MockObject */
    private $fileFactoryMock;

    /** @var Context|MockObject */
    private $contextMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->directoryMock = $this->createMock(ReadInterface::class);
        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->storageMock = $this->createMock(Storage::class);
        $this->urlDecoderMock = $this->createMock(DecoderInterface::class);
        $this->ioFileMock = $this->createMock(IoFile::class);
        $this->resultRawMock = $this->createMock(Raw::class);
        $this->fileFactoryMock = $this->createMock(FileFactory::class);
        $this->resultRawFactoryMock = $this->createPartialMock(RawFactory::class, ['create']);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getResponse')
            ->willReturn($this->createMock(ResponseInterface::class));
    }

    public function testExecuteNoParamsThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Page not found.');

        $this->requestMock->expects($this->any())->method('getParam')->willReturn('');

        $this->createController()->execute();
    }

    /**
     * @param string $file
     * @param string $expectedContentType
     * @return void
     */
    #[DataProvider('imageContentTypeDataProvider')]
    public function testExecuteGetParamImageSetsContentType(string $file, string $expectedContentType): void
    {
        $decodedFile = 'decoded_file';
        $fileName = 'customer_address/' . $file;
        $path = 'absolute/' . $file;

        $this->requestMock->expects($this->any())->method('getParam')
            ->willReturnMap([['file', '', ''], ['image', '', $decodedFile]]);
        $this->urlDecoderMock->expects($this->once())->method('decode')->with($decodedFile)->willReturn($file);

        $this->filesystemMock->expects($this->once())->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->directoryMock);
        $this->directoryMock->expects($this->once())->method('getAbsolutePath')->with($fileName)->willReturn($path);
        $this->directoryMock->expects($this->once())->method('isFile')->with($fileName)->willReturn(true);
        $this->directoryMock->expects($this->once())->method('stat')->with($fileName)
            ->willReturn(['size' => 10, 'mtime' => 10]);

        $this->ioFileMock->expects($this->once())->method('getPathInfo')->with($path)
            ->willReturn(['extension' => pathinfo($file, PATHINFO_EXTENSION), 'basename' => $file]);

        $headers = [];
        $this->resultRawMock->expects($this->any())->method('setHttpResponseCode')->willReturnSelf();
        $this->resultRawMock->expects($this->any())->method('setHeader')
            ->willReturnCallback(
                function ($name, $value) use (&$headers) {
                    $headers[$name] = $value;
                    return $this->resultRawMock;
                }
            );
        $this->resultRawFactoryMock->expects($this->once())->method('create')->willReturn($this->resultRawMock);

        $this->assertSame($this->resultRawMock, $this->createController()->execute());
        $this->assertSame($expectedContentType, $headers['Content-type']);
    }

    /**
     * @return array
     */
    public static function imageContentTypeDataProvider(): array
    {
        return [
            'gif' => ['image.gif', 'image/gif'],
            'jpg' => ['image.jpg', 'image/jpeg'],
            'png' => ['image.png', 'image/png'],
            'webp' => ['image.webp', 'image/webp'],
            'avif' => ['image.avif', 'image/avif'],
            'uppercase webp' => ['image.WEBP', 'image/webp'],
            'unknown' => ['archive.zip', 'application/octet-stream'],
        ];
    }

    public function testExecuteParamFileDownloadsTheFile(): void
    {
        $decodedFile = 'decoded_file';
        $file = 'image.webp';
        $fileName = 'customer_address/' . $file;
        $path = 'absolute/' . $file;

        $this->requestMock->expects($this->atLeastOnce())->method('getParam')->with('file')
            ->willReturn($decodedFile);
        $this->urlDecoderMock->expects($this->once())->method('decode')->with($decodedFile)->willReturn($file);

        $this->filesystemMock->expects($this->once())->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->directoryMock);
        $this->directoryMock->expects($this->once())->method('getAbsolutePath')->with($fileName)->willReturn($path);
        $this->directoryMock->expects($this->once())->method('isFile')->with($fileName)->willReturn(true);

        $this->ioFileMock->expects($this->once())->method('getPathInfo')->with($path)
            ->willReturn(['extension' => 'webp', 'basename' => $file]);

        $fileResponse = $this->createMock(ResponseInterface::class);
        $this->fileFactoryMock->expects($this->once())->method('create')
            ->with($file, ['type' => 'filename', 'value' => $fileName], DirectoryList::MEDIA)
            ->willReturn($fileResponse);

        $this->assertSame($fileResponse, $this->createController()->execute());
    }

    public function testExecuteRejectsTraversalPath(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Page not found.');

        $file = '../../../app/etc/env.php';
        $decodedFile = base64_encode($file);
        $fileName = 'customer_address/' . $file;

        $this->requestMock->expects($this->atLeastOnce())->method('getParam')->with('file')
            ->willReturn($decodedFile);
        $this->urlDecoderMock->expects($this->once())->method('decode')->with($decodedFile)->willReturn($file);

        $this->filesystemMock->expects($this->once())->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->directoryMock);
        $this->directoryMock->expects($this->once())->method('getAbsolutePath')->with($fileName)->willReturn($fileName);

        $this->createController()->execute();
    }

    /**
     * @return Viewfile
     */
    private function createController(): Viewfile
    {
        return $this->objectManager->getObject(
            Viewfile::class,
            [
                'context' => $this->contextMock,
                'fileFactory' => $this->fileFactoryMock,
                'resultRawFactory' => $this->resultRawFactoryMock,
                'urlDecoder' => $this->urlDecoderMock,
                'filesystem' => $this->filesystemMock,
                'storage' => $this->storageMock,
                'ioFile' => $this->ioFileMock,
            ]
        );
    }
}
