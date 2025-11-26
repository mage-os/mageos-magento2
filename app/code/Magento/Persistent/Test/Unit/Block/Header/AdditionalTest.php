<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Test\Unit\Block\Header;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Persistent\Block\Header\Additional;
use Magento\Persistent\Helper\Data;
use Magento\Persistent\Helper\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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

        // Create mock of Additional class and use reflection to set properties
        $this->additional = $this->createPartialMock(Additional::class, []);
        
        // Use reflection to set protected properties
        $reflection = new \ReflectionClass(Additional::class);
        
        $customerViewHelperProperty = $reflection->getProperty('_customerViewHelper');
        $customerViewHelperProperty->setAccessible(true);
        $customerViewHelperProperty->setValue($this->additional, $this->customerViewHelperMock);
        
        $persistentSessionHelperProperty = $reflection->getProperty('_persistentSessionHelper');
        $persistentSessionHelperProperty->setAccessible(true);
        $persistentSessionHelperProperty->setValue($this->additional, $this->persistentSessionHelperMock);
        
        $customerRepositoryProperty = $reflection->getProperty('customerRepository');
        $customerRepositoryProperty->setAccessible(true);
        $customerRepositoryProperty->setValue($this->additional, $this->customerRepositoryMock);
        
        // Use reflection to set private properties
        $jsonSerializerProperty = $reflection->getProperty('jsonSerializer');
        $jsonSerializerProperty->setAccessible(true);
        $jsonSerializerProperty->setValue($this->additional, $this->jsonSerializerMock);
        
        $persistentHelperProperty = $reflection->getProperty('persistentHelper');
        $persistentHelperProperty->setAccessible(true);
        $persistentHelperProperty->setValue($this->additional, $this->persistentHelperMock);
    }

    /**
     * Test getCustomerId method
     *
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
     * Test getConfig method
     *
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
