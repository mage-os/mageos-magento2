<?php
/**
 * Copyright 2011 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute;

use Magento\Backend\Model\Session;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Helper\Product\Edit\Action\Attribute;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\TestFramework\MessageQueue\EnvironmentPreconditionException;
use Magento\TestFramework\MessageQueue\PreconditionFailedException;
use Magento\TestFramework\MessageQueue\PublisherConsumerController;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
 * @magentoAppArea adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveTest extends AbstractBackendController
{
    /** @var PublisherConsumerController */
    private $publisherConsumerController;

    /**
     * @var string[]
     */
    private $consumers = ['product_action_attribute.update'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->publisherConsumerController = $this->_objectManager->create(
            PublisherConsumerController::class,
            ['consumers' => $this->consumers]
        );
        try {
            $this->publisherConsumerController->startConsumers();
        } catch (EnvironmentPreconditionException $e) {
            $this->markTestSkipped($e->getMessage());
        } catch (PreconditionFailedException $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        $this->publisherConsumerController->stopConsumers();
        parent::tearDown();
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     */
    public function testSaveActionRedirectsSuccessfully(): void
    {
        /** @var $session Session */
        $session = $this->_objectManager->get(Session::class);
        $session->setProductIds([1]);
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        /** @var \Magento\Backend\Model\UrlInterface $urlBuilder */
        $urlBuilder = $this->_objectManager->get(UrlInterface::class);

        /** @var Attribute $attributeHelper */
        $attributeHelper = $this->_objectManager->get(Attribute::class);
        $expectedUrl = $urlBuilder->getUrl(
            'catalog/product/index',
            ['store' => $attributeHelper->getSelectedStoreId()]
        );
        $isRedirectPresent = false;
        foreach ($this->getResponse()->getHeaders() as $header) {
            if ($header->getFieldName() === 'Location' && strpos($header->getFieldValue(), $expectedUrl) === 0) {
                $isRedirectPresent = true;
            }
        }

        $this->assertTrue($isRedirectPresent);
    }

    /**
     * @dataProvider saveActionVisibilityAttrDataProvider
     * @param array $attributes
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     */
    public function testSaveActionChangeVisibility(array $attributes): void
    {
        /** @var ProductRepository $repository */
        $repository = $this->_objectManager->create(ProductRepository::class);
        $product = $repository->get('simple');
        $product->setOrigData();
        $product->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
        $product->save();

        /** @var $session Session */
        $session = $this->_objectManager->get(Session::class);
        $session->setProductIds([$product->getId()]);
        $this->getRequest()->setParam('attributes', $attributes);
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        /** @var \Magento\Catalog\Model\Category $category */
        $categoryFactory = $this->_objectManager->get(CategoryFactory::class);
        /** @var ListProduct $listProduct */
        $listProduct = $this->_objectManager->get(ListProduct::class);

        $this->publisherConsumerController->waitForAsynchronousResult(
            fn () => (int) $repository->get('simple', forceReload: true)->getVisibility()
                !== Visibility::VISIBILITY_NOT_VISIBLE
        );

        $category = $categoryFactory->create()->load(2);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();
        $productItem = $productCollection->getFirstItem();
        $this->assertEquals([$product->getId()], [$productItem->getId()]);
        $this->assertEmpty($session->getProductIds());
    }

    /**
     * Data Provider for save with visibility attribute
     *
     * @return array
     */
    public static function saveActionVisibilityAttrDataProvider(): array
    {
        return [
            ['attributes' => ['visibility' => Visibility::VISIBILITY_BOTH]],
            ['attributes' => ['visibility' => Visibility::VISIBILITY_IN_CATALOG]]
        ];
    }

    /**
     * Assert that custom layout update can not be change for existing entity.
     *
     * @return void
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSaveActionCantChangeCustomLayoutUpdate(): void
    {
        /** @var ProductRepository $repository */
        $repository = $this->_objectManager->get(ProductRepository::class);
        $product = $repository->get('simple');

        $product->setOrigData('custom_layout_update', 'test');
        $product->setData('custom_layout_update', 'test');
        $product->save();
        /** @var $session Session */
        $session = $this->_objectManager->get(Session::class);
        $session->setProductIds([$product->getId()]);
        $this->getRequest()->setParam('attributes', ['custom_layout_update' => 'test2']);
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        $this->assertSessionMessages(
            $this->equalTo(['Custom layout update text cannot be changed, only removed']),
            MessageInterface::TYPE_ERROR
        );
        $this->assertEquals('test', $product->getData('custom_layout_update'));
    }

    /**
     * Test that mass update validates special price dates correctly when from_date is after to_date.
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @return void
     */
    public function testSaveActionValidatesSpecialPriceDateRangeWithInvalidDates(): void
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->get(ProductRepositoryInterface::class);
        $product = $productRepository->get('simple');

        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        $session->setProductIds([$product->getId()]);

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue([
            'attributes' => [
                'special_from_date' => '2026-12-31',
                'special_to_date' => '2026-01-01',
            ],
        ]);

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        $this->assertSessionMessages(
            $this->logicalOr(
                $this->containsEqual('Make sure the To Date is later than or the same as the From Date.'),
                $this->containsEqual('Please correct the product special price dates.')
            ),
            MessageInterface::TYPE_ERROR
        );

        // Validation failure prevents async operation, so no need to wait
        $updatedProduct = $productRepository->getById($product->getId());
        $this->assertNull($updatedProduct->getSpecialFromDate());
        $this->assertNull($updatedProduct->getSpecialToDate());
    }

    /**
     * Test that mass update accepts valid special price dates.
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @return void
     */
    public function testSaveActionValidatesSpecialPriceDateRangeWithValidDates(): void
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->get(ProductRepositoryInterface::class);
        $product = $productRepository->get('simple');

        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        $session->setProductIds([$product->getId()]);

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue([
            'attributes' => [
                'special_from_date' => '2026-01-01',
                'special_to_date' => '2026-12-31',
                'special_price' => 5.00,
            ],
        ]);

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        $this->publisherConsumerController->waitForAsynchronousResult(
            fn () => $productRepository->get('simple', forceReload: true)->getSpecialFromDate() !== null
        );

        $this->assertSessionMessages(
            $this->isEmpty(),
            MessageInterface::TYPE_ERROR
        );

        $updatedProduct = $productRepository->get('simple');
        $this->assertNotNull($updatedProduct->getSpecialFromDate());
        $this->assertNotNull($updatedProduct->getSpecialToDate());
        $this->assertEquals(5.00, $updatedProduct->getSpecialPrice());
    }

    /**
     * Test that mass update accepts special price with only from_date set.
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @return void
     */
    public function testSaveActionValidatesSpecialPriceWithOnlyFromDate(): void
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->get(ProductRepositoryInterface::class);
        $product = $productRepository->get('simple');

        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        $session->setProductIds([$product->getId()]);

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue([
            'attributes' => [
                'special_from_date' => '2026-01-01',
                'special_price' => 7.00,
            ],
        ]);

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        $this->publisherConsumerController->waitForAsynchronousResult(
            fn () => $productRepository->get('simple', forceReload: true)->getSpecialFromDate() !== null
        );

        $this->assertSessionMessages(
            $this->isEmpty(),
            MessageInterface::TYPE_ERROR
        );

        $updatedProduct = $productRepository->get('simple');
        $this->assertNotNull($updatedProduct->getSpecialFromDate());
        $this->assertEquals(7.00, $updatedProduct->getSpecialPrice());
    }

    /**
     * Test that mass update accepts special price with only to_date set.
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @return void
     */
    public function testSaveActionValidatesSpecialPriceWithOnlyToDate(): void
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->get(ProductRepositoryInterface::class);
        $product = $productRepository->get('simple');

        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        $session->setProductIds([$product->getId()]);

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue([
            'attributes' => [
                'special_to_date' => '2026-12-31',
                'special_price' => 6.00,
            ],
        ]);

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        $this->publisherConsumerController->waitForAsynchronousResult(
            fn () => $productRepository->get('simple', forceReload: true)->getSpecialToDate() !== null
        );

        $this->assertSessionMessages(
            $this->isEmpty(),
            MessageInterface::TYPE_ERROR
        );

        $updatedProduct = $productRepository->get('simple');
        $this->assertNotNull($updatedProduct->getSpecialToDate());
        $this->assertEquals(6.00, $updatedProduct->getSpecialPrice());
    }

    /**
     * Test that mass update validates special price dates when from_date equals to_date.
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @return void
     */
    public function testSaveActionValidatesSpecialPriceDateRangeWithEqualDates(): void
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->get(ProductRepositoryInterface::class);
        $product = $productRepository->get('simple');

        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        $session->setProductIds([$product->getId()]);

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue([
            'attributes' => [
                'special_from_date' => '2026-06-15',
                'special_to_date' => '2026-06-15',
                'special_price' => 8.00,
            ],
        ]);

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        $this->publisherConsumerController->waitForAsynchronousResult(
            fn () => $productRepository->get('simple', forceReload: true)->getSpecialFromDate() !== null
        );

        $this->assertSessionMessages(
            $this->isEmpty(),
            MessageInterface::TYPE_ERROR
        );

        $updatedProduct = $productRepository->get('simple');
        $this->assertNotNull($updatedProduct->getSpecialFromDate());
        $this->assertNotNull($updatedProduct->getSpecialToDate());
        $this->assertEquals(8.00, $updatedProduct->getSpecialPrice());
    }

    /**
     * Test that mass update validates special price dates for multiple products.
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Catalog/_files/second_product_simple.php
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @return void
     */
    public function testSaveActionValidatesSpecialPriceDateRangeForMultipleProducts(): void
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->get(ProductRepositoryInterface::class);
        $product1 = $productRepository->get('simple');
        $product2 = $productRepository->get('simple2');

        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        $session->setProductIds([$product1->getId(), $product2->getId()]);

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue([
            'attributes' => [
                'special_from_date' => '2026-12-31',
                'special_to_date' => '2026-01-01',
            ],
        ]);

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        $this->assertSessionMessages(
            $this->logicalOr(
                $this->containsEqual('Make sure the To Date is later than or the same as the From Date.'),
                $this->containsEqual('Please correct the product special price dates.')
            ),
            MessageInterface::TYPE_ERROR
        );

        // Validation failure prevents async operation, so no need to wait
        $updatedProduct1 = $productRepository->getById($product1->getId());
        $updatedProduct2 = $productRepository->getById($product2->getId());
        $this->assertNull($updatedProduct1->getSpecialFromDate());
        $this->assertNull($updatedProduct1->getSpecialToDate());
        $this->assertNull($updatedProduct2->getSpecialFromDate());
        $this->assertNull($updatedProduct2->getSpecialToDate());
    }
}
