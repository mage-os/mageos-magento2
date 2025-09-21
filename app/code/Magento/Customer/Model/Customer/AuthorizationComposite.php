<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Model\Customer;

use Magento\Framework\AuthorizationInterface;

/**
 * Class to invalidate user credentials
 */
class AuthorizationComposite implements AuthorizationInterface
{
    /**
     * @var AuthorizationInterface[]
     */
    private $authorizationChecks;

    /**
     * AuthorizationComposite constructor.
     *
     * @param AuthorizationInterface[] $authorizationChecks
     */
    public function __construct(
        array $authorizationChecks
    ) {
        $this->authorizationChecks = $authorizationChecks;
    }

    /**
     * @inheritdoc
     */
    public function isAllowed($resource, $privilege = null)
    {
        $result = false;

        foreach ($this->authorizationChecks as $authorizationCheck) {
            $result = $authorizationCheck->isAllowed($resource, $privilege);
            if (!$result) {
                break;
            }
        }

        return $result;
    }
}
