<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\ResourceModel\Address\Attribute\Source;

use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\CountryWithWebsites;
use Magento\Customer\Test\Unit\Helper\CountryWithWebsitesTestHelper;
use Magento\Directory\Model\AllowedCountries;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CountryWithWebsitesTest extends TestCase
{
    /**
     * @var CollectionFactory|MockObject
     */
    private $countriesFactoryMock;

    /**
     * @var AllowedCountries|MockObject
     */
    private $allowedCountriesMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var CountryWithWebsites
     */
    private $countryByWebsite;

    /**
     * @var Share|MockObject
     */
    private $shareConfigMock;

    protected function setUp(): void
    {
        $this->countriesFactoryMock = $this->createPartialMock(
            CollectionFactory::class,
            ['create']
        );
        $this->allowedCountriesMock = $this->createMock(AllowedCountries::class);
        $eavCollectionFactoryMock = $this->createMock(
            \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory::class
        );
        $optionsFactoryMock = $this->createMock(OptionFactory::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->shareConfigMock = $this->createMock(Share::class);
        $requestMock = $this->createMock(\Magento\Framework\App\Request\Http::class);
        $customerRepositoryMock = $this->createMock(\Magento\Customer\Api\CustomerRepositoryInterface::class);
        $parentStoreManagerMock = $this->createMock(StoreManagerInterface::class);
        
        $this->countryByWebsite = new CountryWithWebsitesTestHelper(
            $eavCollectionFactoryMock,
            $optionsFactoryMock,
            $this->countriesFactoryMock,
            $this->allowedCountriesMock,
            $this->storeManagerMock,
            $this->shareConfigMock,
            $requestMock,
            $customerRepositoryMock,
            $parentStoreManagerMock
        );
    }

    public function testGetAllOptions()
    {
        $website1 = $this->createMock(WebsiteInterface::class);
        $website2 = $this->createMock(WebsiteInterface::class);

        $website1->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(1);
        $website2->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(2);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->willReturn([$website1, $website2]);
        $collectionMock = $this->createMock(AbstractDb::class);

        $this->allowedCountriesMock->expects($this->exactly(2))
            ->method('getAllowedCountries')
            ->willReturnCallback(
                function ($arg1, $arg2) {
                    if ($arg1 === 'website' && $arg2 === 1) {
                        return ['AM' => 'AM'];
                    } elseif ($arg1 === 'website' && $arg2 === 2) {
                        return ['AM' => 'AM', 'DZ' => 'DZ'];
                    }
                }
            );
        $this->countriesFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);
        $collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with('country_id', ['in' => ['AM' => 'AM', 'DZ' => 'DZ']])
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('toOptionArray')
            ->willReturn([
                ['value' => 'AM', 'label' => 'UZ']
            ]);

        $this->assertEquals([
            ['value' => 'AM', 'label' => 'UZ', 'website_ids' => [1, 2]]
        ], $this->countryByWebsite->getAllOptions());
    }
}
