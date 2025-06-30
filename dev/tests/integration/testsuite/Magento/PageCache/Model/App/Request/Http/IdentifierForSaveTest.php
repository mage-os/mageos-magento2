<?php
/**
 * Copyright 2025 Adobe
 * All rights reserved.
 */
declare(strict_types=1);

namespace Magento\PageCache\Model\App\Request\Http;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Test\Fixture\Group as StoreGroupFixture;
use Magento\Store\Test\Fixture\Store as StoreFixture;
use Magento\Store\Test\Fixture\Website as WebsiteFixture;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Customer\Test\Fixture\Customer as CustomerFixture;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for \Magento\PageCache\Model\App\Request\Http\IdentifierForSave
 */
class IdentifierForSaveTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @var IdentifierForSave
     */
    private $identifierForSave;

    /**
     * @var DataFixtureStorage
     */
    private $fixtures;

    /*
     * @var Context
     */
    private $context;

    /*
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->request = $this->objectManager->get(HttpRequest::class);
        $this->identifierForSave = $this->objectManager->get(IdentifierForSave::class);
        $this->fixtures = $this->objectManager->get(DataFixtureStorageManager::class)->getStorage();
        $this->context = $this->objectManager->get(Context::class);
        $this->cookieManager = $this->objectManager->get(CookieManagerInterface::class);
        $this->cookieMetadataFactory = $this->objectManager->get(CookieMetadataFactory::class);
    }

    /**
     * Test that cache identifier properly handles logged-in customers
     */
    #[
        DbIsolation(false),
        ConfigFixture('system/full_page_cache/caching_application', '1', 'store'),
        ConfigFixture('system/full_page_cache/enabled', '1', 'store'),
        DataFixture(WebsiteFixture::class, as: 'website'),
        DataFixture(StoreGroupFixture::class, ['website_id' => '$website.id$'], 'store_group'),
        DataFixture(StoreFixture::class, ['store_group_id' => '$store_group.id$'], 'store'),
        DataFixture(CustomerFixture::class, as: 'customer')
    ]
    public function testAfterGetValueWithLoggedInCustomer()
    {
        $storeCode = $this->fixtures->get('store')->getCode();
        $serverParams = [
            StoreManager::PARAM_RUN_TYPE => 'store',
            StoreManager::PARAM_RUN_CODE => $storeCode
        ];
        $this->request->setServer(new \Laminas\Stdlib\Parameters($serverParams));

        // Get customer and login
        $customer = $this->fixtures->get('customer');
        $customerSession = $this->objectManager->get(Session::class);
        $customerSession->loginById($customer->getId());

        // Get cache identifiers
        $result = $this->identifierForSave->getValue();

        // Verify that both cache keys are not empty and contain customer context
        $this->assertNotEmpty($result, 'Cache identifier for save should not be empty for logged-in user');

        // Test scenario: Simulate context vary string being empty but cookie vary string present
        // Get the current vary string from context
        $originalVaryString = $this->context->getVaryString();
        $this->assertNotEmpty($originalVaryString, 'Context vary string should not be empty for logged-in user');

        // Set the vary cookie to simulate a previous request
        $cookieMetadata = $this->cookieMetadataFactory->createSensitiveCookieMetadata()->setPath('/');
        $this->cookieManager->setSensitiveCookie(
            HttpResponse::COOKIE_VARY_STRING,
            $originalVaryString,
            $cookieMetadata
        );

        // Clear the context vary string to simulate depersonalization
        $this->context->_resetState();

        // Verify context vary string is now empty
        $this->assertEmpty($this->context->getVaryString(), 'Context vary string should be empty after reset');

        // Get cache identifiers again - should still work due to cookie fallback
        $resultWithEmptyContext = $this->identifierForSave->getValue();

        // Both should still generate valid cache keys due to cookie fallback
        $this->assertNotEmpty(
            $resultWithEmptyContext,
            'Cache identifier for save should work with empty context due to cookie fallback'
        );

        // Both cache key should be same even after context vary string is empty because it use cookie vary string
        $this->assertEquals($result, $resultWithEmptyContext);

        // Clean up
        $this->cookieManager->deleteCookie(
            HttpResponse::COOKIE_VARY_STRING,
            $cookieMetadata
        );
    }
}
