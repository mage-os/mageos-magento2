<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\LayeredNavigation\Test\Unit\Observer\Edit\Tab\Front;

use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\Test\Unit\Helper\ObserverTestHelper;
use Magento\Framework\Module\Manager;
use Magento\LayeredNavigation\Observer\Edit\Tab\Front\ProductAttributeFormBuildFormFieldDependenciesObserver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class ProductAttributeFormBuildFormFieldDependenciesObserverTest extends TestCase
{
    /**
     * @var MockObject|Manager
     */
    private Manager $moduleManager;
    /**
     * @var MockObject|Observer
     */
    private Observer $event;

    /**
     * @var MockObject|ProductAttributeFormBuildFormFieldDependenciesObserver
     */
    private ProductAttributeFormBuildFormFieldDependenciesObserver $observer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->moduleManager = $this->createMock(Manager::class);
        $this->event = new ObserverTestHelper();
        $this->event->setDependencies($this->createMock(Dependence::class));
        $this->observer = new ProductAttributeFormBuildFormFieldDependenciesObserver($this->moduleManager);

        parent::setUp();
    }

    /**
     * Test case when module output is disabled
     */
    public function testExecuteDisabled(): void
    {
        $this->moduleManager->expects($this->once())
            ->method('isOutputEnabled')
            ->with('Magento_LayeredNavigation')
            ->willReturn(false);

        // getDependencies() is directly implemented, no expects() needed

        $this->observer->execute($this->event);
    }

    /**
     * Test case when module output is enabled
     */
    public function testExecuteEnabled(): void
    {
        $this->moduleManager->expects($this->once())
            ->method('isOutputEnabled')
            ->with('Magento_LayeredNavigation')
            ->willReturn(true);

        $dependencies = $this->event->getDependencies();
        $dependencies->expects($this->once())
            ->method('addFieldMap')
            ->with('is_filterable_in_search', 'filterable_in_search');
        $dependencies->expects($this->once())
            ->method('addFieldDependence')
            ->with('filterable_in_search', 'searchable', '1');
        // getDependencies() is directly implemented, no expects() needed

        $this->observer->execute($this->event);
    }
}
