<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Reports\Model\Event;
use Magento\Weee\Block\Element\Weee\Tax;
use Magento\Weee\Observer\UpdateElementTypesObserver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Magento\Weee\Observer\UpdateElementTypesObserver
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class UpdateElementTypesObserverTest extends TestCase
{
    /*
     * Stub response type
     */
    public const STUB_RESPONSE_TYPE = [];

    /**
     * Testable Object
     *
     * @var UpdateElementTypesObserver
     */
    private $observer;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Observer|MockObject
     */
    private $observerMock;

    /**
     * @var Event|MockObject
     */
    private $eventMock;

    /**
     * @var DataObject|MockObject
     */
    private $responseMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->observerMock = $this->createMock(Observer::class);

        $this->eventMock = $this->createPartialMock(Event::class, []);
        $reflection = new \ReflectionClass($this->eventMock);
        $property = $reflection->getProperty('_data');
        $property->setValue($this->eventMock, []);

        $this->responseMock = $this->createPartialMock(DataObject::class, []);
        $reflection = new \ReflectionClass($this->responseMock);
        $property = $reflection->getProperty('_data');
        $property->setValue($this->responseMock, []);

        $this->observer = $this->objectManager->getObject(UpdateElementTypesObserver::class);
    }

    /**
     * Test for execute(), covers test case to adding custom element type for attributes form
     */
    public function testRemoveProductUrlsFromStorage(): void
    {
        $this->observerMock
            ->expects($this->once())
            ->method('getEvent')
            ->willReturn($this->eventMock);

        $this->eventMock->setResponse($this->responseMock);

        $this->responseMock->setTypes(self::STUB_RESPONSE_TYPE);

        $this->observer->execute($this->observerMock);

        $this->assertEquals(['weee' => Tax::class], $this->responseMock->getTypes());
    }
}
