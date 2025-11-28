<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\WeeeGraphQl\Test\Unit;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\GraphQl\Model\Query\Context;
use Magento\GraphQl\Model\Query\ContextExtensionInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Weee\Helper\Data as WeeeHelper;
use Magento\WeeeGraphQl\Model\Resolver\FixedProductTax;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FixedProductTaxTest extends TestCase
{
    use MockCreationTrait;
    public const STUB_STORE_ID = 1;

    /**
     * @var MockObject|Context
     */
    private $contextMock;

    /**
     * @var MockObject|ContextExtensionInterface
     */
    private $extensionAttributesMock;

    /**
     * @var FixedProductTax
     */
    private $resolver;

    /**
     * @var MockObject|WeeeHelper
     */
    private $weeeHelperMock;

    /**
     * @var MockObject|TaxHelper
     */
    private $taxHelperMock;

    /**
     * @var MockObject|DataObject
     */
    private $productMock;

    /**
     * Build the Testing Environment
     */
    protected function setUp(): void
    {
        $this->extensionAttributesMock = $this->createPartialMockWithReflection(
            ContextExtensionInterface::class,
            ['setStore', 'getStore', 'getIsCustomer', 'setIsCustomer', 'getCustomerGroupId', 'setCustomerGroupId']
        );

        $this->contextMock = $this->createPartialMockWithReflection(Context::class, ['getExtensionAttributes']);
        $this->contextMock->expects($this->any())
            ->method('getExtensionAttributes')
            ->willReturn($this->extensionAttributesMock);

        $this->productMock = $this->createMock(DataObject::class);

        $this->weeeHelperMock = $this->createPartialMock(
            WeeeHelper::class,
            ['isEnabled', 'getProductWeeeAttributesForDisplay']
        );
        $this->taxHelperMock = $this->createPartialMock(TaxHelper::class, ['getPriceDisplayType']);

        $this->resolver = new FixedProductTax($this->weeeHelperMock, $this->taxHelperMock);
    }

    /**
     * Verifies if the Exception is being thrown when no Product Model passed to resolver
     */
    public function testExceptionWhenNoModelSpecified(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessageMatches('/value should be specified/');

        $this->resolver->resolve(
            $this->getFieldStub(),
            $this->contextMock,
            $this->getResolveInfoStub()
        );
    }

    /**
     * Verifies that Attributes for display are not being fetched if feature not enabled in store
     */
    public function testNotGettingAttributesWhenWeeeDisabledForStore(): void
    {
        // Given
        $this->extensionAttributesMock->expects($this->any())
            ->method('getStore')
            ->willReturn(self::STUB_STORE_ID);

        // When
        $this->weeeHelperMock->expects($this->any())
            ->method('isEnabled')
            ->with(self::STUB_STORE_ID)
            ->willReturn(false);

        // Then
        $this->weeeHelperMock->expects($this->never())
            ->method('getProductWeeeAttributesForDisplay');

        $this->resolver->resolve(
            $this->getFieldStub(),
            $this->contextMock,
            $this->getResolveInfoStub(),
            ['model' => $this->productMock]
        );
    }

    /**
     * Returns stub for Field
     *
     * @return Field
     */
    private function getFieldStub(): Field
    {
        /** @var MockObject|Field $fieldMock */
        $fieldMock = $this->getMockBuilder(Field::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $fieldMock;
    }

    /**
     * Returns stub for ResolveInfo
     *
     * @return ResolveInfo
     */
    private function getResolveInfoStub(): ResolveInfo
    {
        /** @var MockObject|ResolveInfo $resolveInfoMock */
        $resolveInfoMock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $resolveInfoMock;
    }
}
