<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Model\Validator;

use DateTimeZone;
use Magento\Customer\Model\Customer;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Customer dob field validator.
 */
class Dob extends AbstractValidator
{
    /**
     * Validate dob field.
     *
     * @param Customer $customer
     * @return bool
     */
    public function isValid($customer): bool
    {
        $timezone = new DateTimeZone($customer->getStore()->getConfig('general/locale/timezone'));
        if (!$this->isValidDob($customer->getDob(), $timezone)) {
            $this->_addMessages([['dob' => 'The Date of Birth should not be greater than today.']]);
        }

        return count($this->_messages) === 0;
    }

    /**
     * Check if specified dob is not in the future
     *
     * @param string|null $dobValue
     * @param DateTimeZone $timezone
     * @return bool
     */
    private function isValidDob(?string $dobValue, ?DateTimeZone $timezone = null): bool
    {
        if ($dobValue != null) {

            // Get the date of birth and set the time to 00:00:00
            $dobDate = new \DateTime($dobValue, $timezone);
            $dobDate->setTime(0, 0, 0);

            // Get the timestamp of the date of birth and the current date
            $dobTimestamp = $dobDate->getTimestamp();
            $currentTimestamp = time();

            // If the date's of birth first minute is in the future, return false - the day has not started yet
            return ($dobTimestamp <= $currentTimestamp);
        }

        return true;
    }
}
