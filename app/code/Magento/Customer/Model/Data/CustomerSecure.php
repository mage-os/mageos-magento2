<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */

namespace Magento\Customer\Model\Data;

/**
 * Class containing secure customer data that cannot be exposed as part of \Magento\Customer\Api\Data\CustomerInterface
 *
 * @method string getRpToken()
 * @method string getRpTokenCreatedAt()
 * @method string getPasswordHash()
 * @method string getDeleteable()
 * @method setRpToken(string $rpToken)
 * @method setRpTokenCreatedAt(string $rpTokenCreatedAt)
 * @method setPasswordHash(string $hashedPassword)
 * @method setDeleteable(bool $deleteable)
 */
class CustomerSecure extends \Magento\Framework\DataObject
{
}
