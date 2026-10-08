<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MediaStorage\Test\Unit\Model\File;

use Magento\Framework\File\Mime;
use Magento\Framework\File\Uploader as FrameworkUploader;
use Magento\MediaStorage\Model\File\Uploader;
use PHPUnit\Framework\TestCase;

class UploaderTest extends TestCase
{
    public function testValidAvifWithGenericMimeIsAccepted(): void
    {
        $uploader = $this->createUploader($this->fixture('avif.avif'), 'image.avif', 'application/octet-stream');

        $this->assertTrue($uploader->checkMimeType(['image/avif']));
    }

    public function testGenericMimeDoesNotAllowAnotherImageRenamedAsAvif(): void
    {
        $uploader = $this->createUploader($this->fixture('webp.webp'), 'image.avif', 'application/octet-stream');

        $this->assertFalse($uploader->checkMimeType(['image/avif']));
    }

    public function testSpecificNonAvifMimeIsNotOverridden(): void
    {
        $uploader = $this->createUploader($this->fixture('avif.avif'), 'image.avif', 'text/html');

        $this->assertFalse($uploader->checkMimeType(['image/avif']));
    }

    private function createUploader(string $filePath, string $fileName, string $mimeType): Uploader
    {
        $mime = $this->createMock(Mime::class);
        $mime->method('getMimeType')->with($filePath)->willReturn($mimeType);

        $uploader = (new \ReflectionClass(Uploader::class))->newInstanceWithoutConstructor();
        (new \ReflectionProperty(FrameworkUploader::class, '_file'))->setValue(
            $uploader,
            ['tmp_name' => $filePath, 'name' => $fileName]
        );
        (new \ReflectionProperty(FrameworkUploader::class, '_fileExists'))->setValue($uploader, true);
        (new \ReflectionProperty(FrameworkUploader::class, 'fileMime'))->setValue($uploader, $mime);

        return $uploader;
    }

    private function fixture(string $name): string
    {
        return dirname(__DIR__, 8) . '/dev/tests/acceptance/tests/_data/' . $name;
    }
}
