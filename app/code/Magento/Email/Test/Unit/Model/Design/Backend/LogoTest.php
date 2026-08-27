<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Email\Test\Unit\Model\Design\Backend;

use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Email\Model\Design\Backend\Logo;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LogoTest extends TestCase
{
    /** @var Logo */
    private $logoBackend;

    /** @var File|MockObject */
    private $ioFileSystem;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->ioFileSystem = $this->getMockObject(File::class);
        $this->logoBackend = new Logo(
            $this->getMockObject(Context::class),
            $this->getMockObject(Registry::class),
            $this->getMockObject(ScopeConfigInterface::class),
            $this->getMockObject(TypeListInterface::class),
            $this->getMockObject(UploaderFactory::class),
            $this->getMockObject(RequestDataInterface::class),
            $this->getMockObject(Filesystem::class),
            $this->getMockObject(UrlInterface::class),
            $this->getMockObject(AbstractResource::class),
            $this->getMockObject(AbstractDb::class),
            [],
            $this->getMockObject(Database::class),
            $this->ioFileSystem
        );
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        unset($this->logoBackend);
    }

    /**
     * @param string $class
     * @return MockObject
     */
    private function getMockObject(string $class): MockObject
    {
        return $this->getMockBuilder($class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @param string $extension
     * @return void
     */
    #[DataProvider('rejectedExtensionDataProvider')]
    public function testBeforeSaveRejectsExtensionUnsupportedByMailClients(string $extension): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Something is wrong with the file upload settings.');

        $this->prepareFile('logo.' . $extension, $extension);
        $this->logoBackend->beforeSave();
    }

    /**
     * @return array
     */
    public static function rejectedExtensionDataProvider(): array
    {
        return [
            'webp' => ['webp'],
            'avif' => ['avif'],
        ];
    }

    /**
     * @param string $extension
     * @return void
     */
    #[DataProvider('acceptedExtensionDataProvider')]
    public function testBeforeSaveAcceptsExtensionSupportedByMailClients(string $extension): void
    {
        $this->prepareFile('logo.' . $extension, $extension, true);

        $this->assertSame($this->logoBackend, $this->logoBackend->beforeSave());
    }

    /**
     * @return array
     */
    public static function acceptedExtensionDataProvider(): array
    {
        return [
            'jpg' => ['jpg'],
            'jpeg' => ['jpeg'],
            'gif' => ['gif'],
            'png' => ['png'],
        ];
    }

    /**
     * @param string $fileName
     * @param string $extension
     * @param bool $exists
     * @return void
     */
    private function prepareFile(string $fileName, string $extension, bool $exists = false): void
    {
        $value = ['file' => $fileName];
        if ($exists) {
            $value['exists'] = true;
        }
        $this->logoBackend->setData(['value' => [$value]]);
        $this->ioFileSystem->expects($this->any())
            ->method('getPathInfo')
            ->with($fileName)
            ->willReturn(['extension' => $extension]);
    }
}
