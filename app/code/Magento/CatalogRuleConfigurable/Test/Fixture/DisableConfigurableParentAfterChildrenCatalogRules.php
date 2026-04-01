<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRuleConfigurable\Test\Fixture;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\TestFramework\Fixture\RevertibleDataFixtureInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/**
 * Loads per-child catalog rule products and rules, then disables the configurable parent.
 */
class DisableConfigurableParentAfterChildrenCatalogRules implements RevertibleDataFixtureInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    public function apply(array $data = []): ?DataObject
    {
        Resolver::getInstance()->requireDataFixture(
            'Magento/CatalogRuleConfigurable/_files/configurable_product_with_percent_rules_for_children.php'
        );
        Bootstrap::getInstance()->loadArea(Area::AREA_ADMINHTML);
        $configurable = $this->productRepository->get('configurable');
        $configurable->setStatus(Status::STATUS_DISABLED);
        $this->productRepository->save($configurable);

        return new DataObject(['sku' => $configurable->getSku()]);
    }

    /**
     * @inheritdoc
     */
    public function revert(DataObject $data): void
    {
        Resolver::getInstance()->requireDataFixture(
            'Magento/CatalogRuleConfigurable/_files/configurable_product_with_percent_rules_for_children_rollback.php'
        );
    }
}
