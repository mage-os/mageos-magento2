<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Controller\Index;

use Magento\Captcha\Helper\Data as CaptchaHelper;
use Magento\Captcha\Model\DefaultModel as CaptchaModel;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Data\Customer as CustomerData;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\Layout as ResultLayout;
use Magento\Store\Model\Store;
use Magento\Wishlist\Controller\Index\Send;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\Config;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\Session\Generic as WishlistSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Captcha\Observer\CaptchaStringResolver;
use Magento\Framework\Escaper;
use Magento\Framework\Test\Unit\Helper\RequestInterfaceTestHelper;
use Magento\Customer\Test\Unit\Helper\CustomerTestHelper;
use Magento\Customer\Test\Unit\Helper\SessionTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SendTest extends TestCase
{
    /**
     * @var Send|MockObject
     */
    protected $model;

    /**
     * @var ActionContext|MockObject
     */
    protected $context;

    /**
     * @var FormKeyValidator|MockObject
     */
    protected $formKeyValidator;

    /**
     * @var WishlistProviderInterface|MockObject
     */
    protected $wishlistProvider;

    /**
     * @var Store|MockObject
     */
    protected $store;

    /**
     * @var ResultFactory|MockObject
     */
    protected $resultFactory;

    /**
     * @var ResultRedirect|MockObject
     */
    protected $resultRedirect;

    /**
     * @var ResultLayout|MockObject
     */
    protected $resultLayout;

    /**
     * @var RequestInterface|MockObject
     */
    protected $request;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $messageManager;

    /**
     * @var CustomerData|MockObject
     */
    protected $customerData;

    /**
     * @var UrlInterface|MockObject
     */
    protected $url;

    /**
     * @var TransportInterface|MockObject
     */
    protected $transport;

    /**
     * @var EventManagerInterface|MockObject
     */
    protected $eventManager;

    /**
     * @var CaptchaHelper|MockObject
     */
    protected $captchaHelper;

    /**
     * @var CaptchaModel|MockObject
     */
    protected $captchaModel;

    /**
     * @var Session|MockObject
     */
    protected $customerSession;

    /**
     * @var Config|MockObject
     */
    protected $wishlistConfig;

    /**
     * @var TransportBuilder|MockObject
     */
    protected $transportBuilder;

    /**
     * @var StateInterface|MockObject
     */
    protected $inlineTranslation;

    /**
     * @var View|MockObject
     */
    protected $customerHelperView;

    /**
     * @var WishlistSession|MockObject
     */
    protected $wishlistSession;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface|MockObject
     */
    protected $storeManager;

    /**
     * @var CaptchaStringResolver|MockObject
     */
    protected $captchaStringResolver;

    /**
     * @var Escaper|MockObject
     */
    protected $escaper;

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp(): void
    {
        $this->resultRedirect = $this->createMock(ResultRedirect::class);
        $this->resultLayout = $this->createMock(ResultLayout::class);
        $this->resultFactory = $this->createMock(ResultFactory::class);
        $this->resultFactory->expects($this->any())
            ->method('create')
            ->willReturnMap(
                [
                [ResultFactory::TYPE_REDIRECT, [], $this->resultRedirect],
                [ResultFactory::TYPE_LAYOUT, [], $this->resultLayout],
                ]
            );

        $this->request = new RequestInterfaceTestHelper();

        $this->messageManager = $this->createStub(ManagerInterface::class);

        $this->url = $this->createMock(UrlInterface::class);

        $this->eventManager = $this->createMock(EventManagerInterface::class);

        $this->context = $this->createMock(ActionContext::class);
        $this->context->method('getRequest')->willReturn($this->request);
        $this->context->method('getResultFactory')->willReturn($this->resultFactory);
        $this->context->method('getMessageManager')->willReturn($this->messageManager);
        $this->context->method('getUrl')->willReturn($this->url);
        $this->context->method('getEventManager')->willReturn($this->eventManager);

        $this->formKeyValidator = $this->createMock(FormKeyValidator::class);

        $customerMock = new CustomerTestHelper();

        $this->customerSession = new SessionTestHelper();
        $this->customerSession->setCustomer($customerMock);

        $this->wishlistProvider = $this->createMock(WishlistProviderInterface::class);

        $this->captchaModel = $this->createMock(CaptchaModel::class);
        
        $this->captchaHelper = $this->createMock(CaptchaHelper::class);

        $this->wishlistConfig = $this->createMock(Config::class);
        $this->transportBuilder = $this->createMock(TransportBuilder::class);
        $this->inlineTranslation = $this->createMock(StateInterface::class);
        $this->customerHelperView = $this->createMock(View::class);
        $this->wishlistSession = $this->createMock(WishlistSession::class);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->captchaStringResolver = $this->createMock(CaptchaStringResolver::class);
        $this->escaper = $this->createMock(Escaper::class);

        $this->captchaHelper->expects($this->once())
            ->method('getCaptcha')
            ->willReturn($this->captchaModel);
        $this->captchaModel->method('isRequired')->willReturn(false);
        $this->captchaModel->method('logAttempt')->willReturn($this->captchaModel);

        $this->model = new Send(
            $this->context,
            $this->formKeyValidator,
            $this->customerSession,
            $this->wishlistProvider,
            $this->wishlistConfig,
            $this->transportBuilder,
            $this->inlineTranslation,
            $this->customerHelperView,
            $this->wishlistSession,
            $this->scopeConfig,
            $this->storeManager,
            $this->captchaHelper,
            $this->captchaStringResolver,
            $this->escaper
        );
    }

    /**
     * Verify execute method without Form Key validated
     *
     * @return void
     */
    public function testExecuteNoFormKeyValidated(): void
    {
        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(false);

        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertEquals($this->resultRedirect, $this->model->execute());
    }

    /**
     * Verify execute with no emails left
     *
     * @return void
     */
    public function testExecuteWithNoEmailLeft(): void
    {
        $expectedMessage = new Phrase('Maximum of %1 emails can be sent.', [0]);

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(true);

        $this->request->postData = [
            'emails' => 'some.email2@gmail.com',
            'message' => null
        ];

        $wishlist = $this->createMock(Wishlist::class);
        $this->wishlistProvider->expects($this->once())
            ->method('getWishlist')
            ->willReturn($wishlist);
        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/share')
            ->willReturnSelf();
        $this->messageManager->method('addErrorMessage')
            ->with($expectedMessage);

        $this->assertEquals($this->resultRedirect, $this->model->execute());
    }

    /**
     * Execute method with no wishlist available.
     *
     * @return void
     */
    public function testExecuteNoWishlistAvailable(): void
    {
        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(true);

        $this->wishlistProvider->expects($this->once())
            ->method('getWishlist')
            ->willReturn(null);
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Page not found');

        $this->model->execute();
    }
}
