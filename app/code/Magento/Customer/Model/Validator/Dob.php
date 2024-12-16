<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Model\Validator;

use Magento\Customer\Model\Customer;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Customer dob field validator.
 */
class Dob extends AbstractValidator
{
    /**
     * @var \DateTime
     */
    private \DateTime $currentDate;

   /**
    * @var StoreManagerInterface
    */
    private StoreManagerInterface $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->currentDate = new \DateTime();
        $this->storeManager = $storeManager;
    }

    /**
     * Validate name fields.
     *
     * @param Customer $customer
     * @return bool
     */
    public function isValid($customer): bool
    {
        if (!$this->isValidDob($customer->getDob(), $customer->getStoreId())) {
            parent::_addMessages([['dob' => 'The Date of Birth should not be greater than today.']]);
        }

        return count($this->_messages) == 0;
    }

    /**
     * Check if specified dob is not in the future
     *
     * @param string|null $dobValue
     * @param int $storeId
     * @return bool
     */
    private function isValidDob(?string $dobValue, int $storeId): bool
    {
        if ($dobValue != null) {

            // Get the timezone of the store
            $store = $this->storeManager->getStore($storeId);
            $timezone = $store->getConfig('general/locale/timezone');

            // Get the date of birth and set the time to 00:00:00
            $dobDate = new \DateTime($dobValue, new \DateTimeZone($timezone));
            $dobDate->setTime(0, 0, 0);

            // Get the timestamp of the date of birth and the current date
            $dobTimestamp = $dobDate->getTimestamp();
            $currentTimestamp = $this->currentDate->getTimestamp();

            // If the date's of birth first minute is in the future, return false - the day has not started yet
            if ($dobTimestamp > $currentTimestamp) {
                return false;
            }
        }

        return true;
    }
}
