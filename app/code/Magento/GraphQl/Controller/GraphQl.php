<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Controller;

use Exception;
use GraphQL\Error\FormattedError;
use GraphQL\Error\SyntaxError;
use GraphQL\Language\Source;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\GraphQl\Exception\ExceptionFormatter;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\InvalidRequestInterface;
use Magento\Framework\GraphQl\Query\Fields as QueryFields;
use Magento\Framework\GraphQl\Query\QueryParser;
use Magento\Framework\GraphQl\Query\QueryProcessor;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\SchemaGeneratorInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Webapi\Response;
use Magento\GraphQl\Helper\Query\Logger\LogData;
use Magento\GraphQl\Model\Query\ContextFactoryInterface;
use Magento\GraphQl\Model\Query\Logger\LoggerPool;

/**
 * Front controller for web API GraphQL area.
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.3.0
 */
class GraphQl implements FrontControllerInterface
{
    private const METHOD_OPTIONS = 'OPTIONS';

    /**
     * @var \Magento\Framework\Webapi\Response
     * @deprecated 100.3.2
     * @see no replacement
     */
    private $response;

    /**
     * @var SchemaGeneratorInterface
     */
    private $schemaGenerator;

    /**
     * @var SerializerInterface
     */
    private $jsonSerializer;

    /**
     * @var QueryProcessor
     */
    private $queryProcessor;

    /**
     * @var ExceptionFormatter
     */
    private $graphQlError;

    /**
     * @var ContextInterface
     * @deprecated 100.3.3
     * @see $contextFactory is used for creating Context object
     */
    private $resolverContext;

    /**
     * @var HttpRequestProcessor
     */
    private $requestProcessor;

    /**
     * @var QueryFields
     */
    private $queryFields;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var HttpResponse
     */
    private $httpResponse;

    /**
     * @var ContextFactoryInterface
     */
    private $contextFactory;

    /**
     * @var LogData
     */
    private $logDataHelper;

    /**
     * @var LoggerPool
     */
    private $loggerPool;

    /**
     * @var AreaList
     */
    private $areaList;

    /**
     * @var QueryParser
     */
    private $queryParser;

    /**
     * @param Response $response
     * @param SchemaGeneratorInterface $schemaGenerator
     * @param SerializerInterface $jsonSerializer
     * @param QueryProcessor $queryProcessor
     * @param ExceptionFormatter $graphQlError
     * @param ContextInterface $resolverContext Deprecated. $contextFactory is used for creating Context object.
     * @param HttpRequestProcessor $requestProcessor
     * @param QueryFields $queryFields
     * @param JsonFactory|null $jsonFactory
     * @param HttpResponse|null $httpResponse
     * @param ContextFactoryInterface|null $contextFactory
     * @param LogData|null $logDataHelper
     * @param LoggerPool|null $loggerPool
     * @param AreaList|null $areaList
     * @param QueryParser|null $queryParser
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Response $response,
        SchemaGeneratorInterface $schemaGenerator,
        SerializerInterface $jsonSerializer,
        QueryProcessor $queryProcessor,
        ExceptionFormatter $graphQlError,
        ContextInterface $resolverContext,
        HttpRequestProcessor $requestProcessor,
        QueryFields $queryFields,
        ?JsonFactory $jsonFactory = null,
        ?HttpResponse $httpResponse = null,
        ?ContextFactoryInterface $contextFactory = null,
        ?LogData $logDataHelper = null,
        ?LoggerPool $loggerPool = null,
        ?AreaList $areaList = null,
        ?QueryParser $queryParser = null
    ) {
        $this->response = $response;
        $this->schemaGenerator = $schemaGenerator;
        $this->jsonSerializer = $jsonSerializer;
        $this->queryProcessor = $queryProcessor;
        $this->graphQlError = $graphQlError;
        $this->resolverContext = $resolverContext;
        $this->requestProcessor = $requestProcessor;
        $this->queryFields = $queryFields;
        $this->jsonFactory = $jsonFactory ?: ObjectManager::getInstance()->get(JsonFactory::class);
        $this->httpResponse = $httpResponse ?: ObjectManager::getInstance()->get(HttpResponse::class);
        $this->contextFactory = $contextFactory ?: ObjectManager::getInstance()->get(ContextFactoryInterface::class);
        $this->logDataHelper = $logDataHelper ?: ObjectManager::getInstance()->get(LogData::class);
        $this->loggerPool = $loggerPool ?: ObjectManager::getInstance()->get(LoggerPool::class);
        $this->areaList = $areaList ?: ObjectManager::getInstance()->get(AreaList::class);
        $this->queryParser = $queryParser ?: ObjectManager::getInstance()->get(QueryParser::class);
    }

    /**
     * Handle GraphQL request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @since 100.3.0
     */
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $this->areaList->getArea(Area::AREA_GRAPHQL)->load(Area::PART_TRANSLATE);
        $jsonResult = $this->jsonFactory->create();
        $data = [];
        $result = null;
        $schema = null;

        try {
            $data = $this->getDataFromRequest($request);
            $query = $data['query'] ?? '';

            /** @var Http $request */
            $this->requestProcessor->validateRequest($request);
            $statusCode = $request->getMethod() === self::METHOD_OPTIONS ? 204 : 200;

            if ($request->isGet() || $request->isPost()) {
                $parsedQuery = $this->queryParser->parse($query);
                $data['parsedQuery'] = $parsedQuery;

                // We must extract queried field names to avoid instantiation of unnecessary fields in webonyx schema
                // Temporal coupling is required for performance optimization
                $this->queryFields->setQuery($parsedQuery, $data['variables'] ?? null);
                $schema = $this->schemaGenerator->generate();

                $result = $this->queryProcessor->process(
                    $schema,
                    $parsedQuery,
                    $this->contextFactory->create(),
                    $data['variables'] ?? []
                );
                $statusCode = $this->getHttpResponseCode($result);
            }
        } catch (Exception $error) {
            [$result, $statusCode] = $this->handleGraphQlException($error);
        }

        $jsonResult->setHttpResponseCode($statusCode);
        if ($result !== null) {
            $jsonResult->setData($result);
        }
        $jsonResult->renderResult($this->httpResponse);

        // log information about the query, unless it is an introspection query
        if (!isset($data['query']) || strpos($data['query'], 'IntrospectionQuery') === false) {
            $queryInformation = $this->logDataHelper->getLogData($request, $data, $schema, $this->httpResponse);
            $this->loggerPool->execute($queryInformation);
        }

        return $this->httpResponse;
    }

    /**
     * Handle GraphQL Exceptions
     *
     * @param Exception $e
     * @return array
     */
    private function handleGraphQlException(Exception $e): array
    {
        [$error, $statusCode] = match (true) {
            $e instanceof InvalidRequestInterface => [FormattedError::createFromException($e), $e->getStatusCode()],
            $e instanceof SyntaxError => [FormattedError::createFromException($e), 400],
            $e instanceof GraphQlAuthenticationException => [$this->graphQlError->create($e), 401],
            $e instanceof GraphQlAuthorizationException => [$this->graphQlError->create($e), 403],
            $e instanceof GraphQlInputException => [FormattedError::createFromException($e), 200],
            default => [$this->graphQlError->create($e), ExceptionFormatter::HTTP_GRAPH_QL_SCHEMA_ERROR_STATUS],
        };

        return [['errors' => [$error]], $statusCode];
    }

    /**
     * Retrieve http response code based on the error categories
     *
     * @param array $result
     * @return int
     */
    private function getHttpResponseCode(array $result): int
    {
        foreach ($result['errors'] ?? [] as $error) {
            if (isset($error['extensions']['category'])) {
                return match ($error['extensions']['category']) {
                    GraphQlAuthenticationException::EXCEPTION_CATEGORY => 401,
                    GraphQlAuthorizationException::EXCEPTION_CATEGORY => 403,
                    default => 200,
                };
            }
        }

        return 200;
    }

    /**
     * Get data from request body or query string
     *
     * @param RequestInterface $request
     * @return array
     * @throws SyntaxError
     */
    private function getDataFromRequest(RequestInterface $request): array
    {
        $data = [];
        /** @var Http $request */
        if ($request->isPost() && $request->getContent()) {
            $content = $request->getContent();
            try {
                $data = $this->jsonSerializer->unserialize($content);
            } catch (\InvalidArgumentException) {
                $source = new Source($content);
                throw new SyntaxError($source, 0, 'Unable to parse the request.');
            }
        } elseif ($request->isGet()) {
            $data = $request->getParams();
            try {
                $data['variables'] = !empty($data['variables']) && is_string($data['variables'])
                    ? $this->jsonSerializer->unserialize($data['variables'])
                    : null;
            } catch (\InvalidArgumentException) {
                $source = new Source($data['variables']);
                throw new SyntaxError($source, 0, 'Unable to parse the variables.');
            }
        }

        return $data;
    }
}
