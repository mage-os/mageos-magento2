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
namespace Magento\Sales\Model\ResourceModel\Order\Shipment;

use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Sales\Model\Order\Shipment\Comment\Validator;
use Magento\Sales\Model\ResourceModel\Attribute;
use Magento\Sales\Model\ResourceModel\EntityAbstract;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Sales\Model\Spi\ShipmentCommentResourceInterface;
use Magento\SalesSequence\Model\Manager;
use Magento\Authorization\Model\UserContextInterface;

/**
 * Flat sales order shipment comment resource
 *
 */
class Comment extends EntityAbstract implements ShipmentCommentResourceInterface
{
    /**
     * Model Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_shipment_comment_resource';

    /**
     * Class Validator
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Class User Context
     *
     * @var UserContextInterface
     */
    private UserContextInterface $userContext;

    /**
     * @param Context $context
     * @param Snapshot $entitySnapshot
     * @param RelationComposite $entityRelationComposite
     * @param Attribute $attribute
     * @param Manager $sequenceManager
     * @param Validator $validator
     * @param string $connectionName
     * @param UserContextInterface|null $userContext
     */
    public function __construct(
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        Attribute $attribute,
        Manager $sequenceManager,
        Validator $validator,
        $connectionName = null,
        ?UserContextInterface $userContext = null
    ) {
        $this->validator = $validator;
        $this->userContext = $userContext ?? ObjectManager::getInstance()->get(UserContextInterface::class);
        parent::__construct(
            $context,
            $entitySnapshot,
            $entityRelationComposite,
            $attribute,
            $sequenceManager,
            $connectionName
        );
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_shipment_comment', 'entity_id');
    }

    /**
     * Performs validation before save
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        /** @var \Magento\Sales\Model\Order\Shipment\Comment $object */
        if (!$object->getParentId() && $object->getShipment()) {
            $object->setParentId($object->getShipment()->getId());
        }

        if ($object->getId()) {
            $this->getCommentById($object);
        }

        parent::_beforeSave($object);
        $errors = $this->validator->validate($object);
        if (!empty($errors)) {
            throw new LocalizedException(
                __("Cannot save comment:\n%1", implode("\n", $errors))
            );
        }

        $this->setUserDetailsToComment($object);

        return $this;
    }

    /**
     * Fetch comment by id
     *
     * @param AbstractModel $commentObject
     * @return void
     * @throws LocalizedException
     */
    private function getCommentById(AbstractModel $commentObject): void
    {
        $table = $this->getMainTable();
        $query = $this->getConnection()->select()
            ->from($table, ['user_id','user_type'])
            ->where('entity_id = ?', $commentObject->getId())
            ->where('parent_id = ?', $commentObject->getParentId());
        $result = $this->getConnection()->fetchRow($query);

        if (!empty($result)) {
            $commentObject->setData('user_id', $result['user_id']);
            $commentObject->setData('user_type', $result['user_type']);
        }
    }

    /**
     * Set user details to sales entity comment
     *
     * @param AbstractModel $salesEntityComment
     * @return void
     */
    public function setUserDetailsToComment(AbstractModel $salesEntityComment): void
    {
        $salesEntityComment->setData('user_id', $this->userContext->getUserId());
        $salesEntityComment->setData('user_type', $this->userContext->getUserType());
    }
}
