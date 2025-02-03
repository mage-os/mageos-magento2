<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\Rule\Model\Condition\Sql;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Test\Fixture\AttributeOption as AttributeOptionFixture;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\Catalog\Test\Fixture\MultiselectAttribute;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogWidget\Model\RuleFactory;
use Magento\CatalogWidget\Model\Rule\Condition\Combine as CombineCondition;
use Magento\CatalogWidget\Model\Rule\Condition\Product as ProductCondition;

/**
 * Test for Magento\Rule\Model\Condition\Sql\Builder
 */
class BuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Builder
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = Bootstrap::getObjectManager()->create(Builder::class);
    }

    /**
     * @param array $conditions
     * @param string $expectedSql
     * @dataProvider attachConditionToCollectionDataProvider
     * @return void
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
    public function testAttachConditionToCollection(array $conditions, string $expectedSql): void
    {
        /** @var ProductCollectionFactory $collectionFactory */
        $collectionFactory = Bootstrap::getObjectManager()->create(ProductCollectionFactory::class);
        $collection = $collectionFactory->create();
        /*$multiselect = DataFixtureStorageManager::getStorage()->get(
            'multi_select_attr'
        );*/
        $attribute = Bootstrap::getObjectManager()->create(
            Attribute::class
        );
        $attribute->load('multi_select_attr', 'attribute_code');
        $multiselectAttributeOptionIds = [];
        foreach ($attribute->getOptions() as $option) {
            if ($option->getValue()) {
                $multiselectAttributeOptionIds[] = $option->getValue();
            }
        }
        $conditions['1--4'] = [
            'type' => ProductCondition::class,
            'attribute' => 'multiselect_attribute',
            'operator' => '{}',
            'value' => implode(',', $multiselectAttributeOptionIds),
        ];
        /** @var RuleFactory $ruleFactory */
        $ruleFactory = Bootstrap::getObjectManager()->create(RuleFactory::class);
        $rule = $ruleFactory->create();

        $ruleConditionArray = [
            'conditions' => $conditions,
        ];

        $rule->loadPost($ruleConditionArray);
        $this->model->attachConditionToCollection($collection, $rule->getConditions());

        $whereString = "/\(category_id IN \('3'\).+\(`e`\.`entity_id` = '2017-09-15 00:00:00'\)"
            .".+\(`e`\.`sku` IN \('sku1', 'sku2', 'sku3', 'sku4', 'sku5'\)"
            . ".+ORDER BY \(FIELD\(`e`.`sku`, 'sku1', 'sku2', 'sku3', 'sku4', 'sku5'\)\)/";
        $this->assertStringContainsString($expectedSql, $collection->getSelectSql(true));
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
                "WHERE ((((`e`.`entity_id` IN (SELECT `catalog_category_product`.`product_id` FROM `catalog_category_product` WHERE (category_id IN ('3')))) AND(`e`.`entity_id` = '2017-09-15 00:00:00') AND(`e`.`sku` IN ('sku1', 'sku2', 'sku3', 'sku4', 'sku5')) ))) AND (e.created_in <= 1) AND (e.updated_in > 1) ORDER BY (FIELD(`e`.`sku`, 'sku1', 'sku2', 'sku3', 'sku4', 'sku5'))"
            ]
        ];
    }
}
