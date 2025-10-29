<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Plugin;

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
     * @param \Magento\Framework\GraphQl\Query\QueryParser $subject
     * @param \GraphQL\Language\AST\DocumentNode $documentNode
     * @return \GraphQL\Language\AST\DocumentNode $documentNode
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterParse(\Magento\Framework\GraphQl\Query\QueryParser $subject, $documentNode)
    {
        // Get the first operation definition
        $operation = null;
        foreach ($documentNode->definitions as $definition) {
            if ($definition instanceof \GraphQL\Language\AST\OperationDefinitionNode) {
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
