<?php
/************************************************************************
 *
 * Copyright 2023 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 * ***********************************************************************
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Fixture;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Framework\Exception\InvalidArgumentException;
use Magento\Framework\DataObject;
use Magento\TestFramework\Fixture\DataFixtureInterface;

/**
 * Assigning product to categories
 */
class AssignCategories implements DataFixtureInterface
{
    private const PRODUCT = 'product';
    private const CATEGORIES = 'categories';

    /**
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     */
    public function __construct(
        private readonly CategoryLinkManagementInterface $categoryLinkManagement
    ) {
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function apply(array $data = []): ?DataObject
    {
        if (empty($data[self::PRODUCT])) {
            throw new InvalidArgumentException(__('"%field" is required', ['field' => self::PRODUCT]));
        }
        if (empty($data[self::CATEGORIES])) {
            throw new InvalidArgumentException(__('"%field" is required', ['field' => self::CATEGORIES]));
        }
        if (!is_array($data[self::CATEGORIES])) {
            throw new InvalidArgumentException(__('"%field" must be an array', ['field' => self::CATEGORIES]));
        }

        $this->categoryLinkManagement->assignProductToCategories(
            $data[self::PRODUCT]->getSku(),
            array_map(fn ($category) => $category->getId(), $data[self::CATEGORIES])
        );

        return null;
    }
}
