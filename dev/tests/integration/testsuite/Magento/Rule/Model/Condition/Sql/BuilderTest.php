<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\Rule\Model\Condition\Sql;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Test\Fixture\AttributeOption as AttributeOptionFixture;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\Catalog\Test\Fixture\MultiselectAttribute;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogWidget\Model\RuleFactory;
use Magento\CatalogWidget\Model\Rule\Condition\Combine as CombineCondition;
use Magento\CatalogWidget\Model\Rule\Condition\Product as ProductCondition;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\Rule\Model\Condition\Sql\Builder
 * @magentoDbIsolation enabled
 */
class BuilderTest extends TestCase
{
    /**
     * @var Builder
     */
    private Builder $model;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->model = Bootstrap::getObjectManager()->create(Builder::class);
    }

    /**
     * @param array $conditions
     * @param string $expectedWhere
     * @param string $expectedOrder
     * @return void
     * @throws LocalizedException
     * @dataProvider attachConditionToCollectionDataProvider
     */
    #[
        DataFixture(MultiselectAttribute::class, ['attribute_code' => 'multi_select_attr'], 'multiselect'),
        DataFixture(
            AttributeOptionFixture::class,
            [
                'attribute_code' => 'multiselect.attribute_code$',
                'label' => 'red',
                'sort_order' => 20
            ],
            'multiselect_custom_attribute_option_1'
        ),
        DataFixture(
            AttributeOptionFixture::class,
            [
                'attribute_code' => 'multiselect.attribute_code$',
                'sort_order' => 10,
                'label' => 'white',
                'is_default' => true
            ],
            'multiselect_custom_attribute_option_2'
        )
    ]
    public function testAttachConditionToCollection(
        array $conditions,
        string $expectedWhere,
        string $expectedOrder
    ): void {
        /** @var ProductCollectionFactory $collectionFactory */
        $collectionFactory = Bootstrap::getObjectManager()->create(ProductCollectionFactory::class);
        $collection = $collectionFactory->create();
        foreach ($conditions as $key => $condition) {
            if (isset($condition['attribute']) && $condition['attribute'] === 'multiselect_attribute') {
                $multiselect = Bootstrap::getObjectManager()->create(
                    Attribute::class
                );
                $multiselect->load('multi_select_attr', 'attribute_code');
                $multiselectAttributeOptionIds = [];
                foreach ($multiselect->getOptions() as $option) {
                    if ($option->getValue()) {
                        $multiselectAttributeOptionIds[] = $option->getValue();
                    }
                }

                $condition[$key]['value'] = implode(',', $multiselectAttributeOptionIds);
            }
        }

        /** @var RuleFactory $ruleFactory */
        $ruleFactory = Bootstrap::getObjectManager()->create(RuleFactory::class);
        $rule = $ruleFactory->create();

        $ruleConditionArray = [
            'conditions' => $conditions,
        ];

        $rule->loadPost($ruleConditionArray);
        $this->model->attachConditionToCollection($collection, $rule->getConditions());

        $this->assertStringContainsString($expectedWhere, $collection->getSelectSql(true));
        $this->assertStringContainsString($expectedOrder, $collection->getSelectSql(true));
    }

    /**
     * @return array
     */
    public static function attachConditionToCollectionDataProvider(): array
    {
        return [
            [
                [
                    '1' => [
                        'type' => CombineCondition::class,
                        'aggregator' => 'all',
                        'value' => '1',
                        'new_child' => '',
                    ],
                    '1--1' => [
                        'type' => ProductCondition::class,
                        'attribute' => 'category_ids',
                        'operator' => '==',
                        'value' => '3',
                    ],
                    '1--2' => [
                        'type' => ProductCondition::class,
                        'attribute' => 'special_to_date',
                        'operator' => '==',
                        'value' => '2017-09-15',
                    ],
                    '1--3' => [
                        'type' => ProductCondition::class,
                        'attribute' => 'sku',
                        'operator' => '()',
                        'value' => 'sku1,sku2,sku3,sku4,sku5',
                    ]
                ],
                "WHERE (((`e`.`entity_id` IN (SELECT `catalog_category_product`.`product_id` FROM `catalog_category_product` WHERE (category_id IN ('3')))) AND(`e`.`entity_id` = '2017-09-15 00:00:00') AND(`e`.`sku` IN ('sku1', 'sku2', 'sku3', 'sku4', 'sku5')) ))",
                "ORDER BY (FIELD(`e`.`sku`, 'sku1', 'sku2', 'sku3', 'sku4', 'sku5'))"
            ],
            [
                [
                    '1' => [
                        'type' => CombineCondition::class,
                        'aggregator' => 'all',
                        'value' => '1',
                        'new_child' => '',
                    ],
                    '1--1' => [
                        'type' => ProductCondition::class,
                        'attribute' => 'category_ids',
                        'operator' => '==',
                        'value' => '3',
                    ],
                    '1--2' => [
                        'type' => ProductCondition::class,
                        'attribute' => 'sku',
                        'operator' => '()',
                        'value' => 'sku1,sku2,sku3',
                    ],
                    '1--3' => [
                        'type' => ProductCondition::class,
                        'attribute' => 'multiselect_attribute',
                        'operator' => '{}',
                    ]
                ],
                "WHERE ((((`e`.`entity_id` IN (SELECT `catalog_category_product`.`product_id` FROM " .
                "`catalog_category_product` WHERE (category_id IN ('3')))) AND(`e`.`sku` IN " .
                "('sku1', 'sku2', 'sku3')) AND(`multiselect_attribute` LIKE '%%') ))) AND " .
                "(e.created_in <= 1) AND (e.updated_in > 1) ",
                "ORDER BY (FIELD(`e`.`sku`, 'sku1', 'sku2', 'sku3'))"
            ]
        ];
    }
}
