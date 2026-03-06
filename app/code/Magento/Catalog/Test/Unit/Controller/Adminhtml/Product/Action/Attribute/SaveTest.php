<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Controller\Adminhtml\Product\Action\Attribute;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save;
use Magento\Catalog\Helper\Product\Edit\Action\Attribute as AttributeHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Model\Product\Filter\DateTime as DateTimeFilter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Eav\Model\Entity\Attribute\Exception as EavAttributeException;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveTest extends TestCase
{
    use MockCreationTrait;

    /**
     * Build Save controller with injected dependencies.
     *
     * @param Context $context
     * @param AttributeHelper $attributeHelper
     * @param BulkManagementInterface $bulkManagement
     * @param OperationInterfaceFactory $operationFactory
     * @param IdentityGeneratorInterface $identityService
     * @param SerializerInterface $serializer
     * @param UserContextInterface $userContext
     * @param TimezoneInterface $timezone
     * @param EavConfig $eavConfig
     * @param ProductFactory $productFactory
     * @param DateTimeFilter $dateTimeFilter
     * @return Save
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    private function buildController(
        Context $context,
        AttributeHelper $attributeHelper,
        BulkManagementInterface $bulkManagement,
        OperationInterfaceFactory $operationFactory,
        IdentityGeneratorInterface $identityService,
        SerializerInterface $serializer,
        UserContextInterface $userContext,
        TimezoneInterface $timezone,
        EavConfig $eavConfig,
        ProductFactory $productFactory,
        DateTimeFilter $dateTimeFilter
    ): Save {
        return new Save(
            $context,
            $attributeHelper,
            $bulkManagement,
            $operationFactory,
            $identityService,
            $serializer,
            $userContext,
            100,
            $timezone,
            $eavConfig,
            $productFactory,
            $dateTimeFilter
        );
    }

    /**
     * Create dependency mocks for execute() success path (request, context, services, product, date filter).
     *
     * @param array $attributesData Request 'attributes' param value
     * @param array $productIds Product IDs for attribute helper
     * @param int $storeId Store ID
     * @param callable|null $getPost Optional getPost callback for request (e.g. multiselect toggle)
     * @param string|null $specialToDate Value for product->getSpecialToDate()
     * @return array{request: RequestInterface, messageManager: ManagerInterface, redirect: Redirect, context: Context, attributeHelper: AttributeHelper, bulkManagement: BulkManagementInterface, operationFactory: OperationInterfaceFactory, identityService: IdentityGeneratorInterface, serializer: SerializerInterface, userContext: UserContextInterface, timezone: TimezoneInterface, dateTimeFilter: DateTimeFilter, product: Product, productFactory: ProductFactory}
     */
    private function createExecuteSuccessDependencyMocks(
        array $attributesData,
        array $productIds = [1],
        int $storeId = 1,
        ?callable $getPost = null,
        ?string $specialToDate = null
    ): array {
        $request = $getPost
            ? $this->createPartialMock(HttpRequest::class, ['getParam', 'getPost'])
            : $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnMap([
            ['attributes', [], $attributesData],
            ['remove_website_ids', [], []],
            ['add_website_ids', [], []],
        ]);
        if ($getPost !== null && $request instanceof HttpRequest) {
            $request->method('getPost')->willReturnCallback($getPost);
        }
        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())->method('addSuccessMessage')->with($this->anything());
        $messageManager->expects($this->never())->method('addErrorMessage');
        $redirect = $this->createMock(Redirect::class);
        $redirect->method('setPath')->willReturnSelf();
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->method('create')->willReturn($redirect);
        $context = $this->createPartialMock(
            Context::class,
            ['getRequest', 'getMessageManager', 'getResultRedirectFactory']
        );
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultRedirectFactory')->willReturn($resultRedirectFactory);
        $attributeHelper = $this->createMock(AttributeHelper::class);
        $attributeHelper->method('getSelectedStoreId')->willReturn($storeId);
        $attributeHelper->method('getStoreWebsiteId')->with($storeId)->willReturn(1);
        $attributeHelper->method('getProductIds')->willReturn($productIds);
        $attributeHelper->method('setProductIds')->with([]);
        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $bulkManagement->expects($this->once())->method('scheduleBulk')->willReturn(true);
        $operation = $this->createMock(OperationInterface::class);
        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $operationFactory->expects($this->atLeastOnce())->method('create')->willReturn($operation);
        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $identityService->method('generateId')->willReturn('bulk-uuid');
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('serialized');
        $userContext = $this->createMock(UserContextInterface::class);
        $userContext->method('getUserId')->willReturn(1);
        $timezone = $this->createMock(TimezoneInterface::class);
        $dateTimeFilter = $this->createMock(DateTimeFilter::class);
        $dateTimeFilter->method('filter')->willReturnCallback(function ($v) {
            return $v ? $v . ' 00:00:00' : null;
        });
        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with($this->anything());
        $product->method('getSpecialToDate')->willReturn($specialToDate);
        $productFactory = $this->createMock(ProductFactory::class);
        $productFactory->method('create')->willReturn($product);
        return [
            'request' => $request,
            'messageManager' => $messageManager,
            'redirect' => $redirect,
            'context' => $context,
            'attributeHelper' => $attributeHelper,
            'bulkManagement' => $bulkManagement,
            'operationFactory' => $operationFactory,
            'identityService' => $identityService,
            'serializer' => $serializer,
            'userContext' => $userContext,
            'timezone' => $timezone,
            'dateTimeFilter' => $dateTimeFilter,
            'product' => $product,
            'productFactory' => $productFactory,
        ];
    }

    /**
     * Build Save controller mock with _validateProducts stubbed to true.
     *
     * @param array $deps Result of createExecuteSuccessDependencyMocks
     * @param EavConfig $eavConfig
     * @return Save
     */
    private function createExecuteControllerWithValidate(array $deps, EavConfig $eavConfig): Save
    {
        $controller = $this->getMockBuilder(Save::class)
            ->onlyMethods(['_validateProducts'])
            ->setConstructorArgs([
                $deps['context'],
                $deps['attributeHelper'],
                $deps['bulkManagement'],
                $deps['operationFactory'],
                $deps['identityService'],
                $deps['serializer'],
                $deps['userContext'],
                100,
                $deps['timezone'],
                $eavConfig,
                $deps['productFactory'],
                $deps['dateTimeFilter'],
            ])
            ->getMock();
        $controller->method('_validateProducts')->willReturn(true);
        return $controller;
    }

    /**
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::validateProductAttributes
     */
    public function testValidateProductAttributesSetsMaxValueAndConvertsEavException(): void
    {
        $context = $this->createMock(Context::class);
        $attributeHelper = $this->createMock(AttributeHelper::class);
        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $userContext = $this->createMock(UserContextInterface::class);
        $timezone = $this->createMock(TimezoneInterface::class);
        $eavConfig = $this->createMock(EavConfig::class);
        $productFactory = $this->createMock(ProductFactory::class);
        $dateTimeFilter = $this->createMock(DateTimeFilter::class);

        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with([
            'special_from_date' => '2025-09-10 00:00:00',
            'special_to_date' => '2025-09-01 00:00:00',
        ]);
        $product->method('getSpecialToDate')->willReturn('2025-09-01 00:00:00');

        $productFactory->method('create')->willReturn($product);

        // Attribute for special_from_date
        $fromAttrBackend = $this->createBackendMock(true);
        $fromAttribute = $this->createAttributeMock('2025-09-01 00:00:00', $fromAttrBackend);

        // Attribute for special_to_date
        $toAttrBackend = $this->createBackendMock(false);
        $toAttribute = $this->createAttributeMock(null, $toAttrBackend);

        // eavConfig should return attributes for 'special_from_date' and 'special_to_date'
        $eavConfig->method('getAttribute')
        ->willReturnCallback(function ($entity, $code) use ($fromAttribute, $toAttribute) {
            unset($entity);
            return $code === 'special_from_date' ? $fromAttribute : $toAttribute;
        });

        $controller = $this->buildController(
            $context,
            $attributeHelper,
            $bulkManagement,
            $operationFactory,
            $identityService,
            $serializer,
            $userContext,
            $timezone,
            $eavConfig,
            $productFactory,
            $dateTimeFilter
        );

        $method = new \ReflectionMethod($controller, 'validateProductAttributes');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Make sure the To Date is later than or the same as the From Date.');

        $method->invoke($controller, [
            'special_from_date' => '2025-09-10 00:00:00',
            'special_to_date'   => '2025-09-01 00:00:00',
        ]);
    }

    /**
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::validateProductAttributes
     */
    public function testValidateProductAttributesPassesWhenDatesValid(): void
    {
        $context = $this->createMock(Context::class);
        $attributeHelper = $this->createMock(AttributeHelper::class);
        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $userContext = $this->createMock(UserContextInterface::class);
        $timezone = $this->createMock(TimezoneInterface::class);
        $eavConfig = $this->createMock(EavConfig::class);
        $productFactory = $this->createMock(ProductFactory::class);
        $dateTimeFilter = $this->createMock(DateTimeFilter::class);

        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with([
            'special_from_date' => '2025-09-01 00:00:00',
            'special_to_date' => '2025-09-10 00:00:00',
        ]);
        $product->method('getSpecialToDate')->willReturn('2025-09-10 00:00:00');
        $productFactory->method('create')->willReturn($product);

        $okBackend = $this->createBackendMock(false);
        $fromAttribute = $this->createAttributeMock('2025-09-10 00:00:00', $okBackend);
        $toAttribute = $this->createAttributeMock(null, $okBackend);

        $eavConfig->method('getAttribute')
        ->willReturnCallback(function ($entity, $code) use ($fromAttribute, $toAttribute) {
            unset($entity);
            return $code === 'special_from_date' ? $fromAttribute : $toAttribute;
        });

        $controller = $this->buildController(
            $context,
            $attributeHelper,
            $bulkManagement,
            $operationFactory,
            $identityService,
            $serializer,
            $userContext,
            $timezone,
            $eavConfig,
            $productFactory,
            $dateTimeFilter
        );

        $method = new \ReflectionMethod($controller, 'validateProductAttributes');

        // Should not throw
        $method->invoke($controller, [
            'special_from_date' => '2025-09-01 00:00:00',
            'special_to_date'   => '2025-09-10 00:00:00',
        ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Execute flow: invalid special price dates (From > To) must show error and must not call publish.
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecuteShowsErrorAndDoesNotPublishWhenSpecialPriceFromDateAfterToDate(): void
    {
        $storeId = 1;
        $productIds = [1, 2];
        $attributesData = [
            'special_from_date' => '2025-09-10',
            'special_to_date' => '2025-09-01',
        ];

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnMap([
            ['attributes', [], $attributesData],
            ['remove_website_ids', [], []],
            ['add_website_ids', [], []],
        ]);

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with('Make sure the To Date is later than or the same as the From Date.');

        $redirect = $this->createMock(Redirect::class);
        $redirect->expects($this->once())->method('setPath')->with('catalog/product/', ['store' => $storeId])
            ->willReturnSelf();
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->method('create')->willReturn($redirect);

        $context = $this->createPartialMock(
            Context::class,
            ['getRequest', 'getMessageManager', 'getResultRedirectFactory']
        );
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultRedirectFactory')->willReturn($resultRedirectFactory);

        $attributeHelper = $this->createMock(AttributeHelper::class);
        $attributeHelper->method('getSelectedStoreId')->willReturn($storeId);
        $attributeHelper->method('getStoreWebsiteId')->with($storeId)->willReturn(1);
        $attributeHelper->method('getProductIds')->willReturn($productIds);

        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $bulkManagement->expects($this->never())->method('scheduleBulk');

        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $userContext = $this->createMock(UserContextInterface::class);
        $timezone = $this->createMock(TimezoneInterface::class);

        $dateTimeFilter = $this->createMock(DateTimeFilter::class);
        $dateTimeFilter->method('filter')->willReturnCallback(function ($value) {
            return $value ? $value . ' 00:00:00' : null;
        });

        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with($this->anything());
        $product->method('getSpecialToDate')->willReturn('2025-09-01 00:00:00');
        $productFactory = $this->createMock(ProductFactory::class);
        $productFactory->method('create')->willReturn($product);

        $fromAttrBackend = $this->createBackendMock(true);
        $toAttrBackend = $this->createBackendMock(false);
        $fromAttribute = $this->createDatetimeAttributeMockForExecute($fromAttrBackend);
        $toAttribute = $this->createDatetimeAttributeMockForExecute($toAttrBackend);

        $eavConfig = $this->createMock(EavConfig::class);
        $eavConfig->method('getAttribute')->willReturnCallback(
            function ($entity, $code) use ($fromAttribute, $toAttribute) {
                unset($entity);
                return $code === 'special_from_date' ? $fromAttribute : $toAttribute;
            }
        );

        $controller = $this->getMockBuilder(Save::class)
            ->onlyMethods(['_validateProducts'])
            ->setConstructorArgs([
                $context,
                $attributeHelper,
                $bulkManagement,
                $operationFactory,
                $identityService,
                $serializer,
                $userContext,
                100,
                $timezone,
                $eavConfig,
                $productFactory,
                $dateTimeFilter,
            ])
            ->getMock();
        $controller->method('_validateProducts')->willReturn(true);

        $result = $controller->execute();

        $this->assertSame($redirect, $result);
    }

    /**
     * Execute success path: valid attributes trigger publish and makeOperation (100% coverage).
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecutePublishesAndShowsSuccessWhenAttributesValid(): void
    {
        $attributesData = [
            'special_from_date' => '2025-09-01',
            'special_to_date' => '2025-09-10',
        ];
        $deps = $this->createExecuteSuccessDependencyMocks(
            $attributesData,
            [1, 2],
            1,
            null,
            '2025-09-10 00:00:00'
        );
        $okBackend = $this->createBackendMock(false);
        $fromAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);
        $toAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);
        $eavConfig = $this->createMock(EavConfig::class);
        $eavConfig->method('getAttribute')->willReturnCallback(
            function ($entity, $code) use ($fromAttribute, $toAttribute) {
                unset($entity);
                return $code === 'special_from_date' ? $fromAttribute : $toAttribute;
            }
        );
        $controller = $this->createExecuteControllerWithValidate($deps, $eavConfig);
        $result = $controller->execute();
        $this->assertSame($deps['redirect'], $result);
    }

    /**
     * Execute early return when _validateProducts fails (no publish).
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecuteEarlyReturnWhenValidateProductsFails(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->never())->method('addErrorMessage');

        $redirect = $this->createMock(Redirect::class);
        $redirect->expects($this->once())->method('setPath')->with('catalog/product/', ['_current' => true])
            ->willReturnSelf();
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->expects($this->once())->method('create')->willReturn($redirect);

        $context = $this->createPartialMock(
            Context::class,
            ['getRequest', 'getMessageManager', 'getResultRedirectFactory']
        );
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultRedirectFactory')->willReturn($resultRedirectFactory);

        $attributeHelper = $this->createMock(AttributeHelper::class);
        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $bulkManagement->expects($this->never())->method('scheduleBulk');

        $controller = $this->getMockBuilder(Save::class)
            ->onlyMethods(['_validateProducts'])
            ->setConstructorArgs([
                $context,
                $attributeHelper,
                $bulkManagement,
                $this->createMock(OperationInterfaceFactory::class),
                $this->createMock(IdentityGeneratorInterface::class),
                $this->createMock(SerializerInterface::class),
                $this->createMock(UserContextInterface::class),
                100,
                $this->createMock(TimezoneInterface::class),
                $this->createMock(EavConfig::class),
                $this->createMock(ProductFactory::class),
                $this->createMock(DateTimeFilter::class),
            ])
            ->getMock();
        $controller->method('_validateProducts')->willReturn(false);

        $result = $controller->execute();

        $this->assertSame($redirect, $result);
    }

    /**
     * Execute shows exception message when generic Exception is thrown (not LocalizedException).
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecuteAddsExceptionMessageOnGenericException(): void
    {
        $storeId = 1;
        $attributesData = ['special_from_date' => '2025-09-01', 'special_to_date' => '2025-09-10'];

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnMap([
            ['attributes', [], $attributesData],
            ['remove_website_ids', [], []],
            ['add_website_ids', [], []],
        ]);

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())->method('addExceptionMessage')->with(
            $this->isInstanceOf(\Exception::class),
            $this->anything()
        );

        $redirect = $this->createMock(Redirect::class);
        $redirect->method('setPath')->willReturnSelf();
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->method('create')->willReturn($redirect);

        $context = $this->createPartialMock(
            Context::class,
            ['getRequest', 'getMessageManager', 'getResultRedirectFactory']
        );
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultRedirectFactory')->willReturn($resultRedirectFactory);

        $attributeHelper = $this->createMock(AttributeHelper::class);
        $attributeHelper->method('getSelectedStoreId')->willReturn($storeId);
        $attributeHelper->method('getStoreWebsiteId')->with($storeId)->willReturn(1);
        $attributeHelper->method('getProductIds')->willReturn([1]);

        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $userContext = $this->createMock(UserContextInterface::class);
        $timezone = $this->createMock(TimezoneInterface::class);

        $dateTimeFilter = $this->createMock(DateTimeFilter::class);
        $dateTimeFilter->method('filter')->willReturnCallback(function ($v) {
            return $v ? $v . ' 00:00:00' : null;
        });

        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with($this->anything());
        $product->method('getSpecialToDate')->willReturn('2025-09-10 00:00:00');
        $productFactory = $this->createMock(ProductFactory::class);
        $productFactory->method('create')->willReturn($product);

        $failingBackend = $this->createPartialMock(AbstractBackend::class, ['validate']);
        $failingBackend->method('validate')->willThrowException(new \RuntimeException('Generic error'));

        $okBackend = $this->createBackendMock(false);
        $fromAttribute = $this->createDatetimeAttributeMockForExecute($failingBackend);
        $toAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);

        $eavConfig = $this->createMock(EavConfig::class);
        $eavConfig->method('getAttribute')->willReturnCallback(
            function ($entity, $code) use ($fromAttribute, $toAttribute) {
                unset($entity);
                return $code === 'special_from_date' ? $fromAttribute : $toAttribute;
            }
        );

        $controller = $this->getMockBuilder(Save::class)
            ->onlyMethods(['_validateProducts'])
            ->setConstructorArgs([
                $context,
                $attributeHelper,
                $bulkManagement,
                $operationFactory,
                $identityService,
                $serializer,
                $userContext,
                100,
                $timezone,
                $eavConfig,
                $productFactory,
                $dateTimeFilter,
            ])
            ->getMock();
        $controller->method('_validateProducts')->willReturn(true);

        $result = $controller->execute();

        $this->assertSame($redirect, $result);
    }

    /**
     * Execute when scheduleBulk returns false: LocalizedException is thrown and error shown.
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecuteShowsErrorWhenScheduleBulkFails(): void
    {
        $storeId = 1;
        $productIds = [1];
        $attributesData = ['special_from_date' => '2025-09-01', 'special_to_date' => '2025-09-10'];

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnMap([
            ['attributes', [], $attributesData],
            ['remove_website_ids', [], []],
            ['add_website_ids', [], []],
        ]);

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())->method('addErrorMessage')
            ->with('Something went wrong while processing the request.');

        $redirect = $this->createMock(Redirect::class);
        $redirect->method('setPath')->willReturnSelf();
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->method('create')->willReturn($redirect);

        $context = $this->createPartialMock(
            Context::class,
            ['getRequest', 'getMessageManager', 'getResultRedirectFactory']
        );
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultRedirectFactory')->willReturn($resultRedirectFactory);

        $attributeHelper = $this->createMock(AttributeHelper::class);
        $attributeHelper->method('getSelectedStoreId')->willReturn($storeId);
        $attributeHelper->method('getStoreWebsiteId')->with($storeId)->willReturn(1);
        $attributeHelper->method('getProductIds')->willReturn($productIds);

        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $bulkManagement->expects($this->once())->method('scheduleBulk')->willReturn(false);

        $operation = $this->createMock(OperationInterface::class);
        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $operationFactory->method('create')->willReturn($operation);
        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $identityService->method('generateId')->willReturn('bulk-uuid');
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('serialized');
        $userContext = $this->createMock(UserContextInterface::class);
        $userContext->method('getUserId')->willReturn(1);
        $timezone = $this->createMock(TimezoneInterface::class);

        $dateTimeFilter = $this->createMock(DateTimeFilter::class);
        $dateTimeFilter->method('filter')->willReturnCallback(function ($v) {
            return $v ? $v . ' 00:00:00' : null;
        });

        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with($this->anything());
        $product->method('getSpecialToDate')->willReturn('2025-09-10 00:00:00');
        $productFactory = $this->createMock(ProductFactory::class);
        $productFactory->method('create')->willReturn($product);

        $okBackend = $this->createBackendMock(false);
        $fromAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);
        $toAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);

        $eavConfig = $this->createMock(EavConfig::class);
        $eavConfig->method('getAttribute')->willReturnCallback(
            function ($entity, $code) use ($fromAttribute, $toAttribute) {
                unset($entity);
                return $code === 'special_from_date' ? $fromAttribute : $toAttribute;
            }
        );

        $controller = $this->getMockBuilder(Save::class)
            ->onlyMethods(['_validateProducts'])
            ->setConstructorArgs([
                $context,
                $attributeHelper,
                $bulkManagement,
                $operationFactory,
                $identityService,
                $serializer,
                $userContext,
                100,
                $timezone,
                $eavConfig,
                $productFactory,
                $dateTimeFilter,
            ])
            ->getMock();
        $controller->method('_validateProducts')->willReturn(true);

        $result = $controller->execute();

        $this->assertSame($redirect, $result);
    }

    /**
     * Execute with website data covers publish website + attributes operations (makeOperation both branches).
     */
    public function testExecutePublishWithWebsiteDataCoversBothOperations(): void
    {
        $storeId = 1;
        $websiteId = 1;
        $productIds = [1];
        $attributesData = ['special_from_date' => '2025-09-01', 'special_to_date' => '2025-09-10'];
        $websiteAddData = [1];
        $websiteRemoveData = [];

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnMap([
            ['attributes', [], $attributesData],
            ['remove_website_ids', [], $websiteRemoveData],
            ['add_website_ids', [], $websiteAddData],
        ]);

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())->method('addSuccessMessage')->with($this->anything());

        $redirect = $this->createMock(Redirect::class);
        $redirect->method('setPath')->willReturnSelf();
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->method('create')->willReturn($redirect);

        $context = $this->createPartialMock(
            Context::class,
            ['getRequest', 'getMessageManager', 'getResultRedirectFactory']
        );
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultRedirectFactory')->willReturn($resultRedirectFactory);

        $attributeHelper = $this->createMock(AttributeHelper::class);
        $attributeHelper->method('getSelectedStoreId')->willReturn($storeId);
        $attributeHelper->method('getStoreWebsiteId')->with($storeId)->willReturn($websiteId);
        $attributeHelper->method('getProductIds')->willReturn($productIds);
        $attributeHelper->method('setProductIds')->with([]);

        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $bulkManagement->method('scheduleBulk')->willReturn(true);

        $operation = $this->createMock(OperationInterface::class);
        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $operationFactory->expects($this->exactly(2))->method('create')->willReturn($operation);

        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $identityService->method('generateId')->willReturn('bulk-uuid');
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('serialized');
        $userContext = $this->createMock(UserContextInterface::class);
        $userContext->method('getUserId')->willReturn(1);
        $timezone = $this->createMock(TimezoneInterface::class);

        $dateTimeFilter = $this->createMock(DateTimeFilter::class);
        $dateTimeFilter->method('filter')->willReturnCallback(function ($v) {
            return $v ? $v . ' 00:00:00' : null;
        });

        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with($this->anything());
        $product->method('getSpecialToDate')->willReturn('2025-09-10 00:00:00');
        $productFactory = $this->createMock(ProductFactory::class);
        $productFactory->method('create')->willReturn($product);

        $okBackend = $this->createBackendMock(false);
        $fromAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);
        $toAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);

        $eavConfig = $this->createMock(EavConfig::class);
        $eavConfig->method('getAttribute')->willReturnCallback(
            function ($entity, $code) use ($fromAttribute, $toAttribute) {
                unset($entity);
                return $code === 'special_from_date' ? $fromAttribute : $toAttribute;
            }
        );

        $controller = $this->getMockBuilder(Save::class)
            ->onlyMethods(['_validateProducts'])
            ->setConstructorArgs([
                $context,
                $attributeHelper,
                $bulkManagement,
                $operationFactory,
                $identityService,
                $serializer,
                $userContext,
                100,
                $timezone,
                $eavConfig,
                $productFactory,
                $dateTimeFilter,
            ])
            ->getMock();
        $controller->method('_validateProducts')->willReturn(true);

        $result = $controller->execute();

        $this->assertSame($redirect, $result);
    }

    /**
     * Execute with has_weight and unknown attribute covers sanitize branches (continue and unset).
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecuteSanitizeCoversHasWeightAndUnknownAttribute(): void
    {
        $attributesData = [
            ProductAttributeInterface::CODE_HAS_WEIGHT => 1,
            'unknown_attr' => 'val',
            'special_from_date' => '2025-09-01',
            'special_to_date' => '2025-09-10',
        ];
        $deps = $this->createExecuteSuccessDependencyMocks(
            $attributesData,
            [1],
            1,
            null,
            '2025-09-10 00:00:00'
        );
        $okBackend = $this->createBackendMock(false);
        $fromAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);
        $toAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);
        $attrNoId = $this->createPartialMock(AbstractAttribute::class, ['getAttributeId']);
        $attrNoId->method('getAttributeId')->willReturn(0);
        $hasWeightAttr = $this->createPartialMock(
            AbstractAttribute::class,
            ['getAttributeId', 'getBackendType', 'getFrontendInput', 'getBackend']
        );
        $hasWeightAttr->method('getAttributeId')->willReturn(1);
        $hasWeightAttr->method('getBackendType')->willReturn('int');
        $hasWeightAttr->method('getFrontendInput')->willReturn('select');
        $hasWeightAttr->method('getBackend')->willReturn($okBackend);
        $eavConfig = $this->createMock(EavConfig::class);
        $eavConfig->method('getAttribute')->willReturnCallback(
            function ($entity, $code) use ($fromAttribute, $toAttribute, $attrNoId, $hasWeightAttr) {
                unset($entity);
                if ($code === ProductAttributeInterface::CODE_HAS_WEIGHT) {
                    return $hasWeightAttr;
                }
                if ($code === 'unknown_attr') {
                    return $attrNoId;
                }
                return $code === 'special_from_date' ? $fromAttribute : $toAttribute;
            }
        );
        $controller = $this->createExecuteControllerWithValidate($deps, $eavConfig);
        $result = $controller->execute();
        $this->assertSame($deps['redirect'], $result);
    }

    /**
     * Execute with empty attributes and no website data: publish builds no operations (scheduleBulk not called).
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecutePublishWithEmptyAttributesAndNoWebsiteBuildsNoOperations(): void
    {
        $storeId = 1;
        $productIds = [1];
        $attributesData = [];

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnMap([
            ['attributes', [], $attributesData],
            ['remove_website_ids', [], []],
            ['add_website_ids', [], []],
        ]);

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())->method('addSuccessMessage')->with($this->anything());

        $redirect = $this->createMock(Redirect::class);
        $redirect->method('setPath')->willReturnSelf();
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->method('create')->willReturn($redirect);

        $context = $this->createPartialMock(
            Context::class,
            ['getRequest', 'getMessageManager', 'getResultRedirectFactory']
        );
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultRedirectFactory')->willReturn($resultRedirectFactory);

        $attributeHelper = $this->createMock(AttributeHelper::class);
        $attributeHelper->method('getSelectedStoreId')->willReturn($storeId);
        $attributeHelper->method('getStoreWebsiteId')->with($storeId)->willReturn(1);
        $attributeHelper->method('getProductIds')->willReturn($productIds);
        $attributeHelper->method('setProductIds')->with([]);

        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $bulkManagement->expects($this->never())->method('scheduleBulk');

        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $userContext = $this->createMock(UserContextInterface::class);
        $timezone = $this->createMock(TimezoneInterface::class);
        $dateTimeFilter = $this->createMock(DateTimeFilter::class);
        $eavConfig = $this->createMock(EavConfig::class);
        $productFactory = $this->createMock(ProductFactory::class);
        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with([]);
        $product->method('getSpecialToDate')->willReturn(null);
        $productFactory->method('create')->willReturn($product);

        $controller = $this->getMockBuilder(Save::class)
            ->onlyMethods(['_validateProducts'])
            ->setConstructorArgs([
                $context,
                $attributeHelper,
                $bulkManagement,
                $operationFactory,
                $identityService,
                $serializer,
                $userContext,
                100,
                $timezone,
                $eavConfig,
                $productFactory,
                $dateTimeFilter,
            ])
            ->getMock();
        $controller->method('_validateProducts')->willReturn(true);

        $result = $controller->execute();

        $this->assertSame($redirect, $result);
    }

    /**
     * Execute with multiselect attribute covers sanitize multiselect branch (toggle checked, array imploded).
     * Also covers multiselect toggle not checked (unset and continue) via second attribute.
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecuteSanitizeMultiselectBranch(): void
    {
        $multiselectCode = 'multiselect_attr';
        $attributesData = [
            $multiselectCode => ['a', 'b'],
            'multiselect_attr_unchecked' => ['x'],
        ];
        $getPost = function ($key) use ($multiselectCode) {
            return $key === 'toggle_' . $multiselectCode ? '1' : null;
        };
        $deps = $this->createExecuteSuccessDependencyMocks($attributesData, [1], 1, $getPost, null);
        $okBackend = $this->createBackendMock(false);
        $multiselectAttr = $this->createPartialMock(
            AbstractAttribute::class,
            ['getAttributeId', 'getBackendType', 'getFrontendInput', 'getBackend']
        );
        $multiselectAttr->method('getAttributeId')->willReturn(1);
        $multiselectAttr->method('getBackendType')->willReturn('varchar');
        $multiselectAttr->method('getFrontendInput')->willReturn('multiselect');
        $multiselectAttr->method('getBackend')->willReturn($okBackend);
        $multiselectAttrUnchecked = $this->createPartialMock(
            AbstractAttribute::class,
            ['getAttributeId', 'getBackendType', 'getFrontendInput', 'getBackend']
        );
        $multiselectAttrUnchecked->method('getAttributeId')->willReturn(1);
        $multiselectAttrUnchecked->method('getBackendType')->willReturn('varchar');
        $multiselectAttrUnchecked->method('getFrontendInput')->willReturn('multiselect');
        $multiselectAttrUnchecked->method('getBackend')->willReturn($okBackend);
        $eavConfig = $this->createMock(EavConfig::class);
        $eavConfig->method('getAttribute')->willReturnCallback(
            function ($entity, $code) use ($multiselectAttr, $multiselectAttrUnchecked) {
                unset($entity);
                return $code === 'multiselect_attr_unchecked' ? $multiselectAttrUnchecked : $multiselectAttr;
            }
        );
        $controller = $this->createExecuteControllerWithValidate($deps, $eavConfig);
        $result = $controller->execute();
        $this->assertSame($deps['redirect'], $result);
    }

    /**
     * Execute with empty date value covers filterDate empty path (returns null).
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecuteWithEmptyDateValueCoversFilterDateEmpty(): void
    {
        $storeId = 1;
        $productIds = [1];
        $attributesData = [
            'special_from_date' => '2025-09-01',
            'special_to_date' => '',
        ];

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnMap([
            ['attributes', [], $attributesData],
            ['remove_website_ids', [], []],
            ['add_website_ids', [], []],
        ]);

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())->method('addSuccessMessage')->with($this->anything());

        $redirect = $this->createMock(Redirect::class);
        $redirect->method('setPath')->willReturnSelf();
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->method('create')->willReturn($redirect);

        $context = $this->createPartialMock(
            Context::class,
            ['getRequest', 'getMessageManager', 'getResultRedirectFactory']
        );
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultRedirectFactory')->willReturn($resultRedirectFactory);

        $attributeHelper = $this->createMock(AttributeHelper::class);
        $attributeHelper->method('getSelectedStoreId')->willReturn($storeId);
        $attributeHelper->method('getStoreWebsiteId')->with($storeId)->willReturn(1);
        $attributeHelper->method('getProductIds')->willReturn($productIds);
        $attributeHelper->method('setProductIds')->with([]);

        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $bulkManagement->method('scheduleBulk')->willReturn(true);

        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $operationFactory->method('create')->willReturn($this->createMock(OperationInterface::class));
        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $identityService->method('generateId')->willReturn('bulk-uuid');
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('serialized');
        $userContext = $this->createMock(UserContextInterface::class);
        $userContext->method('getUserId')->willReturn(1);
        $timezone = $this->createMock(TimezoneInterface::class);

        $dateTimeFilter = $this->createMock(DateTimeFilter::class);
        $dateTimeFilter->method('filter')->willReturnCallback(function ($value) {
            return $value !== '' && $value !== null ? $value . ' 00:00:00' : null;
        });

        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with($this->anything());
        $product->method('getSpecialToDate')->willReturn(null);
        $productFactory = $this->createMock(ProductFactory::class);
        $productFactory->method('create')->willReturn($product);

        $okBackend = $this->createBackendMock(false);
        $fromAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);
        $toAttribute = $this->createDatetimeAttributeMockForExecute($okBackend);

        $eavConfig = $this->createMock(EavConfig::class);
        $eavConfig->method('getAttribute')->willReturnCallback(
            function ($entity, $code) use ($fromAttribute, $toAttribute) {
                unset($entity);
                return $code === 'special_from_date' ? $fromAttribute : $toAttribute;
            }
        );

        $controller = $this->getMockBuilder(Save::class)
            ->onlyMethods(['_validateProducts'])
            ->setConstructorArgs([
                $context,
                $attributeHelper,
                $bulkManagement,
                $operationFactory,
                $identityService,
                $serializer,
                $userContext,
                100,
                $timezone,
                $eavConfig,
                $productFactory,
                $dateTimeFilter,
            ])
            ->getMock();
        $controller->method('_validateProducts')->willReturn(true);

        $result = $controller->execute();

        $this->assertSame($redirect, $result);
    }

    /**
     * Execute with datetime frontend input covers filterDate timezone conversion.
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save::execute
     */
    public function testExecuteWithDatetimeAttributeCallsTimezoneConversion(): void
    {
        $storeId = 1;
        $productIds = [1];
        $attributesData = [
            'news_from_date' => '2025-06-01 12:00:00',
        ];

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnMap([
            ['attributes', [], $attributesData],
            ['remove_website_ids', [], []],
            ['add_website_ids', [], []],
        ]);

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())->method('addSuccessMessage')->with($this->anything());

        $redirect = $this->createMock(Redirect::class);
        $redirect->method('setPath')->willReturnSelf();
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->method('create')->willReturn($redirect);

        $context = $this->createPartialMock(
            Context::class,
            ['getRequest', 'getMessageManager', 'getResultRedirectFactory']
        );
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultRedirectFactory')->willReturn($resultRedirectFactory);

        $attributeHelper = $this->createMock(AttributeHelper::class);
        $attributeHelper->method('getSelectedStoreId')->willReturn($storeId);
        $attributeHelper->method('getStoreWebsiteId')->with($storeId)->willReturn(1);
        $attributeHelper->method('getProductIds')->willReturn($productIds);
        $attributeHelper->method('setProductIds')->with([]);

        $bulkManagement = $this->createMock(BulkManagementInterface::class);
        $bulkManagement->method('scheduleBulk')->willReturn(true);

        $operationFactory = $this->createMock(OperationInterfaceFactory::class);
        $operationFactory->method('create')->willReturn($this->createMock(OperationInterface::class));
        $identityService = $this->createMock(IdentityGeneratorInterface::class);
        $identityService->method('generateId')->willReturn('bulk-uuid');
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('serialized');
        $userContext = $this->createMock(UserContextInterface::class);
        $userContext->method('getUserId')->willReturn(1);

        $timezone = $this->createMock(TimezoneInterface::class);
        $timezone->expects($this->once())->method('convertConfigTimeToUtc')->willReturn('2025-06-01 00:00:00');

        $dateTimeFilter = $this->createMock(DateTimeFilter::class);
        $dateTimeFilter->method('filter')->willReturn('2025-06-01 12:00:00');

        $product = $this->createPartialMock(Product::class, ['setData', 'getSpecialToDate']);
        $product->method('setData')->with($this->anything());
        $product->method('getSpecialToDate')->willReturn(null);
        $productFactory = $this->createMock(ProductFactory::class);
        $productFactory->method('create')->willReturn($product);

        $okBackend = $this->createBackendMock(false);
        $newsFromAttribute = $this->createDatetimeAttributeMockForExecuteWithFrontendInput($okBackend, 'datetime');

        $eavConfig = $this->createMock(EavConfig::class);
        $eavConfig->method('getAttribute')->willReturn($newsFromAttribute);

        $controller = $this->getMockBuilder(Save::class)
            ->onlyMethods(['_validateProducts'])
            ->setConstructorArgs([
                $context,
                $attributeHelper,
                $bulkManagement,
                $operationFactory,
                $identityService,
                $serializer,
                $userContext,
                100,
                $timezone,
                $eavConfig,
                $productFactory,
                $dateTimeFilter,
            ])
            ->getMock();
        $controller->method('_validateProducts')->willReturn(true);

        $result = $controller->execute();

        $this->assertSame($redirect, $result);
    }

    /**
     * Create attribute mock for execute flow with configurable frontend input (date vs datetime).
     *
     * @param AbstractBackend $backend Backend instance returned by getBackend().
     * @param string $frontendInput Frontend input type (e.g. 'date', 'datetime').
     * @return AbstractAttribute
     */
    private function createDatetimeAttributeMockForExecuteWithFrontendInput(
        AbstractBackend $backend,
        string $frontendInput = 'date'
    ): AbstractAttribute {
        $attribute = $this->createPartialMockWithReflection(
            AbstractAttribute::class,
            ['getAttributeId', 'getBackendType', 'getFrontendInput', 'setMaxValue', 'getBackend']
        );
        $attribute->method('getAttributeId')->willReturn(1);
        $attribute->method('getBackendType')->willReturn('datetime');
        $attribute->method('getFrontendInput')->willReturn($frontendInput);
        $attribute->method('setMaxValue')->willReturnSelf();
        $attribute->method('getBackend')->willReturn($backend);
        return $attribute;
    }

    /**
     * Create attribute mock for execute flow (sanitize + validate): datetime type with backend.
     *
     * @param AbstractBackend $backend Backend instance returned by getBackend().
     * @return AbstractAttribute
     */
    private function createDatetimeAttributeMockForExecute(AbstractBackend $backend): AbstractAttribute
    {
        $attribute = $this->createPartialMockWithReflection(
            AbstractAttribute::class,
            ['getAttributeId', 'getBackendType', 'getFrontendInput', 'setMaxValue', 'getBackend']
        );
        $attribute->method('getAttributeId')->willReturn(1);
        $attribute->method('getBackendType')->willReturn('datetime');
        $attribute->method('getFrontendInput')->willReturn('date');
        $attribute->method('setMaxValue')->willReturnSelf();
        $attribute->method('getBackend')->willReturn($backend);
        return $attribute;
    }

    /**
     * Create a backend mock with validate behavior.
     *
     * @param bool $shouldThrowException When true, validate() throws EavAttributeException.
     * @return AbstractBackend
     */
    private function createBackendMock(bool $shouldThrowException): AbstractBackend
    {
        $backend = $this->createPartialMock(AbstractBackend::class, ['validate']);
        
        if ($shouldThrowException) {
            $backend->method('validate')->willThrowException(
                new EavAttributeException(__('Make sure the To Date is later than or the same as the From Date.'))
            );
        } else {
            $backend->method('validate')->willReturn(true);
        }
        
        return $backend;
    }

    /**
     * Create an attribute mock with maxValue and backend.
     *
     * @param string|null $maxValue Value returned by getMaxValue().
     * @param AbstractBackend $backend Backend instance returned by getBackend().
     * @return AbstractAttribute
     */
    private function createAttributeMock(?string $maxValue, AbstractBackend $backend): AbstractAttribute
    {
        $attribute = $this->createPartialMockWithReflection(
            AbstractAttribute::class,
            ['setMaxValue', 'getMaxValue', 'getBackend']
        );
        
        $attribute->method('setMaxValue')->willReturnSelf();
        $attribute->method('getMaxValue')->willReturn($maxValue);
        $attribute->method('getBackend')->willReturn($backend);
        
        return $attribute;
    }
}
