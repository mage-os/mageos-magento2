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

use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\App\Response\Redirect;
use Magento\Store\Model\ScopeInterface;
use Magento\Wishlist\Controller\Index\Index;
use Magento\Wishlist\Controller\Index\Plugin;
use Magento\Wishlist\Model\AuthenticationState;
use Magento\Wishlist\Model\AuthenticationStateInterface;
use Magento\Wishlist\Model\DataSerializer;
use Magento\Customer\Test\Unit\Helper\CustomerSessionTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for wishlist plugin before dispatch
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PluginTest extends TestCase
{
    /**
     * @var Session|MockObject
     */
    protected $customerSession;

    /**
     * @var AuthenticationStateInterface|MockObject
     */
    protected $authenticationState;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $config;

    /**
     * @var RedirectInterface|MockObject
     */
    protected $redirector;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $messageManager;

    /**
     * @var Http|MockObject
     */
    protected $request;

    /**
     * @var DataSerializer|MockObject
     */
    private $dataSerializer;

    /**
     * @var FormKey|MockObject
     */
    private $formKey;

    /**
     * @var Validator|MockObject
     */
    private $formKeyValidator;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->customerSession = $this->createCustomerSessionMock();

        $this->authenticationState = $this->createMock(AuthenticationState::class);
        $this->config = $this->createMock(Config::class);
        $this->redirector = $this->createMock(Redirect::class);
        $this->messageManager = $this->createStub(ManagerInterface::class);
        $this->request = $this->createMock(Http::class);
        $this->dataSerializer = $this->createMock(DataSerializer::class);
        $this->formKey = $this->createMock(FormKey::class);
        $this->formKeyValidator = $this->createMock(Validator::class);
    }

    /**
     * @return Plugin
     */
    protected function getPlugin()
    {
        return new Plugin(
            $this->customerSession,
            $this->authenticationState,
            $this->config,
            $this->redirector,
            $this->messageManager,
            $this->dataSerializer,
            $this->formKey,
            $this->formKeyValidator
        );
    }

    public function testBeforeDispatch()
    {
        $this->expectException('Magento\Framework\Exception\NotFoundException');
        $refererUrl = 'http://referer-url.com';
        $params = [
            'product' => 1,
            'login' => [],
        ];

        $actionFlag = $this->createMock(ActionFlag::class);
        $indexController = $this->createMock(Index::class);

        $actionFlag
            ->expects($this->once())
            ->method('set')
            ->with('', 'no-dispatch', true)
            ->willReturn(true);

        $indexController
            ->expects($this->once())
            ->method('getActionFlag')
            ->willReturn($actionFlag);

        $this->authenticationState
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->redirector
            ->expects($this->once())
            ->method('getRefererUrl')
            ->willReturn($refererUrl);

        $this->request
            ->expects($this->once())
            ->method('getParams')
            ->willReturn($params);

        $this->request
            ->expects($this->exactly(2))
            ->method('getActionName')
            ->willReturn('add');
            
        $this->customerSession->beforeWishlistUrl = false;
        $this->customerSession->beforeWishlistRequest = $params;

        $this->config
            ->expects($this->once())
            ->method('isSetFlag')
            ->with('wishlist/general/active', ScopeInterface::SCOPE_STORES)
            ->willReturn(false);

        $this->getPlugin()->beforeDispatch($indexController, $this->request);
    }

    /**
     * Create customer session mock
     */
    private function createCustomerSessionMock()
    {
        return new CustomerSessionTestHelper();
    }
}
