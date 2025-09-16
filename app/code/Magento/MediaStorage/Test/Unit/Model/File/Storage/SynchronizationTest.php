<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MediaStorage\Test\Unit\Model\File\Storage;

use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\File\Write;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaStorage\Model\File\Storage\Database;
use Magento\MediaStorage\Model\File\Storage\DatabaseFactory;
use Magento\MediaStorage\Model\File\Storage\Synchronization;
use PHPUnit\Framework\TestCase;

class SynchronizationTest extends TestCase
{
    public function testSynchronize()
    {
        $content = 'content';
        $relativeFileName = 'config.xml';

        $storageFactoryMock = $this->createPartialMock(DatabaseFactory::class, ['create']); // @phpstan-ignore-line
        $storageMock = new class extends Database {
            public function __construct()
            {
            }
            public function getContent()
            {
                return 'content';
            }
            public function getId()
            {
                return true;
            }
            public function loadByFilename($filename)
            {
                return $this;
            }
        };
        $storageFactoryMock->expects($this->once())->method('create')->willReturn($storageMock);

        $file = $this->createPartialMock(
            Write::class,
            ['lock', 'write', 'unlock', 'close']
        );
        $file->expects($this->once())->method('lock');
        $file->expects($this->once())->method('write')->with($content);
        $file->expects($this->once())->method('unlock');
        $file->expects($this->once())->method('close');
        $directory = $this->createMock(WriteInterface::class);
        $directory->expects($this->once())
            ->method('openFile')
            ->with($relativeFileName)
            ->willReturn($file);

        $objectManager = new ObjectManager($this);
        $model = $objectManager->getObject(Synchronization::class, [
            'storageFactory' => $storageFactoryMock,
            'directory' => $directory,
        ]);
        $model->synchronize($relativeFileName);
    }
}
