<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Block\Adminhtml\Product\Edit\NewCategory;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\Form\Element\Note;
use Magento\Framework\Data\Form\Element\Select;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Unit test for NewCategory block.
 */
class NewCategoryTest extends TestCase
{
    private NewCategory $block;
    private FormFactory|MockObject $formFactory;
    private Registry|MockObject $registry;
    private EncoderInterface|MockObject $jsonEncoder;
    private CategoryFactory|MockObject $categoryFactory;
    private SecureHtmlRenderer|MockObject $secureRenderer;
    private UrlInterface|MockObject $urlBuilder;

    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);
        $objectManagerHelper->prepareObjectManager();

        $this->formFactory = $this->createMock(FormFactory::class);
        $this->registry = $this->createMock(Registry::class);
        $this->jsonEncoder = $this->getMockForAbstractClass(EncoderInterface::class);
        $this->categoryFactory = $this->createMock(CategoryFactory::class);
        $this->secureRenderer = $this->createMock(SecureHtmlRenderer::class);
        $this->urlBuilder = $this->getMockForAbstractClass(UrlInterface::class);

        $context = $this->createMock(Context::class);
        $context->method('getUrlBuilder')->willReturn($this->urlBuilder);

        $this->block = new NewCategory(
            $context,
            $this->registry,
            $this->formFactory,
            $this->jsonEncoder,
            $this->categoryFactory,
            [],
            $this->secureRenderer
        );
    }

    /**
     * Tests that _prepareForm creates form with correct structure and fields.
     */
    public function testPrepareFormCreatesFormWithAllFields(): void
    {
        $form = $this->createMock(Form::class);
        $fieldset = $this->createMock(Fieldset::class);
        $noteField = $this->createMock(Note::class);
        $nameField = $this->createMock(Text::class);
        $parentField = $this->createMock(Select::class);

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with([
                'data' => [
                    'id' => 'new_category_form',
                    'class' => 'admin__scope-old'
                ]
            ])
            ->willReturn($form);

        $form->expects($this->once())
            ->method('addField')
            ->with('new_category_messages', 'note', [])
            ->willReturn($noteField);

        $form->expects($this->once())
            ->method('addFieldset')
            ->with('new_category_form_fieldset', [])
            ->willReturn($fieldset);

        // Mock category options - need 2 categories for options to be returned
        $category1 = $this->createMock(Category::class);
        $category1->method('getEntityId')->willReturn(1);
        $category1->method('getName')->willReturn('Root');

        $category2 = $this->createMock(Category::class);
        $category2->method('getEntityId')->willReturn(2);
        $category2->method('getName')->willReturn('Default Category');

        $this->setupCategoryCollectionMock([
            1 => $category1,
            2 => $category2
        ]);

        $fieldset->expects($this->exactly(2))
            ->method('addField')
            ->willReturnCallback(
                function ($id, $type, $config) use ($nameField, $parentField) {
                    if ($id === 'new_category_name') {
                        $this->assertSame('text', $type);
                        $this->assertSame('Category Name', $config['label']->getText());
                        $this->assertSame('Category Name', $config['title']->getText());
                        $this->assertTrue($config['required']);
                        $this->assertSame('new_category_name', $config['name']);
                        return $nameField;
                    }
                    if ($id === 'new_category_parent') {
                        $this->assertSame('select', $type);
                        $this->assertSame('Parent Category', $config['label']->getText());
                        $this->assertSame('Parent Category', $config['title']->getText());
                        $this->assertTrue($config['required']);
                        $this->assertSame([2 => 'Default Category'], $config['options']);
                        $this->assertSame('validate-parent-category', $config['class']);
                        $this->assertSame('new_category_parent', $config['name']);
                        $this->assertNotEmpty($config['note']);
                        return $parentField;
                    }
                    return null;
                }
            );

        $this->invokePrepareForm();

        $reflectionProperty = (new ReflectionClass($this->block))->getProperty('_form');
        $reflectionProperty->setAccessible(true);
        $assignedForm = $reflectionProperty->getValue($this->block);

        $this->assertSame($form, $assignedForm);
    }

    /**
     * Tests getParentCategoryOptions returns correct options when 2 categories exist.
     */
    public function testGetParentCategoryOptionsWithTwoCategories(): void
    {
        $category1 = $this->createMock(Category::class);
        $category1->method('getEntityId')->willReturn(1);
        $category1->method('getName')->willReturn('Root');

        $category2 = $this->createMock(Category::class);
        $category2->method('getEntityId')->willReturn(2);
        $category2->method('getName')->willReturn('Default Category');

        $this->setupCategoryCollectionMock([
            1 => $category1,
            2 => $category2
        ]);

        $result = $this->invokeGetParentCategoryOptions();

        $this->assertEquals([2 => 'Default Category'], $result);
    }

    /**
     * Tests getParentCategoryOptions returns empty array when not 2 categories.
     */
    public function testGetParentCategoryOptionsWithNotTwoCategories(): void
    {
        $category1 = $this->createMock(Category::class);
        $category1->method('getEntityId')->willReturn(1);
        $category1->method('getName')->willReturn('Root');

        $category2 = $this->createMock(Category::class);
        $category2->method('getEntityId')->willReturn(2);
        $category2->method('getName')->willReturn('Default Category');

        $category3 = $this->createMock(Category::class);
        $category3->method('getEntityId')->willReturn(3);
        $category3->method('getName')->willReturn('Custom Category');

        $this->setupCategoryCollectionMock([
            1 => $category1,
            2 => $category2,
            3 => $category3
        ]);

        $result = $this->invokeGetParentCategoryOptions();

        $this->assertEquals([], $result);
    }

    /**
     * Tests getParentCategoryOptions returns empty array when no categories.
     */
    public function testGetParentCategoryOptionsWithNoCategories(): void
    {
        $this->setupCategoryCollectionMock([]);

        $result = $this->invokeGetParentCategoryOptions();

        $this->assertEquals([], $result);
    }

    /**
     * Tests getSaveCategoryUrl returns correct URL.
     */
    public function testGetSaveCategoryUrl(): void
    {
        $expectedUrl = 'http://example.com/admin/catalog/category/save';

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('catalog/category/save')
            ->willReturn($expectedUrl);

        $result = $this->block->getSaveCategoryUrl();

        $this->assertEquals($expectedUrl, $result);
    }

    /**
     * Tests getAfterElementHtml returns script tag with widget initialization.
     */
    public function testGetAfterElementHtml(): void
    {
        $suggestUrl = 'http://example.com/admin/catalog/category/suggestCategories';
        $saveUrl = 'http://example.com/admin/catalog/category/save';
        $encodedOptions = '{"suggestOptions":{"source":"'
            . $suggestUrl . '","valueField":"#new_category_parent","className":"category-select",
            "multiselect":true,"showAll":true},"saveCategoryUrl":"'
            . $saveUrl . '"}';

        $this->urlBuilder->expects($this->exactly(2))
            ->method('getUrl')
            ->willReturnCallback(function ($route) use ($suggestUrl, $saveUrl) {
                if ($route === 'catalog/category/suggestCategories') {
                    return $suggestUrl;
                }
                if ($route === 'catalog/category/save') {
                    return $saveUrl;
                }
                return '';
            });

        $this->jsonEncoder->expects($this->once())
            ->method('encode')
            ->with([
                'suggestOptions' => [
                    'source' => $suggestUrl,
                    'valueField' => '#new_category_parent',
                    'className' => 'category-select',
                    'multiselect' => true,
                    'showAll' => true,
                ],
                'saveCategoryUrl' => $saveUrl,
            ])
            ->willReturn($encodedOptions);

        $expectedScript = <<<HTML
require(["jquery","mage/mage"],function($) {  // waiting for dependencies at first
    $(function(){ // waiting for page to load to have '#category_ids-template' available
        $('#new-category').mage('newCategoryDialog', {$encodedOptions});
    });
});
HTML;

        $this->secureRenderer->expects($this->once())
            ->method('renderTag')
            ->with('script', [], $expectedScript, false)
            ->willReturn('<script>' . $expectedScript . '</script>');

        $result = $this->block->getAfterElementHtml();

        $this->assertStringContainsString('<script>', $result);
        $this->assertStringContainsString('newCategoryDialog', $result);
        $this->assertStringContainsString($encodedOptions, $result);
    }

    /**
     * Tests constructor sets use container to true.
     */
    public function testConstructorSetsUseContainer(): void
    {
        // UseContainer is set via setUseContainer(true) in constructor
        // Verify by checking it's set in the block's data
        $this->assertTrue($this->block->getData('use_container'));
    }

    /**
     * Invokes protected _prepareForm method.
     */
    private function invokePrepareForm(): void
    {
        $reflection = new ReflectionClass($this->block);
        $method = $reflection->getMethod('_prepareForm');
        $method->setAccessible(true);
        $method->invoke($this->block);
    }

    /**
     * Invokes protected _getParentCategoryOptions method.
     *
     * @return array
     */
    private function invokeGetParentCategoryOptions(): array
    {
        $reflection = new ReflectionClass($this->block);
        $method = $reflection->getMethod('_getParentCategoryOptions');
        $method->setAccessible(true);
        return $method->invoke($this->block);
    }

    /**
     * Sets up category collection mock with given items.
     *
     * @param array $items
     */
    private function setupCategoryCollectionMock(array $items): void
    {
        $collection = $this->createMock(Collection::class);
        $category = $this->createMock(Category::class);

        $this->categoryFactory->expects($this->once())
            ->method('create')
            ->willReturn($category);

        $category->expects($this->once())
            ->method('getCollection')
            ->willReturn($collection);

        $collection->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('name')
            ->willReturnSelf();

        $collection->expects($this->once())
            ->method('addAttributeToSort')
            ->with('entity_id', 'ASC')
            ->willReturnSelf();

        $collection->expects($this->once())
            ->method('setPageSize')
            ->with(3)
            ->willReturnSelf();

        $collection->expects($this->once())
            ->method('load')
            ->willReturnSelf();

        $collection->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
    }
}
