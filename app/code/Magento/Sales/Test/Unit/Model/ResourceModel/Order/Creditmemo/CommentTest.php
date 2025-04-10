<?php
/************************************************************************
 * Copyright 2025 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 * ***********************************************************************
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model\ResourceModel\Order\Creditmemo;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order\Creditmemo\Comment\Validator;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Comment;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Sales\Model\Order\Creditmemo as OrderCreditmemo;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CommentTest extends TestCase
{
    /**
     * @var Comment
     */
    protected $commentResource;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\Comment|MockObject
     */
    protected $commentModelMock;

    /**
     * @var ResourceConnection|MockObject
     */
    protected $appResourceMock;

    /**
     * @var AdapterInterface|MockObject
     */
    protected $connectionMock;

    /**
     * @var Validator|MockObject
     */
    protected $validatorMock;

    /**
     * @var Snapshot|MockObject
     */
    protected $entitySnapshotMock;

    /**
     * @var Select|MockObject
     */
    protected $selectMock;

    /**
     * @var OrderCreditmemo|MockObject
     */
    protected $orderCreditmemo;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->commentModelMock = $this->createMock(\Magento\Sales\Model\Order\Creditmemo\Comment::class);
        $this->appResourceMock = $this->createMock(ResourceConnection::class);
        $this->connectionMock = $this->createMock(Mysql::class);
        $this->validatorMock = $this->createMock(Validator::class);
        $this->selectMock = $this->createMock(Select::class);
        $this->orderCreditmemo = $this->createMock(OrderCreditmemo::class);
        $this->entitySnapshotMock = $this->createMock(
            Snapshot::class
        );

        $this->appResourceMock->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connectionMock);
        $this->connectionMock->expects($this->any())
            ->method('describeTable')
            ->willReturn([]);
        $this->connectionMock->expects($this->any())
            ->method('insert');
        $this->connectionMock->expects($this->any())
            ->method('lastInsertId');
        $this->commentModelMock->expects($this->any())->method('hasDataChanges')->willReturn(true);
        $this->commentModelMock->expects($this->any())->method('isSaveAllowed')->willReturn(true);

        $relationProcessorMock = $this->createMock(
            ObjectRelationProcessor::class
        );

        $contextMock = $this->createMock(Context::class);
        $contextMock->expects($this->once())->method('getResources')->willReturn($this->appResourceMock);
        $contextMock->expects($this->once())->method('getObjectRelationProcessor')->willReturn($relationProcessorMock);

        $objectManager = new ObjectManager($this);

        $this->commentResource = $objectManager->getObject(
            Comment::class,
            [
                'context' => $contextMock,
                'validator' => $this->validatorMock,
                'entitySnapshot' => $this->entitySnapshotMock
            ]
        );
    }

    /**
     * Test _beforeSaveMethod via save()
     */
    public function testSave()
    {
        $commentId = 1;
        $output = [
            'user_id' => 1,
            'user_type' => 'test',
        ];

        $this->commentModelMock->expects($this->any())->method('getId')->willReturn($commentId);
        $this->commentModelMock->expects($this->any())->method('getCreditmemo')->willReturn($this->orderCreditmemo);
        $this->orderCreditmemo->expects($this->any())->method('getId')->willReturn($commentId);

        $this->connectionMock->expects($this->any())
            ->method('select')
            ->willReturn($this->selectMock);

        $this->selectMock->expects($this->any())
            ->method('from')
            ->willReturnSelf();

        $this->selectMock->expects($this->any())
            ->method('where')
            ->willReturnSelf();

        $this->connectionMock->expects($this->any())
            ->method('fetchRow')
            ->with($this->selectMock)
            ->willReturn($output);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($this->commentModelMock)
            ->willReturn([]);
        $this->entitySnapshotMock->expects($this->once())
            ->method('isModified')
            ->with($this->commentModelMock)
            ->willReturn(true);

        $this->commentModelMock->expects($this->any())->method('getData')->willReturn([]);
        $this->commentResource->save($this->commentModelMock);
        $this->assertTrue(true);
    }

    /**
     * Test _beforeSaveMethod via save() with failed validation
     */
    public function testSaveValidationFailed()
    {
        $this->expectException('Magento\Framework\Exception\LocalizedException');
        $this->expectExceptionMessage('Cannot save comment:');
        $this->entitySnapshotMock->expects($this->once())
            ->method('isModified')
            ->with($this->commentModelMock)
            ->willReturn(true);
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($this->commentModelMock)
            ->willReturn(['warning message']);
        $this->commentResource->save($this->commentModelMock);
        $this->assertTrue(true);
    }
}
