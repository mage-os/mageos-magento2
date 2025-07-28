<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Downloadable\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Test to verify REST-API updating product stock_item does not delete downloadable_product_links
 *
 * @magentoAppIsolation enabled
 */
class StockItemUpdatePreservesLinksTest extends WebapiAbstract
{
    private const ADMIN_TOKEN_RESOURCE_PATH = '/V1/integration/admin/token';
    private const PRODUCT_RESOURCE_PATH = '/V1/products';
    private const TEST_PRODUCT_SKU = 'downloadable-product';

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->_markTestAsRestOnly();
    }

    /**
     * Test the complete workflow from Steps 1-16
     * Verify that REST-API updating product stock_item does not delete downloadable_product_links
     *
     * @magentoApiDataFixture Magento/Webapi/_files/webapi_user.php
     * @magentoApiDataFixture Magento/Downloadable/_files/downloadable_product_with_files_and_sample_url.php
     */
    public function testStockItemUpdatePreservesDownloadableLinks()
    {
        // Steps 1-7: Generate admin access token
        $adminToken = $this->generateAdminAccessToken();

        // Get original product and verify it has downloadable links
        $originalProduct = $this->getProductBySku(self::TEST_PRODUCT_SKU);
        $this->verifyProductHasDownloadableLinks($originalProduct, 'Original product should have downloadable links');
        $originalLinks = $originalProduct['extension_attributes']['downloadable_product_links'];

        // Steps 8-14: Update product stock_item via catalogProductRepositoryV1 PUT endpoint
        $updatedProduct = $this->updateProductStockItem($adminToken);

        // Verify the API call was successful (Step 14: Server response Code=200)
        $this->assertNotEmpty($updatedProduct, 'API response should not be empty');
        $this->assertEquals(self::TEST_PRODUCT_SKU, $updatedProduct['sku']);
        $this->assertEquals('99.99', $updatedProduct['price']);
        $this->assertEquals('1', $updatedProduct['status']);

        // Steps 15-16: Verify downloadable product links are preserved
        $this->verifyDownloadableLinksPreserved($originalLinks);
    }

    /**
     * Generate Admin Access Token
     */
    private function generateAdminAccessToken(): string
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::ADMIN_TOKEN_RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
        ];

        $requestData = [
            'username' => 'webapi_user',
            'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
        ];

        $accessToken = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertNotEmpty($accessToken, 'Admin access token should be generated');
        $this->assertIsString($accessToken, 'Access token should be a string');

        return $accessToken;
    }

    /**
     * Update Product Stock Item
     */
    private function updateProductStockItem(string $adminToken): array
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::PRODUCT_RESOURCE_PATH . '/' . self::TEST_PRODUCT_SKU,
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $adminToken,
            ],
        ];

        $productData = [
            'product' => [
                'sku' => self::TEST_PRODUCT_SKU,
                'status' => '1',
                'price' => '99.99',
                'type_id' => 'downloadable',
                'extension_attributes' => [
                    'stock_item' => [
                        'qty' => 1
                    ]
                ]
            ]
        ];

        return $this->_webApiCall($serviceInfo, $productData);
    }

    /**
     * Verify Downloadable Links are Preserved
     */
    private function verifyDownloadableLinksPreserved(array $originalLinks): void
    {
        $updatedProduct = $this->getProductBySku(self::TEST_PRODUCT_SKU);
        $this->verifyProductHasDownloadableLinks($updatedProduct, 'Updated product should preserve downloadable links');

        $preservedLinks = $updatedProduct['extension_attributes']['downloadable_product_links'];

        $this->assertCount(
            count($originalLinks),
            $preservedLinks,
            'Number of downloadable links should be preserved after stock_item update'
        );

        foreach ($preservedLinks as $link) {
            $this->assertArrayHasKey('id', $link, 'Link should have an ID');
            $this->assertArrayHasKey('title', $link, 'Link should have a title');
            $this->assertArrayHasKey('price', $link, 'Link should have a price');
            $this->assertArrayHasKey('sort_order', $link, 'Link should have a sort order');
        }

        $this->assertGreaterThan(0, count($preservedLinks), 'Should have at least one downloadable link preserved');

        $linkTitles = array_column($preservedLinks, 'title');
        $this->assertContains('Downloadable Product Link', $linkTitles,
            'Downloadable product link should be preserved');
    }

    /**
     * Verify product has downloadable links
     */
    private function verifyProductHasDownloadableLinks(array $product, string $message): void
    {
        $this->assertArrayHasKey('extension_attributes', $product, $message . ' - missing extension_attributes');
        $this->assertArrayHasKey(
            'downloadable_product_links',
            $product['extension_attributes'],
            $message . ' - missing downloadable_product_links'
        );
        $this->assertNotEmpty(
            $product['extension_attributes']['downloadable_product_links'],
            $message . ' - downloadable_product_links should not be empty'
        );
    }

    /**
     * Get product by SKU
     */
    private function getProductBySku(string $sku): array
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::PRODUCT_RESOURCE_PATH . '/' . $sku,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
        ];

        return $this->_webApiCall($serviceInfo, []);
    }

    /**
     * Test Steps 1-7: Admin Token Generation
     *
     * @magentoApiDataFixture Magento/Webapi/_files/webapi_user.php
     */
    public function testSteps1Through7AdminTokenGeneration()
    {
        $token = $this->generateAdminAccessToken();
        $this->assertNotEmpty($token, 'Steps 1-7: Admin token should be generated successfully');
    }

    /**
     * Test Steps 8-14: Product Stock Item Update
     *
     * @magentoApiDataFixture Magento/Webapi/_files/webapi_user.php
     * @magentoApiDataFixture Magento/Downloadable/_files/downloadable_product_with_files_and_sample_url.php
     */
    public function testSteps8Through14ProductStockItemUpdate()
    {
        $token = $this->generateAdminAccessToken();
        $updatedProduct = $this->updateProductStockItem($token);

        $this->assertNotEmpty($updatedProduct, 'Steps 8-14: Product should be updated successfully');
        $this->assertEquals('99.99', $updatedProduct['price'], 'Steps 8-14: Product price should be updated to 99.99');
        $this->assertEquals('1', $updatedProduct['status'], 'Steps 8-14: Product status should be enabled');
    }

    /**
     * Test Steps 15-16: Downloadable Links Preservation
     *
     * @magentoApiDataFixture Magento/Webapi/_files/webapi_user.php
     * @magentoApiDataFixture Magento/Downloadable/_files/downloadable_product_with_files_and_sample_url.php
     */
    public function testSteps15Through16DownloadableLinksPreservation()
    {
        $originalProduct = $this->getProductBySku(self::TEST_PRODUCT_SKU);
        $this->verifyProductHasDownloadableLinks($originalProduct, 'Original product should have downloadable links');
        $originalLinks = $originalProduct['extension_attributes']['downloadable_product_links'];

        $token = $this->generateAdminAccessToken();
        $this->updateProductStockItem($token);

        $this->verifyDownloadableLinksPreserved($originalLinks);
    }
}
