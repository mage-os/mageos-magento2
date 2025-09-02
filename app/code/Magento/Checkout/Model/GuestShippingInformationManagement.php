<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Checkout\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Validator\Factory as ValidatorFactory;

class GuestShippingInformationManagement implements \Magento\Checkout\Api\GuestShippingInformationManagementInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Magento\Checkout\Api\ShippingInformationManagementInterface
     */
    protected $shippingInformationManagement;

    /**
     * @var ValidatorFactory
     */
    private $validatorFactory;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
     * @param ValidatorFactory $validatorFactory
     * @param AddressFactory $addressFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
        ValidatorFactory $validatorFactory,
        AddressFactory $addressFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->validatorFactory = $validatorFactory;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @inheritDoc
     */
    public function saveAddressInformation(
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $shippingAddress = $addressInformation->getShippingAddress();
        if ($shippingAddress) {
            $this->validateAddressAttributes($shippingAddress);
        }
        $billingAddress = $addressInformation->getBillingAddress();
        if ($billingAddress) {
            $this->validateAddressAttributes($billingAddress);
        }

        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->shippingInformationManagement->saveAddressInformation(
            (int) $quoteIdMask->getQuoteId(),
            $addressInformation
        );
    }

    /**
     * Validate address attributes using customer_address validator
     *
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return void
     * @throws InputException
     */
    private function validateAddressAttributes($address): void
    {
        try {
            $customerAddress = $this->addressFactory->create();
            $customerAddress->setData([
                'firstname' => $address->getFirstname(),
                'lastname' => $address->getLastname(),
                'street' => $address->getStreet(),
                'city' => $address->getCity(),
                'region' => $address->getRegion(),
                'region_id' => $address->getRegionId(),
                'region_code' => $address->getRegionCode(),
                'postcode' => $address->getPostcode(),
                'country_id' => $address->getCountryId(),
                'telephone' => $address->getTelephone(),
                'company' => $address->getCompany(),
                'email' => $address->getEmail()
            ]);
            $validator = $this->validatorFactory->createValidator('customer_address', 'save');
            if (!$validator->isValid($customerAddress)) {
                $errorMessages = [];
                $messages = $validator->getMessages();
                foreach ($messages as $message) {
                    if (is_array($message)) {
                        foreach ($message as $msg) {
                            $errorMessages[] = $msg;
                        }
                    } else {
                        $errorMessages[] = $message;
                    }
                }
                throw new InputException(
                    __('The address contains invalid data: %1', implode(', ', $errorMessages))
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new InputException(__($e->getMessage()));
        }
    }
}
