<?php
/**
 * Copyright 2011 Adobe.
 * All Rights Reserved.
 */

namespace Magento\Customer\Model;

/**
 * Customer Form Model
 */
class Form extends \Magento\Eav\Model\Form
{
    /**
     * XML configuration paths for "Disable autocomplete on storefront" property
     */
    public const XML_PATH_ENABLE_AUTOCOMPLETE = 'customer/password/autocomplete_on_storefront';

    /**
     * XML configuration paths for "Enable CAPTCHA on Storefront" property
     */
    public const XML_PATH_CUSTOMER_CAPTCHA_ENABLED = 'customer/captcha/enable';

    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'Magento_Customer';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'customer';

    /**
     * Get EAV Entity Form Attribute Collection for Customer exclude 'created_at'
     *
     * @return \Magento\Customer\Model\ResourceModel\Form\Attribute\Collection
     */
    protected function _getFormAttributeCollection()
    {
        return parent::_getFormAttributeCollection()->addFieldToFilter('attribute_code', ['neq' => 'created_at']);
    }
}
