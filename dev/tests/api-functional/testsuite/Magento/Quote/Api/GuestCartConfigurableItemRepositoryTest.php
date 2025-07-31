<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Quote\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\ConfigurableProduct\Test\Fixture\Attribute as AttributeFixture;
use Magento\ConfigurableProduct\Test\Fixture\Product as ConfigurableProductFixture;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\Integration\Model\AdminTokenService;
use Magento\Integration\Model\Oauth\Token as TokenModel;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
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
     * @var DataFixtureStorage
     */
    private $fixtures;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = Bootstrap::getObjectManager()->get(AdminTokenService::class);
        $this->tokenModel = Bootstrap::getObjectManager()->get(TokenModel::class);
        $this->userModel = Bootstrap::getObjectManager()->get(User::class);
        $this->fixtures = Bootstrap::getObjectManager()->get(DataFixtureStorageManager::class)->getStorage();
    }

    /**
     * Test guest cart update configurable item using modern fixtures
     */
    #[
        DataFixture(ProductFixture::class, ['price' => 10, 'sku' => 'simple-10'], as: 'p1'),
        DataFixture(ProductFixture::class, ['price' => 20, 'sku' => 'simple-20'], as: 'p2'),
        DataFixture(AttributeFixture::class, ['attribute_code' => 'test_configurable'], as: 'attr'),
        DataFixture(
            ConfigurableProductFixture::class,
            [
                'sku' => 'configurable',
                'name' => 'Configurable Product',
                '_options' => ['$attr$'],
                '_links' => ['$p1$', '$p2$']
            ],
            'configurableProduct'
        )
    ]
    public function testGuestCartUpdateConfigurableItem()
    {
        $guestCartId = $this->createGuestCart();
        $response = $this->addConfigurableProductToCart($guestCartId);
        $this->updateConfigurableProductInCart($guestCartId, $response['item_id']);
        $this->verifyCartItems($guestCartId, $response['item_id']);
    }

    /**
     * @return string
     */
    private function createGuestCart(): string
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH_GUEST_CART,
                'httpMethod' => Request::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME_GUEST_CART,
                'serviceVersion' => self::SERVICE_VERSION_GUEST_CART,
                'operation' => self::SERVICE_NAME_GUEST_CART . 'Save',
            ],
        ];

        $requestData = ['storeId' => 1];
        $quoteId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue(strlen($quoteId) >= 32);
        return $quoteId;
    }

    /**
     * @param string $guestCartId
     * @return array
     */
    private function addConfigurableProductToCart(string $guestCartId): array
    {
        $configurableProduct = $this->getConfigurableProduct();
        $optionData = $this->getConfigurableOptionData($configurableProduct);

        $requestData = $this->buildCartItemRequestData(
            $guestCartId,
            $configurableProduct->getSku(),
            $optionData['attribute_id'],
            $optionData['option_id']
        );

        $serviceInfo = $this->getCartServiceInfo($guestCartId, 'add');

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

    /**
     * @param string $guestCartId
     * @param int $expectedItemId
     * @return void
     */
    private function verifyCartItems(string $guestCartId, int $expectedItemId): void
    {
        $serviceInfo = $this->getCartServiceInfo($guestCartId, 'get');
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

    /**
     * @param string $guestCartId
     * @param int $itemId
     * @return void
     */
    private function updateConfigurableProductInCart(string $guestCartId, int $itemId): void
    {
        $configurableProduct = $this->getConfigurableProduct();
        $optionData = $this->getConfigurableOptionData($configurableProduct);
        $requestData = $this->buildCartItemRequestData(
            $guestCartId,
            $configurableProduct->getSku(),
            $optionData['attribute_id'],
            $optionData['option_id']
        );
        $requestData['cartItem']['item_id'] = $itemId;
        $serviceInfo = $this->getCartServiceInfo($guestCartId, 'update', $itemId);
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

    /**
     * Get configurable product from fixtures
     *
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    private function getConfigurableProduct(): ProductInterface
    {
        $configurableProduct = $this->fixtures->get('configurableProduct');
        $simpleProducts = $configurableProduct->getTypeInstance()->getUsedProducts($configurableProduct);
        foreach ($simpleProducts as $simpleProduct) {
            $this->simpleProductSkus[] = $simpleProduct->getSku();
        }
        return $configurableProduct;
    }

    /**
     * @param $configurableProduct
     * @param $selectedOption
     * @return array
     */
    private function getConfigurableOptionData($configurableProduct, $selectedOption = null): array
    {
        $configOptions = $configurableProduct->getExtensionAttributes()->getConfigurableProductOptions();

        $options = $configOptions[0]->getOptions();
        $optionKey = (isset($selectedOption) && isset($options[$selectedOption])) ? $selectedOption : 0;

        return [
            'attribute_id' => $configOptions[0]->getAttributeId(),
            'option_id' => $options[$optionKey]['value_index']
        ];
    }

    /**
     * @param string $cartId
     * @param string $sku
     * @param string $attributeId
     * @param string $optionId
     * @return array[]
     */
    private function buildCartItemRequestData(string $cartId, string $sku, string $attributeId, string $optionId): array
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

    /**
     * @param string $cartId
     * @param string $action
     * @param int|null $itemId
     * @return array[]
     */
    private function getCartServiceInfo(
        string $cartId,
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
                'httpMethod' => $httpMethod
            ],
            'soap' => [
                'service' => self::SERVICE_NAME_GUEST_CART,
                'serviceVersion' => self::SERVICE_VERSION_GUEST_CART,
                'operation' => self::SERVICE_NAME_GUEST_CART . 'Save',
            ],
        ];
    }
}
