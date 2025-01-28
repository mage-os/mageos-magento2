<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\Rule\Model\Condition\Sql;

use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Test\Fixture\MultiselectAttribute;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Test\Fixture\AttributeOption as AttributeOptionFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Helper\Bootstrap;
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
     * @return void
     * @dataProvider attachConditionToCollectionDataProvider
     */
    #[
        DataFixture(CategoryFixture::class, ['url_path' => 'cat1'], 'cat1'),
        DataFixture(
            MultiselectAttribute::class,
            [
                'entity_type_id' => CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID,
                'source_model' => Table::class,
                'backend_model' => ArrayBackend::class,
                'attribute_code' => 'product_custom_attribute_multiselect'
            ],
            'multiselect_custom_attribute'
        ),
        DataFixture(
            AttributeOptionFixture::class,
            [
                'entity_type' => CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID,
                'attribute_code' => '$multiselect_custom_attribute.attribute_code$',
                'label' => 'red',
                'sort_order' => 20
            ],
            'multiselect_custom_attribute_option_1'
        ),
        DataFixture(
            AttributeOptionFixture::class,
            [
                'entity_type' => CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID,
                'attribute_code' => '$multiselect_custom_attribute.attribute_code$',
                'sort_order' => 10,
                'label' => 'white',
                'is_default' => true
            ],
            'multiselect_custom_attribute_option_2'
        ),
        DataFixture(ProductFixture::class,['sku' => 'sku1', 'category_ids' => ['$cat1.id$']], 'p1'),
        DataFixture(ProductFixture::class,['sku' => 'sku2', 'category_ids' => ['$cat1.id$']], 'p2'),
        DataFixture(ProductFixture::class,['sku' => 'sku3', 'category_ids' => ['$cat1.id$']], 'p3'),
        DataFixture(ProductFixture::class,['sku' => 'sku4', 'category_ids' => ['$cat1.id$']], 'p4'),
        DataFixture(ProductFixture::class, ['product_links' => [['sku' => '$p1.sku$', 'type' => 'related']]], 'p2'),
        DataFixture(ProductFixture::class, as: 'p3'),
        DataFixture(ProductFixture::class, as: 'p4'),
        DataFixture(ProductFixture::class, as: 'p5'),
        DataFixture(ProductFixture::class, ['product_links' => [['sku' => '$p4.sku$', 'type' => 'upsell']]], 'p6'),
        DataFixture(ProductFixture::class, as: 'p7'),
        DataFixture(ProductFixture::class, as: 'p8'),
        DataFixture(ProductFixture::class, ['product_links' => [['sku' => '$p8.sku$', 'type' => 'crosssell']]], 'p9'),
        DataFixture(RuleConditionFixture::class, ['attribute' => 'sku', 'value' => '$p2.sku$',], 'rule1Condition'),
        DataFixture(RuleConditionsFixture::class, ['conditions' => ['$rule1Condition$']], 'rule1Conditions'),
        DataFixture(RuleActionFixture::class, ['attribute' => 'sku', 'value' => '$p3.sku$',], 'rule1Action'),
        DataFixture(RuleActionsFixture::class, ['conditions' => ['$rule1Action$']], 'rule1Actions'),
        DataFixture(
            RuleFixture::class,
            ['actions' => '$rule1Actions$', 'conditions' => '$rule1Conditions$', 'apply_to' => Rule::RELATED_PRODUCTS],
            'rule1'
        ),
        DataFixture(RuleConditionFixture::class, ['attribute' => 'sku', 'value' => '$p6.sku$',], 'rule2Condition'),
        DataFixture(RuleConditionsFixture::class, ['conditions' => ['$rule2Condition$']], 'rule2Conditions'),
        DataFixture(RuleActionFixture::class, ['attribute' => 'sku', 'value' => '$p5.sku$',], 'rule2Action'),
        DataFixture(RuleActionsFixture::class, ['conditions' => ['$rule2Action$']], 'rule2Actions'),
        DataFixture(
            RuleFixture::class,
            ['actions' => '$rule2Actions$', 'conditions' => '$rule2Conditions$', 'apply_to' => Rule::UP_SELLS],
            'rule2'
        ),
        DataFixture(RuleConditionFixture::class, ['attribute' => 'sku', 'value' => '$p9.sku$',], 'rule3Condition'),
        DataFixture(RuleConditionsFixture::class, ['conditions' => ['$rule3Condition$']], 'rule3Conditions'),
        DataFixture(RuleActionFixture::class, ['attribute' => 'sku', 'value' => '$p7.sku$',], 'rule3Action'),
        DataFixture(RuleActionsFixture::class, ['conditions' => ['$rule3Action$']], 'rule3Actions'),
        DataFixture(
            RuleFixture::class,
            ['actions' => '$rule3Actions$', 'conditions' => '$rule3Conditions$', 'apply_to' => Rule::CROSS_SELLS],
            'rule3'
        ),
    ]
    public function testAttachConditionToCollection(): void
    {
        /** @var ProductCollectionFactory $collectionFactory */
        $collectionFactory = Bootstrap::getObjectManager()->create(ProductCollectionFactory::class);
        $collection = $collectionFactory->create();

        /** @var RuleFactory $ruleFactory */
        $ruleFactory = Bootstrap::getObjectManager()->create(RuleFactory::class);
        $rule = $ruleFactory->create();

        $ruleConditionArray = [
            'conditions' => [
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
        ];

        $rule->loadPost($ruleConditionArray);
        $this->model->attachConditionToCollection($collection, $rule->getConditions());

        $whereString = "/\(category_id IN \('3'\).+\(`e`\.`entity_id` = '2017-09-15 00:00:00'\)"
            .".+\(`e`\.`sku` IN \('sku1', 'sku2', 'sku3', 'sku4', 'sku5'\)"
            . ".+ORDER BY \(FIELD\(`e`.`sku`, 'sku1', 'sku2', 'sku3', 'sku4', 'sku5'\)\)/";
        $this->assertEquals(1, preg_match($whereString, $collection->getSelectSql(true)));
    }

    /**
     * @return array
     */
    public static function attachConditionToCollectionDataProvider(): array
    {
        return [
            [
                'productName' => 'p2',
                'relatedProducts' => ['p1', 'p3'],
                'upsellProducts' => [],
                'crosssellProducts' => [],
                'config' => [
                    'catalog/magento_targetrule/related_position_behavior' => Rule::BOTH_SELECTED_AND_RULE_BASED
                ]
            ],
            [
                'productName' => 'p2',
                'relatedProducts' => ['p1'],
                'upsellProducts' => [],
                'crosssellProducts' => [],
                'config' => [
                    'catalog/magento_targetrule/related_position_behavior' => Rule::SELECTED_ONLY
                ]
            ]
        ];
    }
}
