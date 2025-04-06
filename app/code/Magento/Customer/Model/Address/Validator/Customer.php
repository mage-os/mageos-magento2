<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Model\Address\Validator;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\ValidatorInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;

/**
 * Validates that current Address is related to given Customer.
 */
class Customer implements ValidatorInterface
{
    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @param AddressFactory $addressFactory
     */
    public function __construct(AddressFactory $addressFactory)
    {
        $this->addressFactory = $addressFactory;
    }

    /**
     * @inheritDoc
     */
    public function validate(AbstractAddress $address): array
    {
        $errors = [];
        $addressId = $address instanceof QuoteAddressInterface ? $address->getCustomerAddressId() : $address->getId();
        if ($addressId !== null) {
            $addressCustomerId = (int) $address->getCustomerId();
            $originalAddressCustomerId = (int) $this->addressFactory->create()
                ->load($addressId)
                ->getCustomerId();

            if ($originalAddressCustomerId !== 0 && $originalAddressCustomerId !== $addressCustomerId) {
                $errors[] = __('A customer with the same email address already exists in an associated website.');
            }
        }

        return $errors;
    }
}
