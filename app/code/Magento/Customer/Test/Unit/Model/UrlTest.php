<?php
/**
 * Copyright 2022 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model;

use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Customer\Test\Unit\Helper\CustomerSessionTestHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Test\Unit\Helper\RequestInterfaceTestHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\Customer\Model\Url
 */
class UrlTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /**
     * @var Session|MockObject
     */
    protected $customerSessionMock;

    /**
     * @var UrlInterface|MockObject
     */
    protected $urlBuilderMock;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Url
     */
    protected $model;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->requestMock = $this->createPartialMock(
            RequestInterfaceTestHelper::class,
            ['isGet', 'getParam']
        );
        $this->customerSessionMock = $this->createPartialMock(
            CustomerSessionTestHelper::class,
            ['getNoReferer']
        );
        $this->urlBuilderMock = $this->createMock(UrlInterface::class);

        $this->model = $this->objectManager->getObject(
            Url::class,
            [
                'scopeConfig' => $this->scopeConfigMock,
                'request' => $this->requestMock,
                'customerSession' => $this->customerSessionMock,
                'urlBuilder' => $this->urlBuilderMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testGetLoginUrlParamsForNoRouteReferrer()
    {
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with(Url::REFERER_QUERY_PARAM_NAME)
            ->willReturn(null);
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->willReturn(false);
        $this->customerSessionMock->expects($this->any())
            ->method('getNoReferer')
            ->willReturn(false);
        $this->requestMock->expects($this->any())
            ->method('isGet')
            ->willReturn(true);
        $this->urlBuilderMock->expects($this->any())
            ->method('getUrl')
            ->willReturn('cms/noroute/index');

        $this->assertEquals([], $this->model->getLoginUrlParams());
    }
}
