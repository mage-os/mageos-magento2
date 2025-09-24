<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Category\Plugin;

use Magento\Catalog\Block\Category\Plugin\PriceBoxTags;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render\PriceBox;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Tax\Model\Calculation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PriceBoxTagsTest extends TestCase
{
    /**
     * @var PriceCurrencyInterface|MockObject
     */
    private $priceCurrencyInterface;

    /**
     * @var Currency|MockObject
     */
    private $currency;

    /**
     * @var TimezoneInterface|MockObject
     */
    private $timezoneInterface;

    /**
     * @var ScopeResolverInterface|MockObject
     */
    private $scopeResolverInterface;

    /**
     * @var Session|MockObject
     */
    private $session;

    /**
     * @var Calculation|MockObject
     */
    private $taxCalculation;

    /**
     * @var PriceBoxTags
     */
    private $priceBoxTags;

    protected function setUp(): void
    {
        $this->priceCurrencyInterface = $this->getMockBuilder(
            PriceCurrencyInterface::class
        )->getMock();
        $this->currency = $this->getMockBuilder(Currency::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->timezoneInterface = $this->getMockBuilder(
            TimezoneInterface::class
        )->getMock();
        $this->scopeResolverInterface = $this->getMockBuilder(
            ScopeResolverInterface::class
        )
            ->getMock();
        $this->session = new class extends Session {
            public function __construct()
            {
                // Empty constructor
            }
            public function getDefaultTaxBillingAddress()
            {
                return ['billing_address'];
            }
            public function getDefaultTaxShippingAddress()
            {
                return ['shipping_address'];
            }
            public function getCustomerTaxClassId()
            {
                return 3;
            }
            public function getCustomerGroupId()
            {
                return 2;
            }
            public function getCustomerId()
            {
                return 4;
            }
        };
        $this->taxCalculation = $this->getMockBuilder(Calculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $objectManager = new ObjectManager($this);
        $this->priceBoxTags = $objectManager->getObject(
            PriceBoxTags::class,
            [
                'priceCurrency' => $this->priceCurrencyInterface,
                'dateTime' => $this->timezoneInterface,
                'scopeResolver' => $this->scopeResolverInterface,
                'customerSession' => $this->session,
                'taxCalculation' => $this->taxCalculation
            ]
        );
    }

    public function testAfterGetCacheKey()
    {
        $date = date('Ymd');
        $currencyCode = 'USD';
        $result = 'result_string';
        $billingAddress = ['billing_address'];
        $shippingAddress = ['shipping_address'];
        $scopeId = 1;
        $customerGroupId = 2;
        $customerTaxClassId = 3;
        $customerId = 4;
        $rateIds = [5,6];
        $expected = implode(
            '-',
            [
                $result,
                $currencyCode,
                $date,
                $scopeId,
                $customerGroupId,
                implode('_', $rateIds)
            ]
        );

        $priceBox = $this->getMockBuilder(PriceBox::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->priceCurrencyInterface->expects($this->once())->method('getCurrency')->willReturn($this->currency);
        $this->currency->expects($this->once())->method('getCode')->willReturn($currencyCode);
        $scope = $this->getMockBuilder(ScopeInterface::class)
            ->getMock();
        $this->scopeResolverInterface->method('getScope')->willReturn($scope);
        $scope->method('getId')->willReturn($scopeId);
        $dateTime = $this->getMockBuilder(\DateTime::class)->getMock();
        $this->timezoneInterface->expects($this->any())->method('scopeDate')->with($scopeId)->willReturn($dateTime);
        $dateTime->expects($this->any())->method('format')->with('Ymd')->willReturn($date);
        // $this->session->expects($this->once())->method('getCustomerGroupId')->willReturn($customerGroupId);
        // $this->session->expects($this->once())->method('getDefaultTaxBillingAddress')->willReturn($billingAddress);
        // $this->session->expects($this->once())->method('getDefaultTaxShippingAddress')->willReturn($shippingAddress);
        // $this->session->expects($this->once())->method('getCustomerTaxClassId')
            // ->willReturn($customerTaxClassId);
        // $this->session->expects($this->once())->method('getCustomerId')->willReturn($customerId);
        $rateRequest = $this->getMockBuilder(DataObject::class)
            ->getMock();
        $this->taxCalculation->expects($this->once())->method('getRateRequest')->with(
            new DataObject($shippingAddress),
            new DataObject($billingAddress),
            $customerTaxClassId,
            $scopeId,
            $customerId
        )->willReturn($rateRequest);
        $salableInterface = new class implements SaleableInterface {
            public function getTaxClassId()
            {
                return null;
            }
            
            // Required SaleableInterface methods
            public function getPriceInfo()
            {
                return null;
            }
            public function getTypeId()
            {
                return null;
            }
            public function getId()
            {
                return null;
            }
            public function getQty()
            {
                return 1.0;
            }
        };
        $priceBox->expects($this->once())->method('getSaleableItem')->willReturn($salableInterface);
        // $salableInterface->expects($this->once())->method('getTaxClassId')->willReturn($customerTaxClassId);
        $resource = new class extends AbstractResource {
            public function __construct()
            {
 /* Empty constructor */
            }
            public function getRateIds($rateRequest)
            {
                return [5,6];
            }
            
            // Required abstract methods from AbstractResource
            protected function _construct()
            {
 /* Empty implementation */
            }
            public function getConnection()
            {
                return null;
            }
        };
        $this->taxCalculation->expects($this->once())->method('getResource')->willReturn($resource);
        // $resource->expects($this->once())->method('getRateIds')->with($rateRequest)->willReturn($rateIds);

        $this->assertEquals($expected, $this->priceBoxTags->afterGetCacheKey($priceBox, $result));
    }
}
