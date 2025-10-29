<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Plugin;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\FrontControllerInterface;

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
     * @param FrontControllerInterface $subject
     * @param ResponseInterface $result
     * @return ResponseInterface $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(
        FrontControllerInterface $subject,
        ResponseInterface $result
    ) : ResponseInterface {
        $this->requestTypeRegistry->reset();
        return $result;
    }
}
