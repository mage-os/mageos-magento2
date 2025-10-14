<?php
/**
 * Copyright 2018 Adobe
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
use Magento\Framework\Data\Test\Unit\Helper\ElementTestHelper;
use Magento\Framework\Data\Test\Unit\Helper\FieldsetTestHelper;
use Magento\Framework\Data\Test\Unit\Helper\FormTestHelper;
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
        $this->element = $this->createPartialMock(Text::class, []);
        $elementReflection = new \ReflectionClass($this->element);
        $elementDataProperty = $elementReflection->getProperty('_data');
        $elementDataProperty->setValue($this->element, []);
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
        $this->form = new FormTestHelper();
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
        
        $objects = [
            [
                \Magento\Backend\Block\Template\Context::class,
                $this->createMock(\Magento\Backend\Block\Template\Context::class)
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
                'filesystem' => $this->fileSystem
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
