<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\AddressInterface;

/**
 * Test helper for AddressInterface with custom methods
 */
class AddressInterfaceTestHelper implements AddressInterface
{
    /**
     * Get default billing flag (custom method for tests)
     *
     * @return bool
     */
    public function getDefaultBilling(): bool
    {
        return false;
    }

    /**
     * Get default shipping flag (custom method for tests)
     *
     * @return bool
     */
    public function getDefaultShipping(): bool
    {
        return false;
    }

    // Implement AddressInterface methods as stubs
    public function getId()
    {
        return null;
    }

    public function setId($id)
    {
        return $this;
    }

    public function getCustomerId()
    {
        return null;
    }

    public function setCustomerId($customerId)
    {
        return $this;
    }

    public function getRegion()
    {
        return null;
    }

    public function setRegion(?\Magento\Customer\Api\Data\RegionInterface $region = null)
    {
        return $this;
    }

    public function getRegionId()
    {
        return null;
    }

    public function setRegionId($regionId)
    {
        return $this;
    }

    public function getCountryId()
    {
        return null;
    }

    public function setCountryId($countryId)
    {
        return $this;
    }

    public function getStreet()
    {
        return null;
    }

    public function setStreet(array $street)
    {
        return $this;
    }

    public function getCompany()
    {
        return null;
    }

    public function setCompany($company)
    {
        return $this;
    }

    public function getTelephone()
    {
        return null;
    }

    public function setTelephone($telephone)
    {
        return $this;
    }

    public function getFax()
    {
        return null;
    }

    public function setFax($fax)
    {
        return $this;
    }

    public function getPostcode()
    {
        return null;
    }

    public function setPostcode($postcode)
    {
        return $this;
    }

    public function getCity()
    {
        return null;
    }

    public function setCity($city)
    {
        return $this;
    }

    public function getFirstname()
    {
        return null;
    }

    public function setFirstname($firstname)
    {
        return $this;
    }

    public function getLastname()
    {
        return null;
    }

    public function setLastname($lastname)
    {
        return $this;
    }

    public function getMiddlename()
    {
        return null;
    }

    public function setMiddlename($middlename)
    {
        return $this;
    }

    public function getPrefix()
    {
        return null;
    }

    public function setPrefix($prefix)
    {
        return $this;
    }

    public function getSuffix()
    {
        return null;
    }

    public function setSuffix($suffix)
    {
        return $this;
    }

    public function getVatId()
    {
        return null;
    }

    public function setVatId($vatId)
    {
        return $this;
    }

    public function isDefaultShipping()
    {
        return null;
    }

    public function setIsDefaultShipping($isDefaultShipping)
    {
        return $this;
    }

    public function isDefaultBilling()
    {
        return null;
    }

    public function setIsDefaultBilling($isDefaultBilling)
    {
        return $this;
    }

    public function getExtensionAttributes()
    {
        return null;
    }

    public function setExtensionAttributes(\Magento\Customer\Api\Data\AddressExtensionInterface $extensionAttributes)
    {
        return $this;
    }

    public function getCustomAttributes()
    {
        return null;
    }

    public function setCustomAttributes(array $attributes)
    {
        return $this;
    }

    public function getCustomAttribute($attributeCode)
    {
        return null;
    }

    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        return $this;
    }
}

