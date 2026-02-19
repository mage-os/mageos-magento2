<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Plugin;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\OperationDefinitionNode;

class QueryParser
{

    /**
     * @param RequestTypeRegistry          $requestTypeRegistry
     */
    public function __construct(
        private RequestTypeRegistry $requestTypeRegistry
    ) {
    }

    /**
     * Set QueryType to RequestTypeRegistry
     *
     * @param  \Magento\Framework\GraphQl\Query\QueryParser $subject
     * @param  DocumentNode $documentNode
     * @return DocumentNode $documentNode
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterParse(
        \Magento\Framework\GraphQl\Query\QueryParser $subject,
        DocumentNode $documentNode
    ): DocumentNode {
        // Get the first operation definition
        $operation = null;
        foreach ($documentNode->definitions as $definition) {
            if ($definition instanceof OperationDefinitionNode) {
                $operation = $definition;
                break;
            }
        }

        if ($operation) {
            $isOperationTypeQuery = ($operation->operation === 'query');
            $this->requestTypeRegistry->setIsGetRequestOrQuery($isOperationTypeQuery);
        }

        return $documentNode;
    }
}
