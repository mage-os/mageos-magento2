<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Controller\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Session\Generic;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Review\Controller\Product\Post;
use Magento\Review\Model\Rating;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Review\Model\Review\Config;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PostTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $redirect;

    /**
     * @var MockObject
     */
    protected $request;

    /**
     * @var MockObject
     */
    protected $response;

    /**
     * @var MockObject
     */
    protected $formKeyValidator;

    /**
     * @var MockObject
     */
    protected $reviewSession;

    /**
     * @var MockObject
     */
    protected $eventManager;

    /**
     * @var MockObject
     */
    protected $productRepository;

    /**
     * @var MockObject
     */
    protected $coreRegistry;

    /**
     * @var MockObject
     */
    protected $review;

    /**
     * @var MockObject
     */
    protected $customerSession;

    /**
     * @var MockObject
     */
    protected $rating;

    /**
     * @var MockObject
     */
    protected $messageManager;

    /**
     * @var MockObject
     */
    protected $store;

    /**
     * @var Post
     */
    protected $model;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ResultFactory|MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var Redirect|MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var Config|MockObject
     */
    protected $reviewsConfig;

    /**
     * @inheritDoc
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp(): void
    {
        $this->redirect = $this->createMock(RedirectInterface::class);
        $this->request = $this->createPartialMock(Http::class, ['getParam']);
        $this->response = $this->createPartialMock(\Magento\Framework\App\Response\Http::class, ['setRedirect']);
        $this->formKeyValidator = $this->createPartialMock(
            Validator::class,
            ['validate']
        );
        $this->reviewsConfig = $this->createPartialMock(
            Config::class,
            ['isEnabled']
        );
        $this->reviewSession = new class extends Generic {
            /**
             * @var mixed
             */
            private $formData;
            /**
             * @var mixed
             */
            private $redirectUrl;
            public function __construct()
            {
            }
            public function getFormData($clear = false)
            {
                return $this->formData;
            }
            public function setFormData($data)
            {
                $this->formData = $data;
            }
            public function getRedirectUrl($clear = false)
            {
                return $this->redirectUrl;
            }
            public function setRedirectUrl($url)
            {
                $this->redirectUrl = $url;
            }
        };
        $this->eventManager = $this->createMock(ManagerInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->coreRegistry = $this->createMock(Registry::class);
        $this->review = new class extends Review {
            public function __construct()
            {
 /* Skip parent constructor */
            }
            public function setData($key, $value = null)
            {
                return $this;
            }
            public function validate()
            {
                return true;
            }
            public function setEntityId($entityId)
            {
                return $this;
            }
            public function getEntityIdByCode($entityCode)
            {
                return 1;
            }
            public function save()
            {
                return $this;
            }
            public function getId()
            {
                return 1;
            }
            public function aggregate()
            {
                return $this;
            }
            public function unsetData($key = null)
            {
                return $this;
            }
            public function setEntityPkValue($entityPkValue)
            {
                return $this;
            }
            public function setStatusId($statusId)
            {
                return $this;
            }
            public function setCustomerId($customerId)
            {
                return $this;
            }
            public function setStoreId($storeId)
            {
                return $this;
            }
            public function setStores($stores)
            {
                return $this;
            }
        };
        $reviewFactory = $this->createPartialMock(ReviewFactory::class, ['create']);
        $reviewFactory->expects($this->once())->method('create')->willReturn($this->review);
        $this->customerSession = $this->createPartialMock(Session::class, ['getCustomerId']);
        $this->rating = new class extends Rating {
            public function __construct()
            {
 /* Skip parent constructor */
            }
            public function addOptionVote($optionId, $entityPkValue)
            {
                return $this;
            }
            public function setRatingId($ratingId)
            {
                return $this;
            }
            public function setReviewId($reviewId)
            {
                return $this;
            }
            public function setCustomerId($customerId)
            {
                return $this;
            }
        };
        $ratingFactory = $this->createPartialMock(RatingFactory::class, ['create']);
        $ratingFactory->expects($this->once())->method('create')->willReturn($this->rating);
        $this->messageManager = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);

        $this->store = $this->createPartialMock(
            Store::class,
            ['getId', 'getWebsiteId']
        );

        $storeManager = $this->createPartialMock(\Magento\Store\Model\StoreManager::class, ['getStore']);
        $storeManager->expects($this->any())->method('getStore')->willReturn($this->store);

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->resultRedirectMock);

        $objectManagerHelper = new ObjectManager($this);
        $this->context = $objectManagerHelper->getObject(
            Context::class,
            [
                'request' => $this->request,
                'resultFactory' => $this->resultFactoryMock,
                'messageManager' => $this->messageManager
            ]
        );
        $this->model = $objectManagerHelper->getObject(
            Post::class,
            [
                'response' => $this->response,
                'redirect' => $this->redirect,
                'formKeyValidator' => $this->formKeyValidator,
                'reviewSession' => $this->reviewSession,
                'eventManager' => $this->eventManager,
                'productRepository' => $this->productRepository,
                'coreRegistry' => $this->coreRegistry,
                'reviewFactory' => $reviewFactory,
                'customerSession' => $this->customerSession,
                'ratingFactory' => $ratingFactory,
                'storeManager' => $storeManager,
                'context' => $this->context,
                'reviewsConfig' => $this->reviewsConfig
            ]
        );
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testExecute(): void
    {
        $reviewData = [
            'ratings' => [1 => 1],
            'review_id' => 2
        ];
        $productId = 1;
        $customerId = 1;
        $storeId = 1;
        $reviewId = 1;
        $redirectUrl = 'url';
        $this->formKeyValidator->expects($this->any())->method('validate')
            ->with($this->request)
            ->willReturn(true);
        $this->reviewsConfig->expects($this->any())->method('isEnabled')
            ->willReturn(true);
        $this->reviewSession->setFormData($reviewData);
        $this->reviewSession->setRedirectUrl($redirectUrl);
        $this->request
            ->method('getParam')
            ->willReturnCallback(function ($arg1, $arg2) {
                if ($arg1 == 'category' && $arg2 == false) {
                    return false;
                }
                if ($arg1 == 'id') {
                    return 1;
                }
            });
        $product = $this->createPartialMock(
            Product::class,
            ['__wakeup', 'isVisibleInCatalog', 'isVisibleInSiteVisibility', 'getId', 'getWebsiteIds']
        );
        $product->expects($this->once())
            ->method('isVisibleInCatalog')
            ->willReturn(true);
        $product->expects($this->once())
            ->method('isVisibleInSiteVisibility')
            ->willReturn(true);

        $product->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn([1]);

        $this->store->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);

        $this->productRepository->expects($this->any())->method('getById')
            ->with(1)
            ->willReturn($product);
        $this->coreRegistry
            ->method('register')
            ->willReturnCallback(function ($arg1, $arg2) use ($product) {
                if ($arg1 == 'current_product' && $arg2 == $product) {
                    return $this->coreRegistry;
                }
                if ($arg1 == 'product' && $arg2 == $product) {
                    return $this->coreRegistry;
                }
            });
        // Review mock methods provided by anonymous class
        $product->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($productId);
        // Review and rating mock methods provided by anonymous classes
        $this->messageManager->expects($this->once())->method('addSuccessMessage')
            ->with(__('You submitted your review for moderation.'))
            ->willReturnSelf();
        // Review session mock methods provided by anonymous class

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }
}
