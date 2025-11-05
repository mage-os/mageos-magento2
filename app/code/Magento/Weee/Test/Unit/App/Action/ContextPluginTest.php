<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\App\Action;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Test\Unit\Action\Stub\ActionStub;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\Calculation\Proxy as TaxCalculation;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Weee\Helper\Data as WeeeHelper;
use Magento\Weee\Model\App\Action\ContextPlugin;
use Magento\Weee\Model\Tax;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit Tests to cover Context Plugin
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class ContextPluginTest extends TestCase
{
    /**
     * @var TaxHelper|MockObject
     */
    protected $taxHelperMock;

    /**
     * @var WeeeHelper|MockObject
     */
    protected $weeeHelperMock;

    /**
     * @var Tax|MockObject
     */
    protected $weeeTaxMock;

    /**
     * @var HttpContext|MockObject
     */
    protected $httpContextMock;

    /**
     * @var TaxCalculation|MockObject
     */
    protected $taxCalculationMock;

    /**
     * @var ModuleManager|MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var PageCacheConfig|MockObject
     */
    protected $cacheConfigMock;

    /**
     * @var StoreManager|MockObject
     */
    protected $storeManagerMock;

    /**
     * @var Config|MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var CustomerSession|MockObject
     */
    private $customerSessionMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ContextPlugin
     */
    protected $contextPlugin;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->taxHelperMock = $this->createMock(TaxHelper::class);

        $this->weeeHelperMock = $this->createMock(WeeeHelper::class);

        $this->weeeTaxMock = $this->createMock(Tax::class);

        $this->httpContextMock = $this->createMock(HttpContext::class);

        $this->customerSessionMock = $this->createCustomerSessionMock();

        $this->moduleManagerMock = $this->createMock(ModuleManager::class);

        $this->cacheConfigMock = $this->createMock(PageCacheConfig::class);

        $this->storeManagerMock = $this->createMock(StoreManager::class);

        $this->scopeConfigMock = $this->createMock(Config::class);

        $this->contextPlugin = $this->objectManager->getObject(
            ContextPlugin::class,
            [
                'customerSession' => $this->customerSessionMock,
                'httpContext' => $this->httpContextMock,
                'weeeTax' => $this->weeeTaxMock,
                'taxHelper' => $this->taxHelperMock,
                'weeeHelper' => $this->weeeHelperMock,
                'moduleManager' => $this->moduleManagerMock,
                'cacheConfig' => $this->cacheConfigMock,
                'storeManager' => $this->storeManagerMock,
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Create customer session mock with all required methods
     *
     * @return CustomerSession
     */
    private function createCustomerSessionMock(): CustomerSession
    {
        $session = $this->createPartialMock(CustomerSession::class, ['isLoggedIn']);
        
        // Initialize storage for magic __call methods
        $reflection = new \ReflectionClass($session);
        $property = $reflection->getProperty('storage');
        $property->setValue($session, new \Magento\Framework\Session\Storage());
        
        // Mock isLoggedIn method
        $session->method('isLoggedIn')->willReturn(true);
        
        return $session;
    }

    /**
     * @return void
     */
    public function testBeforeExecuteBasedOnDefault(): void
    {
        // isLoggedIn is already mocked in createCustomerSessionMock()

        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->willReturn(true);

        $this->cacheConfigMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->weeeHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->taxHelperMock->expects($this->once())
            ->method('getTaxBasedOn')
            ->willReturn('billing');

        $storeMock = $this->createMock(Store::class);

        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->scopeConfigMock
            ->method('getValue')
            ->willReturnCallback(function (...$args) {
                static $index = 0;
                $expectedArgs = [
                    [
                        TaxConfig::CONFIG_XML_PATH_DEFAULT_COUNTRY,
                        ScopeInterface::SCOPE_STORE,
                        null
                    ],
                    [
                        TaxConfig::CONFIG_XML_PATH_DEFAULT_REGION,
                        ScopeInterface::SCOPE_STORE,
                        null
                    ]
                ];
                $returnValue = ['US',0];
                $index++;
                return $args === $expectedArgs[$index - 1] ? $returnValue[$index - 1] : null;
            });

        $this->weeeTaxMock->expects($this->once())
            ->method('isWeeeInLocation')
            ->with('US', 0, 1)
            ->willReturn(true);

        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with('weee_tax_region', ['countryId' => 'US', 'regionId' => 0], 0);

        /** @var ActionStub $action */
        $action = $this->objectManager->getObject(ActionStub::class);

        $this->contextPlugin->beforeExecute($action);
    }

    /**
     * @return void
     */
    public function testBeforeExecuteBasedOnOrigin(): void
    {
        // isLoggedIn is already mocked in createCustomerSessionMock()

        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->willReturn(true);

        $this->cacheConfigMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->weeeHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->taxHelperMock->expects($this->once())
            ->method('getTaxBasedOn')
            ->willReturn('origin');

        /** @var ActionStub $action */
        $action = $this->objectManager->getObject(ActionStub::class);

        $this->contextPlugin->beforeExecute($action);
    }

    /**
     * @return void
     */
    public function testBeforeExecuteBasedOnBilling(): void
    {
        // isLoggedIn is already mocked in createCustomerSessionMock()

        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->willReturn(true);

        $this->cacheConfigMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->weeeHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->taxHelperMock->expects($this->once())
            ->method('getTaxBasedOn')
            ->willReturn('billing');

        $storeMock = $this->createMock(Store::class);

        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->scopeConfigMock
            ->method('getValue')
            ->willReturnCallback(function (...$args) {
                static $index = 0;
                $expectedArgs = [
                    [
                        TaxConfig::CONFIG_XML_PATH_DEFAULT_COUNTRY,
                        ScopeInterface::SCOPE_STORE,
                        null
                    ],
                    [
                        TaxConfig::CONFIG_XML_PATH_DEFAULT_REGION,
                        ScopeInterface::SCOPE_STORE,
                        null
                    ]
                ];
                $returnValue = ['US',0];
                $index++;
                return $args === $expectedArgs[$index - 1] ? $returnValue[$index - 1] : null;
            });

        // Use magic __call method via storage
        $this->customerSessionMock->setData('default_tax_billing_address', ['country_id' => 'US', 'region_id' => 1]);

        $this->weeeTaxMock->expects($this->once())
            ->method('isWeeeInLocation')
            ->with('US', 1, 1)
            ->willReturn(true);

        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with('weee_tax_region', ['countryId' => 'US', 'regionId' => 1], 0);

        /** @var ActionStub $action */
        $action = $this->objectManager->getObject(ActionStub::class);

        $this->contextPlugin->beforeExecute($action);
    }

    /**
     * @return void
     */
    public function testBeforeExecuterBasedOnShipping(): void
    {
        // isLoggedIn is already mocked in createCustomerSessionMock()

        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->willReturn(true);

        $this->cacheConfigMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->weeeHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->taxHelperMock->expects($this->once())
            ->method('getTaxBasedOn')
            ->willReturn('shipping');

        $storeMock = $this->createMock(Store::class);

        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->scopeConfigMock
            ->method('getValue')
            ->willReturnCallback(function (...$args) {
                static $index = 0;
                $expectedArgs = [
                    [
                        TaxConfig::CONFIG_XML_PATH_DEFAULT_COUNTRY,
                        ScopeInterface::SCOPE_STORE,
                        null
                    ],
                    [
                        TaxConfig::CONFIG_XML_PATH_DEFAULT_REGION,
                        ScopeInterface::SCOPE_STORE,
                        null
                    ]
                ];
                $returnValue = ['US',0];
                $index++;
                return $args === $expectedArgs[$index - 1] ? $returnValue[$index - 1] : null;
            });

        // Use magic __call method via storage
        $this->customerSessionMock->setData('default_tax_shipping_address', ['country_id' => 'US', 'region_id' => 1]);

        $this->weeeTaxMock->expects($this->once())
            ->method('isWeeeInLocation')
            ->with('US', 1, 1)
            ->willReturn(true);

        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with('weee_tax_region', ['countryId' => 'US', 'regionId' => 1], 0);

        /** @var ActionStub $action */
        $action = $this->objectManager->getObject(ActionStub::class);

        $this->contextPlugin->beforeExecute($action);
    }
}
