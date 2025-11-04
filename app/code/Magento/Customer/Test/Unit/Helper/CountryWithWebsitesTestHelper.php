<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\CountryWithWebsites;
use Magento\Directory\Model\AllowedCountries;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as OptionCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Test helper for CountryWithWebsites to properly mock parent constructor dependencies
 */
class CountryWithWebsitesTestHelper extends CountryWithWebsites
{
    /**
     * @param OptionCollectionFactory $attrOptionCollectionFactory
     * @param OptionFactory $attrOptionFactory
     * @param CollectionFactory $countriesFactory
     * @param AllowedCountries $allowedCountriesReader
     * @param StoreManagerInterface $storeManager
     * @param Share $shareConfig
     * @param Http|null $request
     * @param CustomerRepositoryInterface|null $customerRepository
     * @param StoreManagerInterface|null $parentStoreManager
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        OptionCollectionFactory $attrOptionCollectionFactory,
        OptionFactory $attrOptionFactory,
        CollectionFactory $countriesFactory,
        AllowedCountries $allowedCountriesReader,
        StoreManagerInterface $storeManager,
        Share $shareConfig,
        ?Http $request = null,
        ?CustomerRepositoryInterface $customerRepository = null,
        ?StoreManagerInterface $parentStoreManager = null
    ) {
        // Set properties directly without calling parent constructor
        // to avoid ObjectManager usage in the parent Table class
        
        // Parent Table class properties (protected)
        $this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->_attrOptionFactory = $attrOptionFactory;
        
        // Use reflection to set private properties in parent classes
        $this->setPrivateProperty(
            'storeManager',
            $parentStoreManager ?? $storeManager,
            'Magento\Eav\Model\Entity\Attribute\Source\Table'
        );
        $this->setPrivateProperty('countriesFactory', $countriesFactory, CountryWithWebsites::class);
        $this->setPrivateProperty('allowedCountriesReader', $allowedCountriesReader, CountryWithWebsites::class);
        $this->setPrivateProperty('storeManager', $storeManager, CountryWithWebsites::class);
        $this->setPrivateProperty('shareConfig', $shareConfig, CountryWithWebsites::class);
        $this->setPrivateProperty('request', $request, CountryWithWebsites::class);
        $this->setPrivateProperty('customerRepository', $customerRepository, CountryWithWebsites::class);
    }
    
    /**
     * Set private property using reflection
     *
     * @param string $propertyName
     * @param mixed $value
     * @param string $className
     */
    private function setPrivateProperty(string $propertyName, $value, string $className): void
    {
        $reflection = new \ReflectionClass($className);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this, $value);
    }
}
