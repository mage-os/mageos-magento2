<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Plugin;

class FrontController
{
    /**
     * @param RequestTypeRegistry          $requestTypeRegistry
     */
    public function __construct(
        private RequestTypeRegistry $requestTypeRegistry
    ) {
    }

    /**
     * Identify the Request Type and set true if it is a GET request
     *
     * @param \Magento\Framework\App\FrontControllerInterface $subject
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $method = strtoupper($request->getMethod());
        $isReadOnly = ($method === 'GET');

        $this->requestTypeRegistry->setIsGetRequestOrQuery($isReadOnly);
    }
}
