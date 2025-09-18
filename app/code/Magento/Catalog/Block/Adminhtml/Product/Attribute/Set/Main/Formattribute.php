<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main;

use Magento\Backend\Block\Widget\Form;

class Formattribute extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare the form
     *
     * @return void
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('set_fieldset', ['legend' => __('Add New Attribute')]);

        $fieldset->addField(
            'new_attribute',
            'text',
            ['label' => __('Name'), 'name' => 'new_attribute', 'required' => true]
        );

        $fieldset->addField(
            'submit',
            'note',
            [
                'text' => $this->getLayout()->createBlock(
                    \Magento\Backend\Block\Widget\Button::class
                )->setData(
                    ['label' => __('Add Attribute'), 'onclick' => 'this.form.submit();', 'class' => 'add']
                )->toHtml()
            ]
        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $this->setForm($form);
    }
}
