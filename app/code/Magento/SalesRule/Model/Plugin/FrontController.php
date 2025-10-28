<?php

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
     * @return $request
     */
    public function beforeDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $method = strtoupper($request->getMethod());
        $isReadOnly = ($method === 'GET');

        // Set flag in TotalCollectionState
        $this->requestTypeRegistry->setIsGetRequestOrQuery($isReadOnly);

        return $request;
    }
}
