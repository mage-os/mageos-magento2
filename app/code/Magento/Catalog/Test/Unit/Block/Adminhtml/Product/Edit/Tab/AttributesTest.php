<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product\Edit\Tab;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes\Create;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes\Search;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Tier;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Group;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Attributes block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class AttributesTest extends TestCase
{
    /**
     * @var Attributes&MockObject
     */
    private $attributesMock;

    /**
     * @var FormFactory&MockObject
     */
    private $formFactoryMock;

    /**
     * @var Registry&MockObject
     */
    private $registryMock;

    /**
     * @var ManagerInterface&MockObject
     */
    private $eventManagerMock;

    /**
     * @var AuthorizationInterface&MockObject
     */
    private $authorizationMock;

    /**
     * @var LayoutInterface&MockObject
     */
    private $layoutMock;

    protected function setUp(): void
    {
        $this->formFactoryMock = $this->createMock(FormFactory::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->eventManagerMock = $this->createMock(ManagerInterface::class);
        $this->authorizationMock = $this->createMock(AuthorizationInterface::class);
        $this->layoutMock = $this->createMock(LayoutInterface::class);

        $this->attributesMock = $this->getMockBuilder(Attributes::class)
            ->disableOriginalConstructor()
            ->addMethods(['getGroup', 'getGroupAttributes'])
            ->onlyMethods(['_setFieldset', 'setForm'])
            ->getMock();

        $this->injectDependencies();
    }

    /**
     * Test getAdditionalElementTypes returns all default element types
     *
     * @return void
     */
    public function testGetAdditionalElementTypesReturnsDefaultTypes(): void
    {
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with('adminhtml_catalog_product_edit_element_types');

        $result = $this->invokeProtectedMethod('_getAdditionalElementTypes');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('price', $result);
        $this->assertEquals(\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price::class, $result['price']);
        $this->assertArrayHasKey('weight', $result);
        $this->assertEquals(\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight::class, $result['weight']);
        $this->assertArrayHasKey('gallery', $result);
        $this->assertEquals(\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery::class, $result['gallery']);
        $this->assertArrayHasKey('image', $result);
        $this->assertEquals(\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Image::class, $result['image']);
        $this->assertArrayHasKey('boolean', $result);
        $this->assertEquals(\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Boolean::class, $result['boolean']);
        $this->assertArrayHasKey('textarea', $result);
        $this->assertEquals(\Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg::class, $result['textarea']);
    }

    /**
     * Test getAdditionalElementTypes merges custom types from event
     *
     * @return void
     */
    public function testGetAdditionalElementTypesMergesCustomTypes(): void
    {
        $customTypes = [
            'custom_type' => 'Custom\Type\Class',
            'another_type' => 'Another\Type\Class'
        ];

        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with('adminhtml_catalog_product_edit_element_types')
            ->willReturnCallback(function ($eventName, $data) use ($customTypes) {
                unset($eventName);
                $data['response']->setTypes($customTypes);
            });

        $result = $this->invokeProtectedMethod('_getAdditionalElementTypes');

        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('custom_type', $result);
        $this->assertArrayHasKey('another_type', $result);
        $this->assertEquals('Custom\Type\Class', $result['custom_type']);
        $this->assertEquals('Another\Type\Class', $result['another_type']);
    }

    /**
     * Test prepareForm returns early when no group is set
     *
     * @return void
     */
    public function testPrepareFormReturnsEarlyWhenNoGroup(): void
    {
        $this->formFactoryMock->expects($this->never())->method('create');
        $this->eventManagerMock->expects($this->never())->method('dispatch');
        $this->attributesMock->method('getGroup')->willReturn(null);

        $this->invokeProtectedMethod('_prepareForm');
    }

    /**
     * Test prepareForm creates form and calls setDataObject
     *
     * @return void
     */
    public function testPrepareFormCreatesFormWithProduct(): void
    {
        $productMock = $this->createProductMock(1, false);
        $formMock = $this->createFormMock();
        $groupMock = $this->createGroupMock('general', 'General');

        $this->setupRegistryForProduct($productMock, true);
        $this->setupFormFactory($formMock);
        $this->setupBasicFormExpectations($formMock);

        $formMock->expects($this->once())->method('setDataObject')->with($productMock)->willReturnSelf();

        $this->attributesMock->method('getGroup')->willReturn($groupMock);
        $this->attributesMock->method('getGroupAttributes')->willReturn([]);

        $this->invokeProtectedMethod('_prepareForm');
    }

    /**
     * Test prepareForm sets tier price renderer
     *
     * @return void
     */
    public function testPrepareFormSetsTierPriceRenderer(): void
    {
        $productMock = $this->createProductMock(1, false);
        $formMock = $this->createFormMock();
        $tierPriceElementMock = $this->createMock(AbstractElement::class);
        $tierBlockMock = $this->createMock(Tier::class);
        $groupMock = $this->createGroupMock('pricing', 'Pricing');

        $this->setupRegistryForProduct($productMock, true);
        $this->setupFormFactory($formMock);
        $this->setupBasicFormExpectations($formMock, false);

        $formMock->method('getElement')->willReturnMap([
            ['tier_price', $tierPriceElementMock],
            ['media_gallery', null]
        ]);

        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(Tier::class)
            ->willReturn($tierBlockMock);

        $tierPriceElementMock->expects($this->once())
            ->method('setRenderer')
            ->with($tierBlockMock);

        $this->attributesMock->method('getGroup')->willReturn($groupMock);
        $this->attributesMock->method('getGroupAttributes')->willReturn([]);

        $this->invokeProtectedMethod('_prepareForm');
    }

    /**
     * Test prepareForm creates attribute controls when authorized
     *
     * @return void
     */
    public function testPrepareFormCreatesAttributeControlsWhenAuthorized(): void
    {
        $productMock = $this->createProductMock(1, false);
        $productMock->method('getStoreId')->willReturn(1);
        $productMock->method('getAttributeSetId')->willReturn(4);
        $productMock->method('getTypeId')->willReturn('simple');

        $formMock = $this->createFormMock();
        $formMock->method('getDataObject')->willReturn($productMock);
        $fieldsetMock = $this->createFieldsetMock();
        $createBlockMock = $this->createAttributeCreateBlockMock();
        $searchBlockMock = $this->createAttributeSearchBlockMock();
        $groupMock = $this->createGroupMock('general', 'General', 1);

        $this->setupRegistryForProduct($productMock, true);
        $this->setupFormFactory($formMock);
        $formMock->method('addFieldset')->willReturn($fieldsetMock);
        $formMock->method('setDataObject')->willReturnSelf();
        $formMock->method('getElement')->willReturn(null);
        $formMock->method('setFieldNameSuffix')->willReturnSelf();
        $formMock->method('addValues')->willReturnSelf();

        $this->authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_Catalog::attributes_attributes')
            ->willReturn(true);

        $this->layoutMock->expects($this->exactly(2))
            ->method('createBlock')
            ->willReturnOnConsecutiveCalls($createBlockMock, $searchBlockMock);

        $searchBlockMock->expects($this->once())->method('setAttributeCreate');
        $fieldsetMock->expects($this->once())->method('setHeaderBar');

        $this->attributesMock->method('getGroup')->willReturn($groupMock);
        $this->attributesMock->method('getGroupAttributes')->willReturn([]);

        $this->invokeProtectedMethod('_prepareForm');
    }

    /**
     * Test prepareForm sets default values for new product
     *
     * @return void
     */
    public function testPrepareFormSetsDefaultValuesForNewProduct(): void
    {
        $productMock = $this->createProductMock(null, false);
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->method('getAttributeCode')->willReturn('custom_attribute');
        $attributeMock->method('getDefaultValue')->willReturn('default_value');

        $formMock = $this->createFormMock();
        $groupMock = $this->createGroupMock('general', 'General');

        $this->setupRegistryForProduct($productMock, true);
        $this->setupFormFactory($formMock);
        $this->setupBasicFormExpectations($formMock);

        $formMock->expects($this->once())
            ->method('addValues')
            ->with($this->callback(function ($values) {
                return isset($values['custom_attribute']) &&
                       $values['custom_attribute'] === 'default_value';
            }))
            ->willReturnSelf();

        $this->attributesMock->method('getGroup')->willReturn($groupMock);
        $this->attributesMock->method('getGroupAttributes')->willReturn([$attributeMock]);

        $this->invokeProtectedMethod('_prepareForm');
    }

    /**
     * Test prepareForm locks attributes when product has locked attributes
     *
     * @return void
     */
    public function testPrepareFormLocksAttributesWhenProductHasLockedAttributes(): void
    {
        $productMock = $this->createProductMock(1, true, ['name']);
        $formMock = $this->createFormMock();
        $elementMock = $this->createMock(AbstractElement::class);
        $groupMock = $this->createGroupMock('general', 'General');

        $this->setupRegistryForProduct($productMock, true);
        $this->setupFormFactory($formMock);
        $this->setupBasicFormExpectations($formMock, false);

        $formMock->method('getElement')->willReturnMap([
            ['tier_price', null],
            ['media_gallery', null],
            ['name', $elementMock]
        ]);

        $elementMock->expects($this->once())->method('setReadonly')->with(true, true);

        $this->attributesMock->method('getGroup')->willReturn($groupMock);
        $this->attributesMock->method('getGroupAttributes')->willReturn([]);

        $this->invokeProtectedMethod('_prepareForm');
    }

    /**
     * Test prepareForm dispatches event with form and layout
     *
     * @return void
     */
    public function testPrepareFormDispatchesEventWithFormAndLayout(): void
    {
        $productMock = $this->createProductMock(1, false);
        $formMock = $this->createFormMock();
        $groupMock = $this->createGroupMock('general', 'General');

        $this->setupRegistryForProduct($productMock, true);
        $this->setupFormFactory($formMock);
        $this->setupBasicFormExpectations($formMock);

        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with(
                'adminhtml_catalog_product_edit_prepare_form',
                $this->callback(function ($data) use ($formMock) {
                    return isset($data['form']) && $data['form'] === $formMock;
                })
            );

        $this->attributesMock->method('getGroup')->willReturn($groupMock);
        $this->attributesMock->method('getGroupAttributes')->willReturn([]);

        $this->invokeProtectedMethod('_prepareForm');
    }

    /**
     * Inject dependencies into the attributes mock via reflection
     *
     * @return void
     */
    private function injectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->attributesMock);

        $dependencies = [
            '_eventManager' => $this->eventManagerMock,
            '_layout' => $this->layoutMock,
            '_formFactory' => $this->formFactoryMock,
            '_coreRegistry' => $this->registryMock,
            '_authorization' => $this->authorizationMock
        ];

        foreach ($dependencies as $propertyName => $value) {
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($this->attributesMock, $value);
        }
    }

    /**
     * Invoke a protected method via reflection
     *
     * @param string $methodName
     * @return mixed
     */
    private function invokeProtectedMethod(string $methodName): mixed
    {
        $reflection = new \ReflectionClass($this->attributesMock);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invoke($this->attributesMock);
    }

    /**
     * Create a configured product mock
     *
     * @param int|null $id
     * @param bool $hasLockedAttributes
     * @param array $lockedAttributes
     * @return Product&MockObject
     */
    private function createProductMock($id, bool $hasLockedAttributes, array $lockedAttributes = []): Product&MockObject
    {
        $productMock = $this->createMock(Product::class);
        $productMock->method('getId')->willReturn($id);
        $productMock->method('hasLockedAttributes')->willReturn($hasLockedAttributes);
        $productMock->method('getLockedAttributes')->willReturn($lockedAttributes);
        $productMock->method('dataHasChangedFor')->willReturn(false);
        return $productMock;
    }

    /**
     * Create a configured form mock
     *
     * @return Form&MockObject
     */
    private function createFormMock(): Form&MockObject
    {
        return $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->addMethods(['setDataObject', 'getDataObject', 'setFieldNameSuffix'])
            ->onlyMethods(['addFieldset', 'getElement', 'addValues'])
            ->getMock();
    }

    /**
     * Create a configured fieldset mock
     *
     * @return Fieldset&MockObject
     */
    private function createFieldsetMock(): Fieldset&MockObject
    {
        return $this->getMockBuilder(Fieldset::class)
            ->disableOriginalConstructor()
            ->addMethods(['setHeaderBar'])
            ->getMock();
    }

    /**
     * Create a configured group mock
     *
     * @param string $code
     * @param string $name
     * @param int|null $id
     * @return Group&MockObject
     */
    private function createGroupMock(string $code, string $name, ?int $id = null): Group&MockObject
    {
        $groupMock = $this->getMockBuilder(Group::class)
            ->disableOriginalConstructor()
            ->addMethods(['getAttributeGroupCode'])
            ->onlyMethods(['getAttributeGroupName', 'getId'])
            ->getMock();

        $groupMock->method('getAttributeGroupCode')->willReturn($code);
        $groupMock->method('getAttributeGroupName')->willReturn($name);

        if ($id !== null) {
            $groupMock->method('getId')->willReturn($id);
        }

        return $groupMock;
    }

    /**
     * Setup registry for product and use_wrapper
     *
     * @param Product&MockObject $productMock
     * @param bool $useWrapper
     * @return void
     */
    private function setupRegistryForProduct($productMock, bool $useWrapper): void
    {
        $this->registryMock->method('registry')->willReturnMap([
            ['product', $productMock],
            ['use_wrapper', $useWrapper]
        ]);
    }

    /**
     * Setup form factory to return form mock
     *
     * @param Form&MockObject $formMock
     * @return void
     */
    private function setupFormFactory($formMock): void
    {
        $this->formFactoryMock->method('create')->willReturn($formMock);
    }

    /**
     * Setup basic form expectations common to most tests
     *
     * @param Form&MockObject $formMock
     * @param bool $includeGetElement
     * @return void
     */
    private function setupBasicFormExpectations($formMock, bool $includeGetElement = true): void
    {
        $fieldsetMock = $this->createFieldsetMock();

        $formMock->method('addFieldset')->willReturn($fieldsetMock);

        if ($includeGetElement) {
            $formMock->method('getElement')->willReturn(null);
        }

        $formMock->method('setFieldNameSuffix')->willReturnSelf();
        $formMock->method('addValues')->willReturnSelf();
        $formMock->method('setDataObject')->willReturnSelf();
    }

    /**
     * Create attribute create block mock
     *
     * @return Create&MockObject
     */
    private function createAttributeCreateBlockMock(): Create&MockObject
    {
        $configMock = $this->getMockBuilder(DataObject::class)
            ->disableOriginalConstructor()
            ->addMethods([
                'setAttributeGroupCode', 'setTabId', 'setGroupId',
                'setStoreId', 'setAttributeSetId', 'setTypeId', 'setProductId'
            ])
            ->getMock();

        $configMock->method('setAttributeGroupCode')->willReturnSelf();
        $configMock->method('setTabId')->willReturnSelf();
        $configMock->method('setGroupId')->willReturnSelf();
        $configMock->method('setStoreId')->willReturnSelf();
        $configMock->method('setAttributeSetId')->willReturnSelf();
        $configMock->method('setTypeId')->willReturnSelf();
        $configMock->method('setProductId')->willReturnSelf();

        $createBlockMock = $this->createMock(Create::class);
        $createBlockMock->method('getConfig')->willReturn($configMock);
        $createBlockMock->method('toHtml')->willReturn('<create-html>');

        return $createBlockMock;
    }

    /**
     * Create attribute search block mock
     *
     * @return Search&MockObject
     */
    private function createAttributeSearchBlockMock(): Search&MockObject
    {
        $searchBlockMock = $this->getMockBuilder(Search::class)
            ->disableOriginalConstructor()
            ->addMethods(['setGroupId', 'setGroupCode', 'setAttributeCreate'])
            ->onlyMethods(['toHtml'])
            ->getMock();

        $searchBlockMock->method('setGroupId')->willReturnSelf();
        $searchBlockMock->method('setGroupCode')->willReturnSelf();
        $searchBlockMock->method('setAttributeCreate')->willReturnSelf();
        $searchBlockMock->method('toHtml')->willReturn('<search-html>');

        return $searchBlockMock;
    }
}
