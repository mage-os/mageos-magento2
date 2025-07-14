<?php
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Cms\Model\Page;

/**
 * Provides extension point to generate target url for url builder class
 */
interface TargetUrlBuilderInterface
{
    /**
     * Get target url from the route and store code
     *
     * @param string $routePath
     * @param string $store
     * @return string
     */
    public function process(string $routePath, string $store): string;
}
