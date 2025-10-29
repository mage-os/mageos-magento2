<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Test\Unit\Model\Plugin;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\OperationDefinitionNode;
use Magento\Framework\GraphQl\Query\QueryParser as FrameworkQueryParser;
use Magento\SalesRule\Model\Plugin\QueryParser;
use Magento\SalesRule\Model\Plugin\RequestTypeRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for QueryParser plugin
 */
class QueryParserTest extends TestCase
{
    /**
     * @var RequestTypeRegistry|MockObject
     */
    private $requestTypeRegistry;

    /**
     * @var QueryParser
     */
    private $queryParser;

    /**
     * @var FrameworkQueryParser|MockObject
     */
    private $subject;

    /**
     * @var DocumentNode|MockObject
     */
    private $documentNode;

    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        $this->requestTypeRegistry = $this->createMock(RequestTypeRegistry::class);
        $this->queryParser = new QueryParser($this->requestTypeRegistry);
        $this->subject = $this->createMock(FrameworkQueryParser::class);
        $this->documentNode = $this->createMock(DocumentNode::class);
    }

    /**
     * Test afterParse method with query operation
     */
    public function testAfterParseWithQueryOperation(): void
    {
        // Create a mock operation definition node with 'query' operation
        $operationNode = $this->createMock(OperationDefinitionNode::class);
        $operationNode->operation = 'query';

        // Create a mock definitions list with the operation node
        $definitions = new NodeList([$operationNode]);
        $this->documentNode->definitions = $definitions;

        // Expect RequestTypeRegistry to be called with true
        $this->requestTypeRegistry->expects($this->once())
            ->method('setIsGetRequestOrQuery')
            ->with(true);

        // Call the method
        $result = $this->queryParser->afterParse($this->subject, $this->documentNode);

        // Assert the document node is returned unchanged
        $this->assertSame($this->documentNode, $result);
    }

    /**
     * Test afterParse method with mutation operation
     */
    public function testAfterParseWithMutationOperation(): void
    {
        // Create a mock operation definition node with 'mutation' operation
        $operationNode = $this->createMock(OperationDefinitionNode::class);
        $operationNode->operation = 'mutation';

        // Create a mock definitions list with the operation node
        $definitions = new NodeList([$operationNode]);
        $this->documentNode->definitions = $definitions;

        // Expect RequestTypeRegistry to be called with false
        $this->requestTypeRegistry->expects($this->once())
            ->method('setIsGetRequestOrQuery')
            ->with(false);

        // Call the method
        $result = $this->queryParser->afterParse($this->subject, $this->documentNode);

        // Assert the document node is returned unchanged
        $this->assertSame($this->documentNode, $result);
    }

    /**
     * Test afterParse method with no operation definitions
     */
    public function testAfterParseWithNoOperationDefinitions(): void
    {
        // Create an empty definitions list
        $definitions = new NodeList([]);
        $this->documentNode->definitions = $definitions;

        // Expect RequestTypeRegistry not to be called
        $this->requestTypeRegistry->expects($this->never())
            ->method('setIsGetRequestOrQuery');

        // Call the method
        $result = $this->queryParser->afterParse($this->subject, $this->documentNode);

        // Assert the document node is returned unchanged
        $this->assertSame($this->documentNode, $result);
    }

    /**
     * Test afterParse method with non-operation definition nodes
     */
    public function testAfterParseWithNonOperationDefinitions(): void
    {
        // Create a mock node that is not an OperationDefinitionNode
        $nonOperationNode = $this->createMock(\GraphQL\Language\AST\Node::class);

        // Create a mock definitions list with the non-operation node
        $definitions = new NodeList([$nonOperationNode]);
        $this->documentNode->definitions = $definitions;

        // Expect RequestTypeRegistry not to be called
        $this->requestTypeRegistry->expects($this->never())
            ->method('setIsGetRequestOrQuery');

        // Call the method
        $result = $this->queryParser->afterParse($this->subject, $this->documentNode);

        // Assert the document node is returned unchanged
        $this->assertSame($this->documentNode, $result);
    }
}
