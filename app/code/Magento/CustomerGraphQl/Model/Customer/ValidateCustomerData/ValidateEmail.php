<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CustomerGraphQl\Model\Customer\ValidateCustomerData;

use Magento\CustomerGraphQl\Api\ValidateCustomerDataInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Validator\EmailAddress as EmailAddressValidator;

/**
 * Validates an email
 */
class ValidateEmail implements ValidateCustomerDataInterface
{
    /**
     * @var EmailAddressValidator
     */
    private $emailAddressValidator;

    /**
     * ValidateEmail constructor.
     *
     * @param EmailAddressValidator $emailAddressValidator
     */
    public function __construct(EmailAddressValidator $emailAddressValidator)
    {
        $this->emailAddressValidator = $emailAddressValidator;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $customerData): void
    {
        if (isset($customerData['email']) && !$this->emailAddressValidator->isValid($customerData['email'])) {
            throw new GraphQlInputException(
                __('"%1" is not a valid email address.', $customerData['email'])
            );
        }
    }
}
