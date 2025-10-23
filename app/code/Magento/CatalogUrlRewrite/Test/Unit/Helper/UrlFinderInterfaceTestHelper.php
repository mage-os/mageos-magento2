<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogUrlRewrite\Test\Unit\Helper;

use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Mock class for UrlFinderInterface with all required methods
 */
class UrlFinderInterfaceTestHelper implements UrlFinderInterface
{
    /**
     * Mock method for findOneByData
     *
     * @param array $data
     * @return UrlRewrite|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function findOneByData(array $data): ?UrlRewrite
    {
        return null;
    }

    /**
     * Mock method for findAllByData
     *
     * @param array $data
     * @return UrlRewrite[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function findAllByData(array $data): array
    {
        return [];
    }
}
