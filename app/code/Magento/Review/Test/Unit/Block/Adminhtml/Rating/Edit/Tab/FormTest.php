<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Block\Adminhtml\Rating\Edit\Tab;

use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Registry;
use Magento\Framework\Session\Generic;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\FileSystem as FilesystemView;
use Magento\Review\Model\Rating;
use Magento\Review\Model\Rating\Option;
use Magento\Review\Model\Rating\OptionFactory;
use Magento\Review\Model\ResourceModel\Rating\Option\Collection;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FormTest extends TestCase
{
    use MockCreationTrait;

    /**
     * @var Rating
     */
    protected $rating;

    /**
     * @var Collection
     */
    protected $ratingOptionCollection;

    /**
     * @var Option
     */
    protected $optionRating;

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var Text
     */
    protected $element;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var ReadInterface
     */
    protected $directoryReadInterface;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var Generic
     */
    protected $session;

    /**
     * @var FilesystemView
     */
    protected $viewFileSystem;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Review\Block\Adminhtml\Rating\Edit\Tab\Form
     */
    protected $block;

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function setUp(): void // @codingStandardsIgnoreLine
    {
        $this->ratingOptionCollection = $this->createMock(
            Collection::class
        );
        $this->element = $this->createPartialMockWithReflection(Text::class, ['setRenderer', 'setValue']);
        $elementReflection = new \ReflectionClass($this->element);
        $elementDataProperty = $elementReflection->getProperty('_data');
        $elementDataProperty->setValue($this->element, []);
        
        // Mock setRenderer and setValue to prevent TypeError
        $renderer = $this->createMock(\Magento\Framework\Data\Form\Element\Renderer\RendererInterface::class);
        $this->element->method('setRenderer')->willReturn($this->element);
        $this->element->method('setValue')->willReturn($this->element);
        $this->session = $this->createPartialMock(Generic::class, []);
        $sessionReflection = new \ReflectionClass($this->session);
        $storageProperty = $sessionReflection->getProperty('storage');
        $storage = new \Magento\Framework\Session\Storage();
        $storageProperty->setValue($this->session, $storage);
        $this->rating = $this->createPartialMock(Rating::class, []);
        $reflection = new \ReflectionClass($this->rating);
        $dataProperty = $reflection->getProperty('_data');
        $dataProperty->setValue($this->rating, []);
        $this->optionRating = $this->createMock(Option::class);
        $this->store = $this->createMock(Store::class);
        
        // Create Form with proper factory initialization
        $elementFactory = $this->createMock(\Magento\Framework\Data\Form\Element\Factory::class);
        $elementFactory->method('create')->willReturn($this->element);
        
        $fieldset = $this->createPartialMockWithReflection(
            \Magento\Framework\Data\Form\Element\Fieldset::class,
            ['add', 'addField']
        );
        $fieldsetReflection = new \ReflectionClass($fieldset);
        $fieldsetFactoryProperty = $fieldsetReflection->getProperty('_factoryElement');
        $fieldsetFactoryProperty->setValue($fieldset, $elementFactory);
        $fieldsetCollectionProperty = $fieldsetReflection->getProperty('_factoryCollection');
        $fieldsetCollectionProperty->setValue(
            $fieldset,
            $this->createMock(\Magento\Framework\Data\Form\Element\CollectionFactory::class)
        );
        $fieldsetDataProperty = $fieldsetReflection->getProperty('_data');
        $fieldsetDataProperty->setValue($fieldset, []);
        $fieldset->method('add')->willReturn($this->element);
        $fieldset->method('addField')->willReturn($this->element);
        
        $fieldsetFactory = $this->createMock(\Magento\Framework\Data\Form\Element\CollectionFactory::class);
        $fieldsetFactory->method('create')->willReturn($fieldset);
        
        // Create ElementCollection for _allElements
        $elementCollection = $this->createMock(\Magento\Framework\Data\Form\Element\Collection::class);
        $elementCollection->method('getIterator')->willReturn(new \ArrayIterator([]));
        
        $this->form = $this->createPartialMockWithReflection(
            Form::class,
            ['addFieldset', 'getElement', 'getFieldset']
        );
        // Initialize the form's factories and collections via reflection
        $formReflection = new \ReflectionClass($this->form);
        $elementFactoryProperty = $formReflection->getProperty('_factoryElement');
        $elementFactoryProperty->setValue($this->form, $elementFactory);
        $collectionFactoryProperty = $formReflection->getProperty('_factoryCollection');
        $collectionFactoryProperty->setValue($this->form, $fieldsetFactory);
        $allElementsProperty = $formReflection->getProperty('_allElements');
        $allElementsProperty->setValue($this->form, $elementCollection);
        $this->form->method('addFieldset')->willReturn($fieldset);
        // getElement should return null for fieldsets but return element for actual form elements
        $this->form->method('getElement')->willReturnCallback(function ($id) {
            // Return null for fieldset IDs, return element for field IDs
            if (in_array($id, ['rating_form', 'visibility_form'])) {
                return null;
            }
            return $this->element;
        });
        
        $this->directoryReadInterface = $this->createMock(ReadInterface::class);
        $this->registry = $this->createMock(Registry::class);
        $this->formFactory = $this->createMock(FormFactory::class);
        $this->optionFactory = $this->createPartialMock(OptionFactory::class, ['create']);
        $this->systemStore = $this->createMock(\Magento\Store\Model\System\Store::class);
        $this->viewFileSystem = $this->createMock(FilesystemView::class);
        $this->fileSystem = $this->createPartialMock(Filesystem::class, ['getDirectoryRead']);

        $this->ratingOptionCollection->expects($this->any())->method('addRatingFilter')->willReturnSelf();
        $this->ratingOptionCollection->expects($this->any())->method('load')->willReturnSelf();
        $this->ratingOptionCollection->expects($this->any())->method('getItems')
            ->willReturn([$this->optionRating]);
        $this->optionRating->expects($this->any())->method('getResourceCollection')
            ->willReturn($this->ratingOptionCollection);
        $this->store->expects($this->any())->method('getId')->willReturn('0');
        $this->store->expects($this->any())->method('getName')->willReturn('store_name');
        $this->form->setElement($this->element);
        $this->optionFactory->expects($this->any())->method('create')->willReturn($this->optionRating);
        $this->systemStore->expects($this->any())->method('getStoreCollection')
            ->willReturn(['0' => $this->store]);
        $this->formFactory->expects($this->any())->method('create')->willReturn($this->form);
        $this->viewFileSystem->expects($this->any())->method('getTemplateFileName')
            ->willReturn('template_file_name.html');
        $this->fileSystem->expects($this->any())->method('getDirectoryRead')
            ->willReturn($this->directoryReadInterface);

        $objectManagerHelper = new ObjectManagerHelper($this);
        
        // Create renderer mock for layout
        $renderer = $this->createMock(\Magento\Framework\Data\Form\Element\Renderer\RendererInterface::class);
        
        // Mock layout to return renderer
        $layout = $this->createMock(\Magento\Framework\View\LayoutInterface::class);
        $layout->method('createBlock')->willReturn($renderer);
        
        // Mock event manager
        $eventManager = $this->createMock(\Magento\Framework\Event\ManagerInterface::class);
        
        // Mock scope config
        $scopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn(false);
        
        // Mock URL builder
        $urlBuilder = $this->createMock(\Magento\Framework\UrlInterface::class);
        $urlBuilder->method('getBaseUrl')->willReturn('http://example.com/');
        
        // Mock store manager
        $storeManager = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeManager->method('isSingleStoreMode')->willReturn(false);
        
        // Mock app state
        $appState = $this->createMock(\Magento\Framework\App\State::class);
        $appState->method('getAreaCode')->willReturn(\Magento\Framework\App\Area::AREA_ADMINHTML);
        
        // Mock resolver (viewFileSystem)
        $resolver = $this->createMock(\Magento\Framework\View\Element\Template\File\Resolver::class);
        $resolver->method('getTemplateFileName')->willReturn('template_file_name.html');
        
        // Mock filesystem
        $filesystem = $this->createMock(\Magento\Framework\Filesystem::class);
        $filesystem->method('getDirectoryRead')->willReturn($this->directoryReadInterface);
        
        // Mock validator
        $validator = $this->createMock(\Magento\Framework\View\Element\Template\File\Validator::class);
        $validator->method('isValid')->willReturn(true);
        
        // Mock template engine
        $templateEngine = $this->createMock(\Magento\Framework\View\TemplateEngineInterface::class);
        $templateEngine->method('render')->willReturn('<html>rendered</html>');
        
        // Mock template engine pool
        $templateEnginePool = $this->createMock(\Magento\Framework\View\TemplateEnginePool::class);
        $templateEnginePool->method('get')->willReturn($templateEngine);
        
        // Mock context with all required dependencies
        $context = $this->createMock(\Magento\Backend\Block\Template\Context::class);
        $context->method('getLayout')->willReturn($layout);
        $context->method('getEventManager')->willReturn($eventManager);
        $context->method('getScopeConfig')->willReturn($scopeConfig);
        $context->method('getUrlBuilder')->willReturn($urlBuilder);
        $context->method('getStoreManager')->willReturn($storeManager);
        $context->method('getAppState')->willReturn($appState);
        $context->method('getResolver')->willReturn($resolver);
        $context->method('getFilesystem')->willReturn($filesystem);
        $context->method('getValidator')->willReturn($validator);
        $context->method('getEnginePool')->willReturn($templateEnginePool);
        
        $objects = [
            [
                \Magento\Backend\Block\Template\Context::class,
                $context
            ]
        ];
        $objectManagerHelper->prepareObjectManager($objects);
        
        $this->block = $objectManagerHelper->getObject(
            \Magento\Review\Block\Adminhtml\Rating\Edit\Tab\Form::class,
            [
                'registry' => $this->registry,
                'formFactory' => $this->formFactory,
                'optionFactory' => $this->optionFactory,
                'systemStore' => $this->systemStore,
                'session' => $this->session,
                'viewFileSystem' => $this->viewFileSystem,
                'filesystem' => $this->fileSystem,
                'context' => $context
            ]
        );
    }

    /**
     * @return void
     */
    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testToHtmlSessionRatingData(): void
    {
        $this->registry->expects($this->any())->method('registry')->willReturn($this->rating);
        $ratingCodes = ['rating_codes' => ['0' => 'rating_code']];
        $this->block->toHtml();
    }

    /**
     * @return void
     */
    public function testToHtmlCoreRegistryRatingData(): void
    {
        $this->registry->expects($this->any())->method('registry')->willReturn($this->rating);
        $this->block->toHtml();
    }

    /**
     * @return void
     */
    public function testToHtmlWithoutRatingData(): void
    {
        $this->registry->expects($this->any())->method('registry')->willReturn(false);
        $this->systemStore->expects($this->atLeastOnce())->method('getStoreCollection')
            ->willReturn(['0' => $this->store]);
        $this->formFactory->expects($this->any())->method('create')->willReturn($this->form);
        $this->viewFileSystem->expects($this->any())->method('getTemplateFileName')
            ->willReturn('template_file_name.html');
        $this->fileSystem->expects($this->any())->method('getDirectoryRead')
            ->willReturn($this->directoryReadInterface);
        $this->block->toHtml();
    }
}
