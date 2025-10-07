<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\LayeredNavigation\Test\Unit\Observer\Edit\Tab\Front;

use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\Test\Unit\Helper\ObserverTestHelper;
use Magento\Framework\Module\Manager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\LayeredNavigation\Observer\Edit\Tab\Front\ProductAttributeFormBuildFrontTabObserver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit Test for \Magento\LayeredNavigation\Observer\Edit\Tab\Front\ProductAttributeFormBuildFrontTabObserver
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class ProductAttributeFormBuildFrontTabObserverTest extends TestCase
{
    /**
     * @var MockObject|Observer
     */
    private $eventObserverMock;

    /**
     * @var MockObject|Yesno
     */
    private $optionListLock;

    /**
     * @var MockObject|Manager
     */
    private $moduleManagerMock;

    /**
     * @var ProductAttributeFormBuildFrontTabObserver
     */
    private $observer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->optionListLock = $this->createMock(Yesno::class);
        $this->moduleManagerMock = $this->createMock(Manager::class);
        $this->eventObserverMock = new ObserverTestHelper();
        $formMock = $this->createMock(Form::class);
        $this->eventObserverMock->setForm($formMock);

        $objectManager = new ObjectManager($this);
        $this->observer = $objectManager->getObject(
            ProductAttributeFormBuildFrontTabObserver::class,
            [
                'optionList' => $this->optionListLock,
                'moduleManager' => $this->moduleManagerMock,
            ]
        );
    }

    /**
     * Test case when module output is disabled
     */
    public function testExecuteWhenOutputDisabled(): void
    {
        $this->moduleManagerMock->expects($this->once())
            ->method('isOutputEnabled')
            ->with('Magento_LayeredNavigation')
            ->willReturn(false);

        // getForm() is directly implemented, no expects() needed

        $this->observer->execute($this->eventObserverMock);
    }

    /**
     * Test case when module output is enabled
     */
    public function testExecuteWhenOutputEnabled(): void
    {
        $this->moduleManagerMock->expects($this->once())
            ->method('isOutputEnabled')
            ->with('Magento_LayeredNavigation')
            ->willReturn(true);

        $fieldsetMock = $this->createMock(Fieldset::class);
        $fieldsetMock->expects($this->exactly(3))->method('addField');
        $formMock = $this->eventObserverMock->getForm();
        $formMock->expects($this->once())
            ->method('getElement')
            ->with('front_fieldset')
            ->willReturn($fieldsetMock);

        // getForm() is directly implemented, no expects() needed

        $this->observer->execute($this->eventObserverMock);
    }
}
