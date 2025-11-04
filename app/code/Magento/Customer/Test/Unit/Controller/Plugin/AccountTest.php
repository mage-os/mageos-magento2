<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Controller\Plugin;

use Closure;
use Magento\Customer\Controller\AccountInterface;
use Magento\Customer\Controller\Plugin\Account;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\ResultInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    /**
     * @var string
     */
    private const EXPECTED_VALUE = 'expected_value';

    /**
     * @var Account
     */
    protected $plugin;

    /**
     * @var Session|MockObject
     */
    protected $sessionMock;

    /**
     * @var AccountInterface|MockObject
     */
    protected $actionMock;

    /**
     * @var Http|MockObject
     */
    protected $requestMock;

    /**
     * @var ActionFlag|MockObject
     */
    protected $actionFlagMock;

    /**
     * @var ResultInterface|MockObject
     */
    private $resultMock;

    protected function setUp(): void
    {
        $this->sessionMock = $this->createPartialMock(
            \Magento\Customer\Test\Unit\Helper\CustomerSessionTestHelper::class,
            ['authenticate', 'setNoReferer', 'unsNoReferer']
        );

        $this->actionMock = new \Magento\Customer\Test\Unit\Helper\AccountInterfaceTestHelper();

        $this->requestMock = $this->createPartialMock(
            HttpRequest::class,
            ['getActionName']
        );

        $this->resultMock = $this->createMock(ResultInterface::class);

        $this->actionFlagMock = $this->createMock(ActionFlag::class);
    }

    /**
     * @param string $action
     * @param array $allowedActions
     * @param boolean $isAllowed
     */
    #[DataProvider('beforeExecuteDataProvider')]
    public function testAroundExecuteInterruptsOriginalCallWhenNotAllowed(
        string $action,
        array $allowedActions,
        bool $isAllowed
    ) {
        /** @var callable|MockObject $proceedMock */
        $proceedMock = new \Magento\Framework\Test\Unit\Helper\CallableTestHelper();

        $closureMock = Closure::fromCallable($proceedMock);

        $this->requestMock->expects($this->once())
            ->method('getActionName')
            ->willReturn($action);

        if ($isAllowed) {
            $proceedMock->setReturnValue($this->resultMock);
            $proceedMock->setExpectedCallCount(1);
        } else {
            $proceedMock->setExpectedCallCount(0);
        }

        $plugin = new Account($this->requestMock, $this->sessionMock, $allowedActions);
        $result = $plugin->aroundExecute($this->actionMock, $closureMock);

        if ($isAllowed) {
            $this->assertSame($this->resultMock, $result);
            $this->assertTrue($proceedMock->verifyCallCount());
        } else {
            $this->assertNull($result);
            $this->assertTrue($proceedMock->verifyCallCount());
        }
    }

    /**
     * @return array
     */
    public static function beforeExecuteDataProvider()
    {
        return [
            [
                'action' => 'TestAction',
                'allowedActions' => ['TestAction'],
                'isAllowed' => true
            ],
            [
                'action' => 'testaction',
                'allowedActions' => ['testaction'],
                'isAllowed' => true
            ],
            [
                'action' => 'wrongaction',
                'allowedActions' => ['testaction'],
                'isAllowed' => false
            ],
            [
                'action' => 'wrongaction',
                'allowedActions' => ['testaction'],
                'isAllowed' => false
            ],
            [
                'action' => 'wrongaction',
                'allowedActions' => [],
                'isAllowed' => false
            ],
        ];
    }
}
