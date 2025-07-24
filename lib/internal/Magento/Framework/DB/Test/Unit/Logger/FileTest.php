<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\DB\Test\Unit\Logger;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Logger\File;
use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\WriteInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Zend_Db_Statement_Interface;

class FileTest extends TestCase
{
    const DEBUG_FILE = 'debug.file.log';

    /**
     * @var WriteInterface|MockObject
     */
    private $stream;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|MockObject
     */
    private $dir;

    /**
     * @var File
     */
    private $object;

    /**
     * @var ResourceConnection|MockObject
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var Filesystem|MockObject
     */
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->stream = $this->getMockForAbstractClass(WriteInterface::class);
        $this->dir = $this->getMockForAbstractClass(\Magento\Framework\Filesystem\Directory\WriteInterface::class);
        $this->dir->expects($this->any())
            ->method('openFile')
            ->with(self::DEBUG_FILE, 'a')
            ->willReturn($this->stream);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->filesystem->expects($this->any())
            ->method('getDirectoryWrite')
            ->willReturn($this->dir);
        $this->resourceConnection = $this->createMock(ResourceConnection::class);

        $this->object = new File(
            $this->filesystem,
            $this->resourceConnection,
            self::DEBUG_FILE
        );
    }

    public function testLog()
    {
        $input = 'message';
        $expected = '%amessage';

        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->matches($expected));

        $this->object->log($input);
    }

    /**
     * @param $type
     * @param $sql
     * @param $bind
     * @param $result
     * @param $expectedResult
     * @return void
     * @throws FileSystemException
     * @throws \Zend_Db_Statement_Exception
     * @dataProvider statsDataProvider
     */
    public function testLogStatsWithExplain($type, $sql, $bind, $result, $explainResult, $expectedResult): void
    {
        $statement = $this->createMock(Zend_Db_Statement_Interface::class);
        $statement->expects($this->any())->method('fetchAll')->willReturn(json_decode($explainResult, true));
        $connection = $this->createMock(AdapterInterface::class);
        $connection->expects($this->any())
            ->method('query')
            ->with('EXPLAIN ' . $sql)
            ->willReturn($statement);
        $this->resourceConnection->expects($this->any())->method('getConnection')->willReturn($connection);
        $fileLogger = new File(
            $this->filesystem,
            $this->resourceConnection,
            self::DEBUG_FILE,
            true,
            0.05,
            false,
            true
        );
        $stats = $fileLogger->getStats($type, $sql, $bind, $result);
        $this->assertStringContainsString($expectedResult, $stats);
    }

    /**
     * @return array
     */
    public static function statsDataProvider(): array
    {
        return [
            'no-stats-for-update-query' => [
                LoggerInterface::TYPE_QUERY,
                "UPDATE `admin_user_session` SET `updated_at` = '2025-07-23 14:42:02' WHERE (id=5)",
                [],
                null,
                '{}',
                'INDEX CHECK: NA'
            ],
            'no-stats-for-insert-query' => [
                LoggerInterface::TYPE_QUERY,
                "INSERT INTO `magento_logging_event` (`ip`, `x_forwarded_ip`, `event_code`, `time`, `action`, `info`,
                            `status`, `user`, `user_id`, `fullaction`, `error_message`) VALUES
                            (?, ?, ?, '2025-07-23 14:42:02', ?, ?, ?, ?, ?, ?, ?)",
                [],
                null,
                '{}',
                'INDEX CHECK: NA'
            ],
            'no-stats-for-delete-query' => [
                LoggerInterface::TYPE_QUERY,
                "DELETE FROM `sales_order_grid` WHERE (entity_id IN
                                      (SELECT `magento_sales_order_grid_archive`.`entity_id`
                                       FROM `magento_sales_order_grid_archive`))",
                [],
                null,
                '{}',
                'INDEX CHECK: NA'
            ],
            'no-stats-for-explain-query' => [
                LoggerInterface::TYPE_QUERY,
                "EXPLAIN SELECT `main_table`.* FROM `admin_system_messages` AS `main_table`
                    ORDER BY severity ASC, created_at DESC",
                [],
                null,
                '{}',
                'INDEX CHECK: NA'
            ],
            'full-table-scan-no-index-filesort-query' => [
                LoggerInterface::TYPE_QUERY,
                "SELECT `main_table`.* FROM `admin_system_messages` AS `main_table`
                      ORDER BY severity ASC, created_at DESC",
                [],
                null,
                '[{"id":"1","select_type":"SIMPLE","table":"admin_system_messages","partitions":null,"type":"ALL",
                "possible_keys":null,"key":null,"key_len":null,"ref":null,"rows":"1","filtered":"100.00",
                "Extra":"Using filesort"}]',
                'INDEX CHECK: FULL TABLE SCAN, NO INDEX, FILESORT'
            ],
            'subselect-with-dependent-query' => [
                LoggerInterface::TYPE_QUERY,
                "SELECT `main_table`.*, (IF(
                (SELECT count(*)
                    FROM magento_operation
                    WHERE bulk_uuid = main_table.uuid
                ) = 0,
                0,
                (SELECT MAX(status) FROM magento_operation WHERE bulk_uuid = main_table.uuid)
            )) AS `status` FROM `magento_bulk` AS `main_table` WHERE (`user_id` = '1')
                                                               ORDER BY FIELD(status, 2,3,0,4,1), start_time DESC",
                [],
                null,
                '[{"id":"1","select_type":"PRIMARY","table":"main_table","partitions":null,"type":"ref",
                "possible_keys":"MAGENTO_BULK_USER_ID","key":"MAGENTO_BULK_USER_ID","key_len":"5","ref":"const",
                "rows":"1","filtered":"100.00","Extra":"Using filesort"},{"id":"3","select_type":"DEPENDENT SUBQUERY",
                "table":"magento_operation","partitions":null,"type":"ref","possible_keys":
                "MAGENTO_OPERATION_BULK_UUID_ERROR_CODE","key":"MAGENTO_OPERATION_BULK_UUID_ERROR_CODE",
                "key_len":"42","ref":"magento24i2.main_table.uuid","rows":"1","filtered":"100.00","Extra":null},
                {"id":"2","select_type":"DEPENDENT SUBQUERY","table":"magento_operation","partitions":null,
                "type":"ref","possible_keys":"MAGENTO_OPERATION_BULK_UUID_ERROR_CODE","key":
                "MAGENTO_OPERATION_BULK_UUID_ERROR_CODE","key_len":"42","ref":
                "magento24i2.main_table.uuid","rows":"1","filtered":"100.00","Extra":"Using index"}]',
                'INDEX CHECK: FILESORT, DEPENDENT SUBQUERY'
            ],
        ];
    }


    /**
     * @param $type
     *
     * @param string $q
     * @param array $bind
     * @param \Zend_Db_Statement_Pdo|null $result
     * @param string $expected
     * @dataProvider logStatsDataProvider
     */
    public function testLogStats($type, $q, array $bind, $result, $expected)
    {
        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->matches($expected));
        $this->object->logStats($type, $q, $bind, $result);
    }

    /**
     * @return array
     */
    public static function logStatsDataProvider()
    {
        return [
            [LoggerInterface::TYPE_CONNECT, '', [], null, '%aCONNECT%a'],
            [
                LoggerInterface::TYPE_TRANSACTION,
                'SELECT something',
                [],
                null,
                '%aTRANSACTION SELECT something%a'
            ],
            [
                LoggerInterface::TYPE_QUERY,
                'SELECT something',
                [],
                null,
                '%aSQL: SELECT something%a'
            ],
            [
                LoggerInterface::TYPE_QUERY,
                'SELECT something',
                ['data'],
                null,
                "%aQUERY%aSQL: SELECT something%aBIND: array (%a0 => 'data',%a)%a"
            ],
        ];
    }

    public function testLogStatsWithResult()
    {
        $result = $this->createMock(\Zend_Db_Statement_Pdo::class);
        $result->expects($this->once())
            ->method('rowCount')
            ->willReturn(10);
        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->logicalNot($this->matches('%aSQL: SELECT something%aAFF: 10')));

        $this->object->logStats(
            LoggerInterface::TYPE_QUERY,
            'SELECT something',
            [],
            $result
        );
    }

    public function testLogStatsUnknownType()
    {
        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->logicalNot($this->matches('%aSELECT something%a')));
        $this->object->logStats('unknown', 'SELECT something');
    }

    public function testcritical()
    {
        $exception = new \Exception('error message');
        $expected = "%aEXCEPTION%aException%aerror message%a";

        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->matches($expected));

        $this->object->critical($exception);
    }
}
