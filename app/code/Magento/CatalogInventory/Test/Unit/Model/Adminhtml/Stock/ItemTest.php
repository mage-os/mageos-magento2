<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Adminhtml\Stock;

use Magento\CatalogInventory\Model\Adminhtml\Stock\Item;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class ItemTest extends TestCase
{
    /**
     * @var Item|MockObject
     */
    protected $_model;

    /**
     * setUp
     */
    protected function setUp(): void
    {
        // Create anonymous class for AbstractResource with getIdFieldName method
        $resourceMock = new class extends AbstractResource {
            /** @var string|null */
            private $idFieldName = null;
            /** @var mixed */
            private $connection = null;

            public function getIdFieldName()
            {
                return $this->idFieldName;
            }

            public function setIdFieldName($idFieldName)
            {
                $this->idFieldName = $idFieldName;
                return $this;
            }

            public function getConnection()
            {
                return $this->connection;
            }

            public function setConnection($connection)
            {
                $this->connection = $connection;
                return $this;
            }

            protected function _construct()
            {
                // Required abstract method implementation
            }
        };

        $groupManagement = $this->createMock(GroupManagementInterface::class);

        $allGroup = $this->createMock(GroupInterface::class);

        $allGroup->method('getId')->willReturn(32000);

        $groupManagement->method('getAllCustomersGroup')->willReturn($allGroup);

        // Create all required mocks for the Item constructor
        $contextMock = $this->createMock(\Magento\Framework\Model\Context::class);
        $registryMock = $this->createMock(\Magento\Framework\Registry::class);
        $extensionFactoryMock = $this->createMock(\Magento\Framework\Api\ExtensionAttributesFactory::class);
        $customAttributeFactoryMock = $this->createMock(\Magento\Framework\Api\AttributeValueFactory::class);
        $customerSessionMock = $this->createMock(\Magento\Customer\Model\Session::class);
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $stockConfigurationMock = $this->createMock(\Magento\CatalogInventory\Api\StockConfigurationInterface::class);
        $stockRegistryMock = $this->createMock(\Magento\CatalogInventory\Api\StockRegistryInterface::class);
        $stockItemRepositoryMock = $this->createMock(\Magento\CatalogInventory\Api\StockItemRepositoryInterface::class);

        // Direct instantiation instead of ObjectManagerHelper
        $this->_model = new Item(
            $contextMock,
            $registryMock,
            $extensionFactoryMock,
            $customAttributeFactoryMock,
            $customerSessionMock,
            $storeManagerMock,
            $stockConfigurationMock,
            $stockRegistryMock,
            $stockItemRepositoryMock,
            $groupManagement,
            $resourceMock
        );
    }

    public function testGetCustomerGroupId()
    {
        $this->_model->setCustomerGroupId(null);
        $this->assertEquals(32000, $this->_model->getCustomerGroupId());
        $this->_model->setCustomerGroupId(2);
        $this->assertEquals(2, $this->_model->getCustomerGroupId());
    }

    public function testGetIdentities()
    {
        $this->_model->setProductId(1);
        $this->assertEquals(['cat_p_1'], $this->_model->getIdentities());
    }
}
