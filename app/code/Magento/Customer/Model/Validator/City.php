<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Model\Validator;

use Magento\Customer\Model\Customer;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Customer city fields validator.
 */
class City extends AbstractValidator
{
    /**
     * Allowed characters:
     *
     * \p{L}: Unicode letters.
     * \p{M}: Unicode marks (diacritic marks, accents, etc.).
     * \d: Digits (0-9).
     * \s: Whitespace characters (spaces, tabs, newlines, etc.).
     * -: Hyphen.
     * ': Apostrophe mark.
     * .: Period/full stop.
     * ,: Comma.
     * &: Ampersand.
     * (): Parentheses.
     */
    private const PATTERN_CITY = '/^[\p{L}\p{M}\d\s\-\'\.,&\(\)]{1,100}$/u';

    /**
     * Validate city fields.
     *
     * @param Customer $customer
     * @return bool
     */
    public function isValid($customer)
    {
        if (!$this->isValidCity($customer->getCity())) {
            parent::_addMessages([[
                'city' => "Invalid City. Please use letters, numbers, spaces, and the following characters: - ' . , & ( )"
            ]]);
        }

        return count($this->_messages) == 0;
    }

    /**
     * Check if city field is valid.
     *
     * @param string|null $cityValue
     * @return bool
     */
    private function isValidCity($cityValue)
    {
        if ($cityValue != null) {
            if (preg_match(self::PATTERN_CITY, $cityValue, $matches)) {
                return $matches[0] == $cityValue;
            }
        }

        return true;
    }
}
