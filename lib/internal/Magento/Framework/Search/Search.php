<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Framework\Search;

use Magento\AdvancedSearch\Model\Client\ClientException;
use Magento\Elasticsearch\SearchAdapter\Aggregation\Builder as AggregationBuilder;
use Magento\Elasticsearch\SearchAdapter\ResponseFactory;
use Magento\Framework\Api\Search\SearchInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Search\Request\Builder;

/**
 * Search API for all requests.
 */
class Search implements SearchInterface
{
    /**
     * @var Builder
     */
    private Builder $requestBuilder;

    /**
     * @var ScopeResolverInterface
     */
    private ScopeResolverInterface $scopeResolver;

    /**
     * @var SearchEngineInterface
     */
    private SearchEngineInterface $searchEngine;

    /**
     * @var SearchResponseBuilder
     */
    private SearchResponseBuilder $searchResponseBuilder;

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * @var AggregationBuilder
     */
    private AggregationBuilder $aggregationBuilder;

    /**
     * @param Builder $requestBuilder
     * @param ScopeResolverInterface $scopeResolver
     * @param SearchEngineInterface $searchEngine
     * @param SearchResponseBuilder $searchResponseBuilder
     * @param ResponseFactory $responseFactory
     * @param AggregationBuilder $aggregationBuilder
     */
    public function __construct(
        Builder $requestBuilder,
        ScopeResolverInterface $scopeResolver,
        SearchEngineInterface $searchEngine,
        SearchResponseBuilder $searchResponseBuilder,
        ResponseFactory $responseFactory,
        AggregationBuilder $aggregationBuilder
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->scopeResolver = $scopeResolver;
        $this->searchEngine = $searchEngine;
        $this->searchResponseBuilder = $searchResponseBuilder;
        $this->responseFactory = $responseFactory;
        $this->aggregationBuilder = $aggregationBuilder;
    }

    /**
     * @inheritdoc
     */
    public function search(SearchCriteriaInterface $searchCriteria)
    {
        $this->requestBuilder->setRequestName($searchCriteria->getRequestName());

        $scope = $this->scopeResolver->getScope()->getId();
        $this->requestBuilder->bindDimension('scope', $scope);

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $this->addFieldToFilter($filter->getField(), $filter->getValue());
            }
        }

        $this->requestBuilder->setFrom($searchCriteria->getCurrentPage() * $searchCriteria->getPageSize());
        $this->requestBuilder->setSize($searchCriteria->getPageSize());

        /**
         * This added in Backward compatibility purposes.
         * Temporary solution for an existing API of a fulltext search request builder.
         * It must be moved to different API.
         * Scope to split Search request builder API in MC-16461.
         */
        if (method_exists($this->requestBuilder, 'setSort')) {
            $this->requestBuilder->setSort($searchCriteria->getSortOrders());
        }
        try {
            $request = $this->requestBuilder->create();
            $searchResponse = $this->searchEngine->search($request);
            $response = $this->searchResponseBuilder->build($searchResponse)
                ->setSearchCriteria($searchCriteria);
        } catch (ClientException $e) {
            $aggregations = $this->aggregationBuilder->build($request, AdapterInterface::EMPTY_RAW_RESPONSE);
            $response = $this->responseFactory->create(
                [
                    'documents' => [],
                    'aggregations' => $aggregations,
                    'total' => 0
                ]
            );
            $response = $this->searchResponseBuilder->build($response)->setSearchCriteria($searchCriteria);
        }

        return $response;
    }

    /**
     * Apply attribute filter to facet collection
     *
     * @param string $field
     * @param string|array|null $condition
     * @return $this
     */
    private function addFieldToFilter($field, $condition = null)
    {
        if (!is_array($condition) || !in_array(key($condition), ['from', 'to'], true)) {
            $this->requestBuilder->bind($field, $condition);
        } else {
            if (!empty($condition['from'])) {
                $this->requestBuilder->bind("{$field}.from", $condition['from']);
            }
            if (!empty($condition['to'])) {
                $this->requestBuilder->bind("{$field}.to", $condition['to']);
            }
        }

        return $this;
    }
}
