<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MediaStorage\Test\Unit\Model\File\Storage\Directory;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\MediaStorage\Helper\File\Storage\Database as DatabaseHelper;
use Magento\MediaStorage\Model\File\Storage;
use Magento\MediaStorage\Model\File\Storage\Directory\DatabaseFactory;
use Magento\MediaStorage\Model\ResourceModel\File\Storage\Directory\Database;
use Magento\MediaStorage\Test\Unit\Helper\DirectoryDatabaseTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DatabaseTest extends TestCase
{
    /**
     * @var \Magento\MediaStorage\Model\File\Storage\Directory\Database|MockObject
     */
    protected $directoryDatabase;

    /**
     * @var Context|MockObject
     */
    protected $contextMock;

    /**
     * @var Registry|MockObject
     */
    protected $registryMock;

    /**
     * @var DatabaseHelper|MockObject
     */
    protected $helperStorageDatabase;

    /**
     * @var DateTime|MockObject
     */
    protected $dateModelMock;

    /**
     * @var \Magento\MediaStorage\Model\File\Storage\Directory\Database|MockObject
     */
    protected $directoryMock;

    /**
     * @var DatabaseFactory|MockObject
     */
    protected $directoryFactoryMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $configMock;

    /**
     * @var Database|MockObject
     */
    protected $resourceDirectoryDatabaseMock;

    /**
     * @var LoggerInterface
     */
    protected $loggerMock;

    /**
     * @var string
     */
    protected $customConnectionName = 'custom-connection-name';

    /**
     * Setup preconditions
     */
    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->helperStorageDatabase = $this->createMock(DatabaseHelper::class);
        $this->dateModelMock = $this->createMock(DateTime::class);
        $this->directoryMock = new DirectoryDatabaseTestHelper();
        $this->directoryFactoryMock = $this->createPartialMock(
            DatabaseFactory::class, // @phpstan-ignore-line
            ['create']
        );
        $this->resourceDirectoryDatabaseMock = $this->createMock(
            \Magento\MediaStorage\Model\ResourceModel\File\Storage\Directory\Database::class
        );
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->directoryFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->willReturn(
            $this->directoryMock
        );

        $this->configMock = $this->createMock(ScopeConfigInterface::class);
        $this->configMock->expects(
            $this->any()
        )->method(
            'getValue'
        )->with(
            Storage::XML_PATH_STORAGE_MEDIA_DATABASE,
            'default'
        )->willReturn(
            $this->customConnectionName
        );

        $this->contextMock->expects($this->once())->method('getLogger')->willReturn($this->loggerMock);

        $this->directoryDatabase = new \Magento\MediaStorage\Model\File\Storage\Directory\Database(
            $this->contextMock,
            $this->registryMock,
            $this->helperStorageDatabase,
            $this->dateModelMock,
            $this->configMock,
            $this->directoryFactoryMock,
            $this->resourceDirectoryDatabaseMock,
            null,
            $this->customConnectionName,
            []
        );
    }

    /**
     * test import directories
     */
    public function testImportDirectories()
    {

        $this->directoryDatabase->importDirectories(
            [
                ['name' => 'first', 'path' => './path/number/one'],
                ['name' => 'second', 'path' => './path/number/two'],
            ]
        );
    }

    /**
     * test import directories without parent
     */
    public function testImportDirectoriesFailureWithoutParent()
    {

        $this->loggerMock->expects($this->any())->method('critical');

        $this->directoryDatabase->importDirectories([]);
    }

    /**
     * test import directories not an array
     */
    public function testImportDirectoriesFailureNotArray()
    {

        $this->directoryDatabase->importDirectories('not an array');
    }

    public function testSetGetConnectionName()
    {
        $this->assertSame($this->customConnectionName, $this->directoryDatabase->getConnectionName());
        $this->directoryDatabase->setConnectionName('test');
        $this->assertSame('test', $this->directoryDatabase->getConnectionName());
        $this->directoryDatabase->unsetData();
        $this->assertSame('test', $this->directoryDatabase->getConnectionName());
    }
}
