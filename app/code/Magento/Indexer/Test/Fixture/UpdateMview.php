<?php
/************************************************************************
 *
 * Copyright 2024 Adobe
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
 * ************************************************************************
 */
declare(strict_types=1);

namespace Magento\Indexer\Test\Fixture;

use Magento\Framework\DataObject;
use Magento\Indexer\Model\Processor;
use Magento\TestFramework\Fixture\DataFixtureInterface;

class UpdateMview implements DataFixtureInterface
{
    /**
     * @param Processor $processor
     */
    public function __construct(
        private readonly Processor $processor
    ) {
    }

    /**
     * @inheritDoc
     */
    public function apply(array $data = []): ?DataObject
    {
        $this->processor->updateMview();
        return null;
    }
}
