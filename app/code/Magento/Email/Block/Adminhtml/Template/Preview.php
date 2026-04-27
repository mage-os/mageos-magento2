<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Email\Block\Adminhtml\Template;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Email\Model\AbstractTemplate;
use Magento\Email\Model\Template;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\Filter\Input\MaliciousCode;
use Magento\Framework\Profiler;
use Magento\Store\Model\Store;

/**
 * Email template preview block.
 *
 * @api
 * @since 100.0.2
 */
class Preview extends Widget
{
    /**
     * @var MaliciousCode
     */
    protected $_maliciousCode;

    /**
     * @var TemplateFactory
     */
    protected $_emailFactory;

    /**
     * @var string
     */
    protected $profilerName = 'email_template_proccessing';

    /**
     * @param Context $context
     * @param MaliciousCode $maliciousCode
     * @param TemplateFactory $emailFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        MaliciousCode $maliciousCode,
        TemplateFactory $emailFactory,
        array $data = []
    ) {
        $this->_maliciousCode = $maliciousCode;
        $this->_emailFactory = $emailFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare html output
     *
     * @return string
     * @throws Exception
     */
    protected function _toHtml()
    {
        $request = $this->getRequest();

        $storeId = $this->getAnyStoreView()->getId();
        /** @var $template Template */
        $template = $this->_emailFactory->create();

        if ($id = (int)$request->getParam('id')) {
            $template->load($id);
        } else {
            $template->setTemplateType($request->getParam('type'));
            $template->setTemplateText($this->_maliciousCode->filter($request->getParam('text')));
            $template->setTemplateStyles($request->getParam('styles'));

        }

        Profiler::start($this->profilerName);

        $template->emulateDesign($storeId);
        $templateProcessed = $this->_appState->emulateAreaCode(
            AbstractTemplate::DEFAULT_DESIGN_AREA,
            [$template, 'getProcessedTemplate']
        );
        $template->revertDesign();
        $templateProcessed = $this->_maliciousCode->filter($templateProcessed);

        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . $this->escapeHtml($templateProcessed) . "</pre>";
        }

        Profiler::stop($this->profilerName);

        return $templateProcessed;
    }

    /**
     * Get either default or any store view
     *
     * @return Store|null
     */
    protected function getAnyStoreView()
    {
        $store = $this->_storeManager->getDefaultStoreView();
        if ($store) {
            return $store;
        }
        foreach ($this->_storeManager->getStores() as $store) {
            return $store;
        }
        return null;
    }
}
