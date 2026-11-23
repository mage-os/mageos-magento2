<?php

declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Model\Plugin\Frontend;

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\ConfigurableProduct\Model\Plugin\Frontend\UsedProductsCache;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Session;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UsedProductsAttributeCacheTest extends TestCase
{
    private const ATTRIBUTE_CACHE = '_cache_instance_product_set_attributes';

    /** @var FrontendInterface&MockObject */
    private FrontendInterface $cache;
    /** @var ProductInterfaceFactory&MockObject */
    private ProductInterfaceFactory $factory;
    /** @var UsedProductsCache */
    private UsedProductsCache $plugin;
    /** @var Product */
    private Product $parent;
    /** @var Configurable */
    private Configurable $type;

    protected function setUp(): void
    {
        $metadata = $this->createStub(EntityMetadataInterface::class);
        $metadata->method('getLinkField')->willReturn('entity_id');
        $metadataPool = $this->createStub(MetadataPool::class);
        $metadataPool->method('getMetadata')->willReturn($metadata);
        $session = $this->createStub(Session::class);
        $session->method('getCustomerGroupId')->willReturn(0);
        $this->cache = $this->createMock(FrontendInterface::class);
        $this->factory = $this->createMock(ProductInterfaceFactory::class);
        $this->parent = $this->product(['entity_id' => 100, 'store_id' => 1]);
        $this->type = $this->createStub(Configurable::class);
        $this->plugin = new UsedProductsCache(
            $metadataPool,
            $this->cache,
            new Json(),
            $this->factory,
            $session,
            3600
        );
    }

    public function testCacheMissOmitsAttributeObjectsAndPreservesProductData(): void
    {
        $attribute = $this->createStub(Attribute::class);
        $attribute->method('getAttributeCode')->willReturn('color');
        $data = self::variantData();
        $data[self::ATTRIBUTE_CACHE] = ['color' => $attribute];
        $child = $this->product($data);
        $this->cache->expects(self::once())->method('load')->willReturn(false);
        $this->factory->expects(self::never())->method('create');
        $this->cache->expects(self::once())->method('save')->willReturnCallback(
            static function (string $json, string $key, array $tags, int $lifetime): bool {
                self::assertSame([self::variantData()], (new Json())->unserialize($json));
                self::assertSame(sha1('getUsedProducts_100_1_0'), $key);
                self::assertSame(['cat_p_100', 'cat_c', 'cat_p', 'price', 'configurable_100'], $tags);
                self::assertSame(3600, $lifetime);
                return true;
            }
        );

        $children = $this->plugin->aroundGetUsedProducts(
            $this->type,
            static fn (): array => [$child],
            $this->parent
        );

        self::assertSame([$child], $children);
        self::assertSame(['color' => $attribute], $child->getData(self::ATTRIBUTE_CACHE));
        self::assertSame('color', $attribute->getAttributeCode());
    }

    #[DataProvider('cachedAttributeStates')]
    public function testCacheHitRecoversLegacyPayloadsWithoutDroppingVariantData(array $extra): void
    {
        $cachedData = array_replace(self::variantData(), $extra);
        $this->cache->expects(self::once())->method('load')
            ->willReturn((new Json())->serialize([$cachedData]));
        $this->cache->expects(self::never())->method('save');
        $restored = $this->createMock(Product::class);
        $restored->expects(self::once())->method('setData')->with(self::variantData())->willReturnSelf();
        $this->factory->expects(self::once())->method('create')->willReturn($restored);

        $children = $this->plugin->aroundGetUsedProducts(
            $this->type,
            static function (): never {
                throw new \LogicException('A cache hit must not reload the collection');
            },
            $this->parent
        );

        self::assertSame([$restored], $children);
    }

    public static function cachedAttributeStates(): array
    {
        return [
            'legacy attribute arrays' => [[self::ATTRIBUTE_CACHE => ['color' => ['attribute_code' => 'color']]]],
            'legacy empty attribute objects' => [[self::ATTRIBUTE_CACHE => ['color' => []]]],
            'legacy empty internal cache' => [[self::ATTRIBUTE_CACHE => []]],
            'clean payload' => [[]]
        ];
    }

    private function product(array $data): Product
    {
        $product = $this->createStub(Product::class);
        $product->method('getData')->willReturnCallback(
            static fn ($key = '') => $key === '' ? $data : ($data[$key] ?? null)
        );
        $product->method('getStoreId')->willReturn($data['store_id'] ?? 1);
        $product->method('getIdentities')->willReturn(['cat_p_100']);
        return $product;
    }

    private static function variantData(): array
    {
        return [
            'entity_id' => 101,
            'sku' => 'variant-blue',
            'store_id' => 1,
            'name' => 'Blue variant',
            'price' => '12.50',
            'color' => '42',
            'bar_code' => '0123456789012',
            'media_gallery' => ['images' => [['file' => '/b/l/blue.jpg']]],
            'tier_price' => [['price_qty' => '5', 'price' => '10.00']]
        ];
    }
}
