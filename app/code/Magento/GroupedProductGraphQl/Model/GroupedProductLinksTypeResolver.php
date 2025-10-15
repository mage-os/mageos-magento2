<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GroupedProductGraphQl\Model;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

/**
 * @inheritdoc
 */
class GroupedProductLinksTypeResolver implements TypeResolverInterface
{
    /**
     * @var string[]
     */
    private $linkTypes = ['associated'];

    /**
     * @inheritdoc
     */
    public function resolveType(array $data): string
    {
        if (isset($data['link_type'])) {
            $linkType = $data['link_type'];
            if (in_array($linkType, $this->linkTypes)) {
                return 'ProductLinks';
            }
        }
        return '';
    }
}
