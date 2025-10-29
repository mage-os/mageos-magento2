<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Plugin;

class GraphQlController
{
    /**
     * @param RequestTypeRegistry          $requestTypeRegistry
     */
    public function __construct(
        private RequestTypeRegistry $requestTypeRegistry
    ) {
    }

    /**
     * Reset request type registry after dispatching GraphQL controller
     *
     * @param \Magento\Framework\App\FrontControllerInterface $subject
     * @param \Magento\Framework\App\ResponseInterface $result
     * @return \Magento\Framework\App\ResponseInterface $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Magento\Framework\App\ResponseInterface $result
    ) {
        $this->requestTypeRegistry->reset();
        return $result;
    }
}
