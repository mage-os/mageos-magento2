<?php
/**
 * Copyright 2022 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Model\Product;

use Magento\Bundle\Api\ProductLinkManagementInterface;
use Magento\Bundle\Api\ProductOptionRepositoryInterface as OptionRepository;
use Magento\Bundle\Api\Data\OptionInterface;
use Magento\Bundle\Model\Option\SaveAction;
use Magento\Bundle\Model\Product\Type;
use Magento\Bundle\Model\Product\SaveHandler;
use Magento\Bundle\Model\Product\CheckOptionLinkIfExist;
use Magento\Bundle\Model\ProductRelationsProcessorComposite;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Catalog\Test\Unit\Helper\ProductTestHelper;
use Magento\Catalog\Test\Unit\Helper\ProductExtensionInterfaceTestHelper;

class SaveHandlerTest extends TestCase
{
    /**
     * @var ProductLinkManagementInterface|MockObject
     */
    private $productLinkManagement;

    /**
     * @var OptionRepository|MockObject
     */
    private $optionRepository;

    /**
     * @var SaveAction|MockObject
     */
    private $optionSave;

    /**
     * @var MetadataPool|MockObject
     */
    private $metadataPool;

    /**
     * @var CheckOptionLinkIfExist|MockObject
     */
    private $checkOptionLinkIfExist;

    /**
     * @var ProductRelationsProcessorComposite|MockObject
     */
    private $productRelationsProcessorComposite;

    /**
     * @var ProductTestHelper
     */
    private $entity;

    /**
     * @var SaveHandler
     */
    private $saveHandler;

    protected function setUp(): void
    {
        $this->productLinkManagement = $this->getMockBuilder(ProductLinkManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->optionRepository = $this->getMockBuilder(OptionRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->optionSave = $this->getMockBuilder(SaveAction::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadataPool = $this->getMockBuilder(MetadataPool::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->checkOptionLinkIfExist = $this->getMockBuilder(CheckOptionLinkIfExist::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productRelationsProcessorComposite = $this->getMockBuilder(ProductRelationsProcessorComposite::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->entity = new ProductTestHelper();
        $this->entity->setTypeId(Type::TYPE_CODE);

        $this->saveHandler = new SaveHandler(
            $this->optionRepository,
            $this->productLinkManagement,
            $this->optionSave,
            $this->metadataPool,
            $this->checkOptionLinkIfExist,
            $this->productRelationsProcessorComposite
        );
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecuteWithBulkOptionsProcessing(): void
    {
        $option = $this->createMock(OptionInterface::class);
        $option->method('getOptionId')->willReturn(1);
        $bundleOptions = [$option];

        $extensionAttributes = new ProductExtensionInterfaceTestHelper();
        $extensionAttributes->setBundleProductOptions($bundleOptions);
        $this->entity->setExtensionAttributes($extensionAttributes);
        $metadata = $this->createMock(EntityMetadataInterface::class);
        $this->metadataPool->expects($this->once())
            ->method('getMetadata')
            ->willReturn($metadata);
        $this->optionRepository->method('getList')->willReturn($bundleOptions);

        $this->optionSave->expects($this->once())
            ->method('saveBulk');
        $this->saveHandler->execute($this->entity);
    }
}
