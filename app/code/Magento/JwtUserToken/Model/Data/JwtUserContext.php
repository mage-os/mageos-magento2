<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\JwtUserToken\Model\Data;

use Magento\Authorization\Model\UserContextInterface;

class JwtUserContext implements UserContextInterface
{
    /**
     * @var int|null
     *
     * phpcs:disable Magento2.Commenting.ClassPropertyPHPDocFormatting
     */
    private readonly ?int $userId;

    /**
     * @var int|null
     *
     * phpcs:disable Magento2.Commenting.ClassPropertyPHPDocFormatting
     */
    private readonly ?int $userType;

    /**
     * @param int|null $userId
     * @param int|null $userType
     */
    public function __construct(?int $userId, ?int $userType)
    {
        $this->userId = $userId;
        $this->userType = $userType;
    }

    /**
     * @inheritDoc
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @inheritDoc
     */
    public function getUserType()
    {
        return $this->userType;
    }
}
