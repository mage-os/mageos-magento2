<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\User\Block;

/**
 * Magento_User role block
 *
 * @api
 * @since 100.0.2
 */
class Role extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_controller = 'user_role';

    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_User';

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_headerText = __('Roles');
        $this->_addButtonLabel = __('Add New Role');
        parent::_construct();
    }

    /**
     * Get a URL to create a role
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/editrole');
    }

    /**
     * Prepare the layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if (!$this->getLayout()->getChildName($this->getNameInLayout(), 'grid')) {
            $this->setChild(
                'grid',
                $this->getLayout()->createBlock(
                    $this->_blockGroup . '\\Block\\Role\\Grid',
                    $this->_controller . '.grid'
                )->setSaveParametersInSession(
                    true
                )
            );
        }
        return \Magento\Backend\Block\Widget\Container::_prepareLayout();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('permissions_role_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
