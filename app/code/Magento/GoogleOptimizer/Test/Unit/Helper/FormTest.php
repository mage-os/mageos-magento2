<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GoogleOptimizer\Test\Unit\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\GoogleOptimizer\Helper\Form;
use Magento\GoogleOptimizer\Model\Code;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * @var Form
     */
    protected $_helper;

    /**
     * @var MockObject
     */
    protected $_formMock;

    /**
     * @var MockObject
     */
    protected $_fieldsetMock;

    /**
     * @var MockObject
     */
    protected $_experimentCodeMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->_formMock = $this->getMockBuilder(\Magento\Framework\Data\Form::class)
            ->addMethods(['setFieldNameSuffix'])
            ->onlyMethods(['addFieldset'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->_fieldsetMock = $this->createMock(Fieldset::class);
        $this->_experimentCodeMock = $this->getMockBuilder(Code::class)
            ->addMethods(['getExperimentScript', 'getCodeId'])
            ->disableOriginalConstructor()
            ->getMock();
        $context = $this->createMock(Context::class);
        $data = ['context' => $context];
        $objectManagerHelper = new ObjectManager($this);
        $this->_helper = $objectManagerHelper->getObject(Form::class, $data);
    }

    /**
     * @return void
     */
    public function testAddFieldsWithExperimentCode(): void
    {
        $experimentCode = 'some-code';
        $experimentCodeId = 'code-id';
        $this->_experimentCodeMock->expects(
            $this->once()
        )->method(
            'getExperimentScript'
        )->willReturn(
            $experimentCode
        );
        $this->_experimentCodeMock->expects(
            $this->once()
        )->method(
            'getCodeId'
        )->willReturn(
            $experimentCodeId
        );
        $this->_prepareFormMock($experimentCode, $experimentCodeId);

        $this->_helper->addGoogleoptimizerFields($this->_formMock, $this->_experimentCodeMock);
    }

    /**
     * @return void
     */
    public function testAddFieldsWithoutExperimentCode(): void
    {
        $experimentCode = '';
        $experimentCodeId = '';
        $this->_prepareFormMock($experimentCode, $experimentCodeId);

        $this->_helper->addGoogleoptimizerFields($this->_formMock);
    }

    /**
     * @param string|array $experimentCode
     * @param string $experimentCodeId
     *
     * @return void
     */
    protected function _prepareFormMock($experimentCode, $experimentCodeId): void
    {
        $this->_formMock->expects(
            $this->once()
        )->method(
            'addFieldset'
        )->with(
            'googleoptimizer_fields',
            ['legend' => 'Google Analytics Content Experiments Code']
        )->willReturn(
            $this->_fieldsetMock
        );

        $this->_fieldsetMock
            ->method('addField')
            ->willReturnCallback(function ($arg1, $arg2, $arg3, $experimentCode, $experimentCodeId) {
                static $callCount = 0;
                if ($callCount === 0) {
                    $callCount++;
                    if ($arg1 == 'experiment_script' && $arg2 == 'textarea' && $arg3['value'] == $experimentCode) {
                        return null;
                    }
                } elseif ($callCount == 1 && $arg1 == 'hidden' && $arg3['value'] == $experimentCodeId) {
                    $callCount++;
                    return null;
                }
            });

        $this->_formMock->expects($this->once())->method('setFieldNameSuffix')->with('google_experiment');
    }
}
