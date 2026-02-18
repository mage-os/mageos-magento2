<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
<<<<<<< HEAD
declare(strict_types=1);

=======
>>>>>>> a741d8bc62d (#40226 - Cache clean and Return Type comments were addressed)
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
