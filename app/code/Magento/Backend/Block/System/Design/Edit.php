<?php
/**
 * Copyright 2011 Adobe
 * All Rights Reserved.
 */
namespace Magento\Backend\Block\System\Design;

use Magento\Framework\Escaper;
use Magento\Framework\App\ObjectManager;

/**
 * Edit store design schedule block.
 */
class Edit extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::system/design/edit.phtml';

    /**
     * Application data storage
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Escaper for secure output rendering
     *
     * @var Escaper
     */
    private $escaper;

    /**
     * @inheritdoc
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * @param Escaper|null $escaper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = [],
        ?Escaper $escaper = null
    ) {
        $this->_coreRegistry = $registry;
        $this->escaper = $escaper ?? ObjectManager::getInstance()->get(Escaper::class);
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('design_edit');
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'back_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('adminhtml/*/') . '\')',
                'class' => 'back'
            ]
        );

        if ($this->getDesignChangeId()) {
            $confirmMessage = $this->escaper->escapeJs(
                $this->escaper->escapeHtml(__('Are you sure?'))
            );
            $deleteOnClick = 'deleteConfirm(\'' . $confirmMessage . '\', \'' .
                $this->getDeleteUrl() . '\', {data: {}})';
            $this->getToolbar()->addChild(
                'delete_button',
                \Magento\Backend\Block\Widget\Button::class,
                [
                    'label' => __('Delete'),
                    'onclick' => $deleteOnClick,
                    'class' => 'delete'
                ]
            );
        }

        $this->getToolbar()->addChild(
            'save_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Save'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#design-edit-form']],
                ]
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * Return design change Id.
     *
     * @return string
     */
    public function getDesignChangeId()
    {
        return $this->_coreRegistry->registry('design')->getId();
    }

    /**
     * Return delete url.
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('adminhtml/*/delete', ['_current' => true]);
    }

    /**
     * Return save url for edit form.
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('adminhtml/*/save', ['_current' => true]);
    }

    /**
     * Return validation url for edit form.
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('adminhtml/*/validate', ['_current' => true]);
    }

    /**
     * Return page header.
     *
     * @return string
     */
    public function getHeader()
    {
        if ($this->_coreRegistry->registry('design')->getId()) {
            $header = __('Edit Design Change');
        } else {
            $header = __('New Store Design Change');
        }
        return $header;
    }
}
