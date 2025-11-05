<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Widget\Test\Unit\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Properties;
use Magento\Widget\Model\Widget\Instance;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PropertiesTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $widget;

    /**
     * @var MockObject
     */
    protected $registry;

    /**
     * @var Properties
     */
    protected $propertiesBlock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        
        $objects = [
            [
                \Magento\Framework\View\Element\Template\Context::class,
                $this->createMock(\Magento\Framework\View\Element\Template\Context::class)
            ],
            [
                \Magento\Framework\App\ObjectManager::class,
                $this->createMock(\Magento\Framework\App\ObjectManager::class)
            ],
            [
                \Magento\Framework\View\Element\BlockFactory::class,
                $this->createMock(\Magento\Framework\View\Element\BlockFactory::class)
            ],
            [
                \Magento\Backend\Block\Template::class,
                $this->createMock(\Magento\Backend\Block\Template::class)
            ],
            [
                \Magento\Backend\Block\Widget\Form::class,
                $this->createMock(\Magento\Backend\Block\Widget\Form::class)
            ],
            [
                \Magento\Backend\Block\Widget\Form\Generic::class,
                $this->createMock(\Magento\Backend\Block\Widget\Form\Generic::class)
            ],
            [
                \Magento\Widget\Block\Adminhtml\Widget\Options::class,
                $this->createMock(\Magento\Widget\Block\Adminhtml\Widget\Options::class)
            ]
        ];
        $objectManager->prepareObjectManager($objects);
        
        $this->widget = $this->createMock(Instance::class);
        $this->registry = $this->createMock(Registry::class);

        $this->propertiesBlock = $objectManager->getObject(
            Properties::class,
            [
                'registry' => $this->registry
            ]
        );
    }

    /**
     * @param array   $widgetConfig
     * @param boolean $isHidden
     */
    #[DataProvider('isHiddenDataProvider')]
    public function testIsHidden($widgetConfig, $isHidden)
    {
        $this->widget->expects($this->atLeastOnce())->method('getWidgetConfigAsArray')->willReturn($widgetConfig);

        $this->registry->expects($this->atLeastOnce())
            ->method('registry')
            ->with('current_widget_instance')
            ->willReturn($this->widget);

        $this->assertEquals($isHidden, $this->propertiesBlock->isHidden());
    }

    /**
     * @return array
     */
    public static function isHiddenDataProvider()
    {
        return [
            [
                'widgetConfig' => [
                    'parameters' => [
                        'title' => [
                            'type' => 'text',
                            'visible' => '0',
                        ],
                        'template' => [
                            'type' => 'select',
                            'visible' => '1',
                        ],
                    ]
                ],
                'isHidden' => true
            ],
            [
                'widgetConfig' => [
                    'parameters' => [
                        'types' => [
                            'type' => 'multiselect',
                            'visible' => '1',
                        ],
                        'template' => [
                            'type' => 'select',
                            'visible' => '1',
                        ],
                    ]
                ],
                'isHidden' => false
            ],
            [
                'widgetConfig' => [],
                'isHidden' => true
            ],
            [
                'widgetConfig' => [
                    'parameters' => [
                        'template' => [
                            'type' => 'select',
                            'visible' => '0',
                        ],
                    ]
                ],
                'isHidden' => true
            ]
        ];
    }
}
