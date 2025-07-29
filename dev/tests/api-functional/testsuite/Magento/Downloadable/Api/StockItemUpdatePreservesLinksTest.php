<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Downloadable\Api;

use Magento\Downloadable\Test\Fixture\DownloadableProduct;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\User\Test\Fixture\User;

/**
 * Test to verify REST-API updating product stock_item does not delete downloadable_product_links
 *
 * @magentoAppIsolation enabled
 */
class StockItemUpdatePreservesLinksTest extends WebapiAbstract
{
    private const PRODUCT_RESOURCE_PATH = '/V1/products';

    /**
     * @var DataFixtureStorage
     */
    private $fixtures;

    /**
     * @var AdminTokenServiceInterface
     */
    private $adminTokenService;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->_markTestAsRestOnly();
        $this->fixtures = DataFixtureStorageManager::getStorage();
        $this->adminTokenService = Bootstrap::getObjectManager()->get(AdminTokenServiceInterface::class);
    }

    /**
     * Test the complete workflow from Steps 1-16
     * Verify that REST-API updating product stock_item does not delete downloadable_product_links
     */
    #[DataFixture(User::class, ['role_id' => 1], 'admin_user')]
    #[DataFixture(DownloadableProduct::class, [
        'sku' => 'downloadable-product',
        'name' => 'Downloadable Product Test',
        'price' => 50.00,
        'type_id' => 'downloadable',
        'links_purchased_separately' => 1,
        'links_title' => 'Downloadable Links',
        'extension_attributes' => [
            'website_ids' => [1],
            'stock_item' => [
                'use_config_manage_stock' => true,
                'qty' => 100,
                'is_qty_decimal' => false,
                'is_in_stock' => true,
            ],
            'downloadable_product_links' => [
                [
                    'title' => 'Downloadable Product Link',
                    'price' => 10.00,
                    'link_type' => 'url',
                    'is_shareable' => 0,
                    'number_of_downloads' => 5,
                    'sort_order' => 1
                ],
                [
                    'title' => 'Another Link',
                    'price' => 15.00,
                    'link_type' => 'file',
                    'link_file' => 'test-file.txt',
                    'is_shareable' => 1,
                    'number_of_downloads' => 10,
                    'sort_order' => 2
                ]
            ]
        ]
    ], 'downloadable_product')]
    public function testStockItemUpdatePreservesDownloadableLinks()
    {
        // Steps 1-7: Generate admin access token using AdminTokenService directly
        $adminToken = $this->generateAdminAccessToken();

        // Get the product SKU from the fixture
        $product = $this->fixtures->get('downloadable_product');
        $productSku = $product->getSku();

        // Get original product and verify it has downloadable links
        $originalProduct = $this->getProductBySku($productSku);
        $this->verifyProductHasDownloadableLinks($originalProduct, 'Original product should have downloadable links');
        $originalLinks = $originalProduct['extension_attributes']['downloadable_product_links'];

        // Steps 8-14: Update product stock_item via catalogProductRepositoryV1 PUT endpoint
        $updatedProduct = $this->updateProductStockItem($adminToken, $productSku);

        // Verify the API call was successful (Step 14: Server response Code=200)
        $this->assertNotEmpty($updatedProduct, 'API response should not be empty');
        $this->assertEquals($productSku, $updatedProduct['sku']);
        $this->assertEquals('99.99', $updatedProduct['price']);
        $this->assertEquals('1', $updatedProduct['status']);

        // Steps 15-16: Verify downloadable product links are preserved
        $this->verifyDownloadableLinksPreserved($originalLinks, $productSku);
    }

    /**
     * Generate Admin Access Token using AdminTokenService directly
     */
    private function generateAdminAccessToken(): string
    {
        $adminUser = $this->fixtures->get('admin_user');
        
        // Use AdminTokenService directly to bypass TwoFactorAuth issues
        $accessToken = $this->adminTokenService->createAdminAccessToken(
            $adminUser->getUsername(),
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );

        $this->assertNotEmpty($accessToken, 'Admin access token should be generated');
        $this->assertIsString($accessToken, 'Access token should be a string');

        return $accessToken;
    }

    /**
     * Update Product Stock Item
     */
    private function updateProductStockItem(string $adminToken, string $productSku): array
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::PRODUCT_RESOURCE_PATH . '/' . $productSku,
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $adminToken,
            ],
        ];

        $productData = [
            'product' => [
                'sku' => $productSku,
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
    private function verifyDownloadableLinksPreserved(array $originalLinks, string $productSku): void
    {
        $updatedProduct = $this->getProductBySku($productSku);
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
        $this->assertContains(
            'Downloadable Product Link',
            $linkTitles,
            'Downloadable product link should be preserved'
        );
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
}
