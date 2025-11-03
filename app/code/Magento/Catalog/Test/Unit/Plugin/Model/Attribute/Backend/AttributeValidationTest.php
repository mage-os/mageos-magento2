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
use Magento\Eav\Test\Unit\Helper\AbstractBackendTestHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Test\Unit\Helper\DataObjectTestHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Test\Unit\Helper\StoreManagerTestHelper;
use Magento\Store\Test\Unit\Helper\StoreTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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

        $this->attributeMock = new AbstractBackendTestHelper();
        $this->subjectMock = new AbstractBackendTestHelper();
        $this->subjectMock->setAttributeReturn($this->attributeMock);

        $this->storeMock = new StoreTestHelper();
        $this->storeManagerMock = new StoreManagerTestHelper();
        $this->storeManagerMock->setStoreReturn($this->storeMock);

        $this->entityMock = new DataObjectTestHelper();

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
