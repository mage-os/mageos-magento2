<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\OpenSearch\Plugin\SearchAdapter\Query\Builder;

use Magento\Elasticsearch\SearchAdapter\Query\Builder\QueryInterface as BaseMatchQuery;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider;

/**
 * Modifications for sanitize search query and output result when name attribute disabled from search
 */
class MatchQueryPlugin
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * Constructor
     *
     * @param DataProvider $dataProvider
     */
    public function __construct(
        DataProvider $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
    }

    /**
     * After build plugin to modify selectQuery
     *
     * @param BaseMatchQuery $subject
     * @param array $selectQuery
     * @return array
     */
    public function afterBuild(
        BaseMatchQuery $subject,
        $selectQuery,
    ): array {

        $nameIsSearchable = $this->dataProvider->getSearchableAttribute('name')->getIsSearchable();
        if ($nameIsSearchable === 0) {
            if (isset($selectQuery['bool']['should'][0]['match_phrase_prefix']['name']['query'])) {
                $requestQueryValue = $selectQuery['bool']['should'][0]['match_phrase_prefix']['name']['query'];
                if (!empty($requestQueryValue) &&
                    str_contains($requestQueryValue, ' ') || str_contains($requestQueryValue, '-')) {
                    $requestQueryValue = str_replace(['-', ' '], '', $requestQueryValue);
                }
                $selectQuery['bool']['should'][0]['match_phrase_prefix']['name']['query'] = $requestQueryValue;
            }
        }
        return $selectQuery;
    }
}
