<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch8\Model\Adapter\DynamicTemplates;

/**
 * @inheridoc
 * @deprecated Elasticsearch8 is no longer supported by Adobe
 * @see this class will be responsible for ES8 only
 */
class PositionMapper implements MapperInterface
{
    /**
     * @inheritdoc
     */
    public function processTemplates(array $templates): array
    {
        $templates[] = [
            'position_mapping' => [
                'match' => 'position_*',
                'match_mapping_type' => 'string',
                'mapping' => [
                    'type' => 'integer',
                    'index' => true,
                ],
            ],
        ];

        return $templates;
    }
}
