<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Plugin\Model\Attribute\Backend;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Plugin\Model\Attribute\Backend\AttributeValidation;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AttributeValidationTest extends TestCase
{
    /**
     * @var AttributeValidation
     */
    private $attributeValidation;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var StoreInterface|MockObject
     */
    private $storeMock;

    /**
     * @var array
     */
    private $allowedEntityTypes;

    /**
     * @var \Callable
     */
    private $proceedMock;

    /**
     * @var bool
     */
    private $isProceedMockCalled = false;

    /**
     * @var AbstractBackend|MockObject
     */
    private $subjectMock;

    /**
     * @var AbstractAttribute|MockObject
     */
    private $attributeMock;

    /**
     * @var DataObject|MockObject
     */
    private $entityMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->attributeMock = new class extends AbstractBackend {
            private $attributeCodeReturn = null;
            
            public function setAttributeCodeReturn($return)
            {
                $this->attributeCodeReturn = $return;
                return $this;
            }
            
            public function getAttributeCode()
            {
                return $this->attributeCodeReturn;
            }
            
            // Required BackendInterface methods
            public function getTable()
            {
                return '';
            }
            public function isStatic()
            {
                return false;
            }
            public function getType()
            {
                return '';
            }
            public function getEntityIdField()
            {
                return '';
            }
            public function setValueId($valueId)
            {
                return $this;
            }
            public function getValueId()
            {
                return 0;
            }
            public function afterLoad($object)
            {
                return $this;
            }
            public function beforeSave($object)
            {
                return $this;
            }
            public function afterSave($object)
            {
                return $this;
            }
            public function beforeDelete($object)
            {
                return $this;
            }
            public function afterDelete($object)
            {
                return $this;
            }
            public function getEntityValueId($entity)
            {
                return 0;
            }
            public function setEntityValueId($entity, $valueId)
            {
                return $this;
            }
            public function isScalar()
            {
                return false;
            }
        };
        $this->subjectMock = new class extends AbstractBackend {
            private $attributeReturn = null;
            
            public function setAttributeReturn($return)
            {
                $this->attributeReturn = $return;
                return $this;
            }
            
            public function getAttribute()
            {
                return $this->attributeReturn;
            }
            
            // Required BackendInterface methods
            public function getTable()
            {
                return '';
            }
            public function isStatic()
            {
                return false;
            }
            public function getType()
            {
                return '';
            }
            public function getEntityIdField()
            {
                return '';
            }
            public function setValueId($valueId)
            {
                return $this;
            }
            public function getValueId()
            {
                return 0;
            }
            public function afterLoad($object)
            {
                return $this;
            }
            public function beforeSave($object)
            {
                return $this;
            }
            public function afterSave($object)
            {
                return $this;
            }
            public function beforeDelete($object)
            {
                return $this;
            }
            public function afterDelete($object)
            {
                return $this;
            }
            public function getEntityValueId($entity)
            {
                return 0;
            }
            public function setEntityValueId($entity, $valueId)
            {
                return $this;
            }
            public function isScalar()
            {
                return false;
            }
        };
        $this->subjectMock->setAttributeReturn($this->attributeMock);

        $this->storeMock = new class implements StoreInterface {
            private $idReturn = null;
            
            public function setIdReturn($return)
            {
                $this->idReturn = $return;
                return $this;
            }
            
            public function getId()
            {
                return $this->idReturn;
            }
            
            // Required StoreInterface methods
            public function setId($id)
            {
                return $this;
            }
            public function getCode()
            {
                return '';
            }
            public function setCode($code)
            {
                return $this;
            }
            public function getName()
            {
                return '';
            }
            public function setName($name)
            {
                return $this;
            }
            public function getWebsiteId()
            {
                return 0;
            }
            public function setWebsiteId($websiteId)
            {
                return $this;
            }
            public function getStoreGroupId()
            {
                return 0;
            }
            public function setStoreGroupId($storeGroupId)
            {
                return $this;
            }
            public function setIsActive($isActive)
            {
                return $this;
            }
            public function getIsActive()
            {
                return 0;
            }
            public function getExtensionAttributes()
            {
                return null;
            }
            public function setExtensionAttributes($extensionAttributes)
            {
                return $this;
            }
        };
        $this->storeManagerMock = new class implements StoreManagerInterface {
            private $storeReturn = null;
            
            public function setStoreReturn($return)
            {
                $this->storeReturn = $return;
                return $this;
            }
            
            public function getStore($storeId = null)
            {
                return $this->storeReturn;
            }
            
            // Required StoreManagerInterface methods
            public function setIsSingleStoreModeAllowed($value)
            {
            }
            public function hasSingleStore()
            {
                return false;
            }
            public function isSingleStoreMode()
            {
                return false;
            }
            public function getStores($withDefault = false, $codeKey = false)
            {
                return [];
            }
            public function getWebsite($websiteId = null)
            {
                return null;
            }
            public function getWebsites($withDefault = false, $codeKey = false)
            {
                return [];
            }
            public function reinitStores()
            {
            }
            public function getDefaultStoreView()
            {
                return null;
            }
            public function getGroup($groupId = null)
            {
                return null;
            }
            public function getGroups($withDefault = false)
            {
                return [];
            }
            public function setCurrentStore($store)
            {
            }
        };
        $this->storeManagerMock->setStoreReturn($this->storeMock);

        $this->entityMock = new class extends DataObject {
            private $getDataCallback = null;
            
            public function setGetDataCallback($callback)
            {
                $this->getDataCallback = $callback;
                return $this;
            }
            
            public function getData($key = '', $index = null)
            {
                if ($this->getDataCallback !== null) {
                    return call_user_func($this->getDataCallback, $key);
                }
                return null;
            }
        };

        $this->allowedEntityTypes = [$this->entityMock];

        $this->proceedMock = function () {
            $this->isProceedMockCalled = true;
        };

        $this->attributeValidation = $objectManager->getObject(
            AttributeValidation::class,
            [
                'storeManager' => $this->storeManagerMock,
                'allowedEntityTypes' => $this->allowedEntityTypes
            ]
        );
    }

    /**
     * @param bool $shouldProceedRun
     * @param bool $defaultStoreUsed
     * @param null|int|string $storeId
     *
     * @return void
     * @throws NoSuchEntityException
     */
    #[DataProvider('aroundValidateDataProvider')]
    public function testAroundValidate(bool $shouldProceedRun, bool $defaultStoreUsed, $storeId): void
    {
        $this->isProceedMockCalled = false;
        $attributeCode = 'code';

        $this->storeMock->setIdReturn($storeId);

        if ($defaultStoreUsed) {
            $this->attributeMock->setAttributeCodeReturn($attributeCode);
            $this->entityMock->setGetDataCallback(function ($arg1) use ($attributeCode) {
                if (empty($arg1)) {
                    return [$attributeCode => null];
                } elseif ($arg1 == $attributeCode) {
                    return null;
                }
            });
        }

        $this->attributeValidation->aroundValidate($this->subjectMock, $this->proceedMock, $this->entityMock);
        $this->assertSame($shouldProceedRun, $this->isProceedMockCalled);
    }

    /**
     * Data provider for testAroundValidate.
     *
     * @return array
     */
    public static function aroundValidateDataProvider(): array
    {
        return [
            [true, false, '0'],
            [true, false, 0],
            [true, false, null],
            [false, true, 1]
        ];
    }
}
