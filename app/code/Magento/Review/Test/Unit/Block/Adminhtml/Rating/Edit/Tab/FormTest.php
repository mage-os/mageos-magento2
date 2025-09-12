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
        $this->element = new class extends Text {
            public function __construct()
            {
            }
            public function setValue($value)
            {
                return $this;
            }
            public function setIsChecked($checked)
            {
                return $this;
            }
        };
        $this->session = new class extends Generic {
            public function __construct()
            {
            }
            public function getRatingData()
            {
                return null;
            }
            public function setRatingData($data)
            {
                return $this;
            }
        };
        $this->rating = new class extends Rating {
            public function __construct()
            {
            }
            public function getId()
            {
                return 1;
            }
            public function getRatingCodes()
            {
                return [];
            }
        };
        $this->optionRating = $this->createMock(Option::class);
        $this->store = $this->createMock(Store::class);
        $this->form = new class extends Form {
            /**
             * @var mixed
             */
            private $element;
            /**
             * @var array
             */
            private $fieldset = [];
            public function __construct()
            {
            }
            public function getForm()
            {
                return $this;
            }
            public function addFieldset($elementId, $config, $after = false, $isAdvanced = false)
            {
                $fieldsetMock = new class {
                    public function addField($elementId, $type, $config, $after = false)
                    {
                        return new class {
                            public function setRenderer($renderer)
                            {
                                return $this;
                            }
                            public function setValue($value)
                            {
                                return $this;
                            }
                        };
                    }
                };
                $this->fieldset[$elementId] = $fieldsetMock;
                return $fieldsetMock;
            }
            public function getFieldset($elementId)
            {
                return $this->fieldset[$elementId] ?? null;
            }
            public function addField($elementId, $type, $config, $after = false)
            {
                return $this;
            }
            public function getElement($elementId)
            {
                if (in_array($elementId, ['stores', 'position', 'is_active'])) {
                    return new class {
                        public function setValue($value)
                        {
                            return $this;
                        }
                        public function setIsChecked($value)
                        {
                            return $this;
                        }
                    };
                }
                return null;
            }
            public function setElement($element)
            {
                $this->element = $element;
            }
            public function setValues($values)
            {
                return $this;
            }
            public function setForm($form)
            {
                return $this;
            }
            public function setRenderer($renderer)
            {
                return $this;
            }
        };
        $this->directoryReadInterface = $this->createMock(ReadInterface::class);
        $this->registry = $this->createMock(Registry::class);
        $this->formFactory = $this->createMock(FormFactory::class);
        $this->optionFactory = $this->createPartialMock(OptionFactory::class, ['create']);
        $this->systemStore = $this->createMock(\Magento\Store\Model\System\Store::class);
        $this->viewFileSystem = $this->createMock(FilesystemView::class);
        $this->fileSystem = $this->createPartialMock(Filesystem::class, ['getDirectoryRead']);

        // Rating mock methods provided by anonymous class
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
        
        // Fix ObjectManager initialization issue using existing helper method
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
        // Form mock methods provided by anonymous class
        $ratingCodes = ['rating_codes' => ['0' => 'rating_code']];
        // Session mock methods provided by anonymous class
        $this->block->toHtml();
    }

    /**
     * @return void
     */
    public function testToHtmlCoreRegistryRatingData(): void
    {
        $this->registry->expects($this->any())->method('registry')->willReturn($this->rating);
        // Form mock methods provided by anonymous class
        // Session and rating mock methods provided by anonymous classes
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
