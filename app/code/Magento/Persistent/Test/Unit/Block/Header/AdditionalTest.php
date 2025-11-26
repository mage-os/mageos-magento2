<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Test\Unit\Block\Header;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\Math\Random;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Persistent\Block\Header\Additional;
use Magento\Persistent\Helper\Data;
use Magento\Persistent\Helper\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test helper class to avoid ObjectManager issues in constructor chain
 */
class AdditionalTestHelper extends Additional
{
    public function __construct(
        $customerViewHelper,
        $persistentSessionHelper,
        $customerRepository,
        $jsonSerializer,
        $persistentHelper
    ) {
        // Set protected properties directly
        $this->_customerViewHelper = $customerViewHelper;
        $this->_persistentSessionHelper = $persistentSessionHelper;
        $this->customerRepository = $customerRepository;
        
        // Use reflection to set private properties
        $reflection = new \ReflectionClass(Additional::class);
        
        $jsonSerializerProperty = $reflection->getProperty('jsonSerializer');
        $jsonSerializerProperty->setAccessible(true);
        $jsonSerializerProperty->setValue($this, $jsonSerializer);
        
        $persistentHelperProperty = $reflection->getProperty('persistentHelper');
        $persistentHelperProperty->setAccessible(true);
        $persistentHelperProperty->setValue($this, $persistentHelper);
    }
}

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AdditionalTest extends TestCase
{
    use MockCreationTrait;

    /**
     * @var View|MockObject
     */
    protected $customerViewHelperMock;

    /**
     * @var Session|MockObject
     */
    protected $persistentSessionHelperMock;

    /**
     * Customer repository
     *
     * @var CustomerRepositoryInterface|MockObject
     */
    protected $customerRepositoryMock;

    /**
     * @var Context|MockObject
     */
    protected $contextMock;

    /**
     * @var Json|MockObject
     */
    private $jsonSerializerMock;

    /**
     * @var Data|MockObject
     */
    private $persistentHelperMock;

    /**
     * @var Additional
     */
    protected $additional;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Set up
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp(): void
    {
        $this->customerViewHelperMock = $this->createMock(View::class);
        $this->persistentSessionHelperMock = $this->createPartialMock(
            Session::class,
            ['getSession']
        );
        // Use createMock() for interfaces - PHPUnit 12 compatible
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);

        $this->jsonSerializerMock = $this->createPartialMock(
            Json::class,
            ['serialize']
        );
        $this->persistentHelperMock = $this->createPartialMock(
            Data::class,
            ['getLifeTime']
        );

        // Use test helper class to avoid ObjectManager::getInstance() issues in parent constructors
        $this->additional = new AdditionalTestHelper(
            $this->customerViewHelperMock,
            $this->persistentSessionHelperMock,
            $this->customerRepositoryMock,
            $this->jsonSerializerMock,
            $this->persistentHelperMock
        );
    }

    /**
     * @return void
     */
    public function testGetCustomerId(): void
    {
        $customerId = 1;
        /** @var \Magento\Persistent\Model\Session|MockObject $sessionMock */
        // Use createPartialMockWithReflection for methods not in the class - PHPUnit 12 compatible
        $sessionMock = $this->createPartialMockWithReflection(
            \Magento\Persistent\Model\Session::class,
            ['getCustomerId']
        );
        $sessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $this->persistentSessionHelperMock->expects($this->once())
            ->method('getSession')
            ->willReturn($sessionMock);

        $this->assertEquals($customerId, $this->additional->getCustomerId());
    }

    /**
     * @return void
     */
    public function testGetConfig(): void
    {
        $lifeTime = 500;
        $arrayToSerialize = ['expirationLifetime' => $lifeTime];
        $serializedArray = '{"expirationLifetime":' . $lifeTime . '}';

        $this->persistentHelperMock->expects($this->once())
            ->method('getLifeTime')
            ->willReturn($lifeTime);
        $this->jsonSerializerMock->expects($this->once())
            ->method('serialize')
            ->with($arrayToSerialize)
            ->willReturn($serializedArray);

        $this->assertEquals($serializedArray, $this->additional->getConfig());
    }
}
