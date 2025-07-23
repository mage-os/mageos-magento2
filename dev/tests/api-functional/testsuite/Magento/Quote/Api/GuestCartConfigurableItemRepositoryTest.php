<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Quote\Api;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\Integration\Model\AdminTokenService;
use Magento\Integration\Model\Oauth\Token as TokenModel;
use Magento\TestFramework\Bootstrap as TestBootstrap;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\User\Model\User;
use Magento\User\Model\User as UserModel;

/**
 * Test for Magento\Quote\Api\GuestCartConfigurableItemRepositoryTest.
 */
class GuestCartConfigurableItemRepositoryTest extends WebapiAbstract
{
    private const RESOURCE_PATH_ADMIN_TOKEN = "/V1/integration/admin/token";

    private const RESOURCE_PATH_GUEST_CART = '/V1/guest-carts/';

    private const SERVICE_VERSION_GUEST_CART = 'V1';

    private const SERVICE_NAME_GUEST_CART = 'quoteGuestCartManagementV1';

    /**
     * @var AdminTokenServiceInterface
     */
    private $tokenService;

    /**
     * @var TokenModel
     */
    private $tokenModel;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var array
     */
    private $simpleProductSkus=[];

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = Bootstrap::getObjectManager()->get(AdminTokenService::class);
        $this->tokenModel = Bootstrap::getObjectManager()->get(TokenModel::class);
        $this->userModel = Bootstrap::getObjectManager()->get(User::class);
    }

    /**
     * @magentoApiDataFixture Magento/Webapi/_files/webapi_user.php
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGuestCartUpdateConfigurableItem()
    {
        $adminToken = $this->createAdminAccessToken();
        $guestCartId = $this->createGuestCart($adminToken);
        $response = $this->addConfigurableProductToCart($guestCartId, $adminToken);
        $this->updateConfigurableProductInCart($guestCartId, $adminToken, $response['item_id']);
        $this->verifyCartItems($guestCartId, $adminToken, $response['item_id']);
    }

    private function createAdminAccessToken()
    {
        $adminUser = 'webapi_user';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH_ADMIN_TOKEN,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
        ];
        $requestData = [
            'username' => $adminUser,
            'password' => TestBootstrap::ADMIN_PASSWORD,
        ];
        $accessToken = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($accessToken);
        return $accessToken;
    }

    private function createGuestCart(string $adminToken)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH_GUEST_CART,
                'httpMethod' => Request::HTTP_METHOD_POST,
                'headers' => [
                    'Authorization' => 'Bearer ' . $adminToken
                ]
            ],
            'soap' => [
                'service' => self::SERVICE_NAME_GUEST_CART,
                'serviceVersion' => self::SERVICE_VERSION_GUEST_CART,
                'operation' => self::SERVICE_NAME_GUEST_CART . 'CreateEmptyCart',
            ],
        ];

        $requestData = ['storeId' => 1];
        $quoteId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue(strlen($quoteId) >= 32);
        return $quoteId;
    }

    private function addConfigurableProductToCart(string $guestCartId, string $adminToken)
    {
        $configurableProduct = $this->getConfigurableProduct('configurable');
        $optionData = $this->getConfigurableOptionData($configurableProduct);

        $requestData = $this->buildCartItemRequestData(
            $guestCartId,
            $configurableProduct->getSku(),
            $optionData['attribute_id'],
            $optionData['option_id']
        );

        $serviceInfo = $this->getCartServiceInfo($guestCartId, $adminToken, 'add');

        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($response['item_id']);
        $this->assertEquals(Configurable::TYPE_CODE, $response['product_type']);
        $this->assertEquals($requestData['cartItem']['qty'], $response['qty']);
        $this->assertContains($response['sku'], $this->simpleProductSkus);
        $this->assertEquals(
            $response['product_option']['extension_attributes']['configurable_item_options'][0],
            $requestData['cartItem']['product_option']['extension_attributes']['configurable_item_options'][0]
        );
        return $response;
    }

    private function verifyCartItems(string $guestCartId, string $adminToken, int $expectedItemId)
    {
        $serviceInfo = $this->getCartServiceInfo($guestCartId, $adminToken, 'get');
        $response = $this->_webApiCall($serviceInfo, []);
        $this->assertIsArray($response);
        $this->assertGreaterThan(0, count($response), 'Cart should contain at least one item');

        $foundItem = false;
        foreach ($response as $item) {
            if ($item['item_id'] == $expectedItemId) {
                $foundItem = true;
                $this->assertEquals(Configurable::TYPE_CODE, $item['product_type']);
                $this->assertEquals(1, $item['qty']);
                $this->assertArrayHasKey('product_option', $item);
                $this->assertContains($item['sku'], $this->simpleProductSkus);
                $this->assertArrayHasKey('extension_attributes', $item['product_option']);
                $this->assertArrayHasKey('configurable_item_options', $item['product_option']['extension_attributes']);
                break;
            }
        }

        $this->assertTrue($foundItem, 'Expected cart item not found in cart items list');
    }

    private function updateConfigurableProductInCart(string $guestCartId, string $adminToken, int $itemId)
    {
        $configurableProduct = $this->getConfigurableProduct('configurable');
        $optionData = $this->getConfigurableOptionData($configurableProduct);
        $requestData = $this->buildCartItemRequestData(
            $guestCartId,
            $configurableProduct->getSku(),
            $optionData['attribute_id'],
            $optionData['option_id']
        );
        $requestData['cartItem']['item_id'] = $itemId;
        $serviceInfo = $this->getCartServiceInfo($guestCartId, $adminToken, 'update', $itemId);
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($response['item_id']);
        $this->assertEquals(Configurable::TYPE_CODE, $response['product_type']);
        $this->assertEquals($requestData['cartItem']['qty'], $response['qty']);
        $this->assertContains($response['sku'], $this->simpleProductSkus);
        $this->assertEquals(
            $response['product_option']['extension_attributes']['configurable_item_options'][0],
            $requestData['cartItem']['product_option']['extension_attributes']['configurable_item_options'][0]
        );
    }

    private function getConfigurableProduct(string $sku)
    {
        $productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
        $configurableProduct = $productRepository->get($sku);
        $simpleProducts = $configurableProduct->getTypeInstance()->getUsedProducts($configurableProduct);
        foreach ($simpleProducts as $simpleProduct) {
            $this->simpleProductSkus[] = $simpleProduct->getSku();
        }
        return $configurableProduct;
    }

    private function getConfigurableOptionData($configurableProduct, $selectedOption = null)
    {
        $configOptions = $configurableProduct->getExtensionAttributes()->getconfigOptions();

        $options = $configOptions[0]->getOptions();
        $optionKey = (isset($selectedOption) && isset($options[$selectedOption])) ? $selectedOption : 0;

        return [
            'attribute_id' => $configOptions[0]->getAttributeId(),
            'option_id' => $options[$optionKey]['value_index']
        ];
    }

    private function buildCartItemRequestData(string $cartId, string $sku, int $attributeId, int $optionId): array
    {
        return [
            'cartItem' => [
                'sku' => $sku,
                'qty' => 1,
                'quote_id' => $cartId,
                'product_option' => [
                    'extension_attributes' => [
                        'configurable_item_options' => [
                            [
                                'option_id' => $attributeId,
                                'option_value' => $optionId,
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    private function getCartServiceInfo(
        string $cartId,
        string $adminToken,
        string $action = 'add',
        ?int $itemId = null
    ): array {
        $resourcePath = self::RESOURCE_PATH_GUEST_CART . $cartId . '/items';
        if ($action === 'update' && $itemId !== null) {
            $resourcePath .= '/' . $itemId;
        }

        $httpMethod = Request::HTTP_METHOD_POST;
        if ($action === 'update') {
            $httpMethod = Request::HTTP_METHOD_PUT;
        } elseif ($action === 'get') {
            $httpMethod = Request::HTTP_METHOD_GET;
        }

        return [
            'rest' => [
                'resourcePath' => $resourcePath,
                'httpMethod' => $httpMethod,
                'headers' => [
                    'Authorization' => 'Bearer ' . $adminToken
                ]
            ],
            'soap' => [
                'service' => self::SERVICE_NAME_GUEST_CART,
                'serviceVersion' => self::SERVICE_VERSION_GUEST_CART,
                'operation' => self::SERVICE_NAME_GUEST_CART . 'Save',
            ],
        ];
    }
}
