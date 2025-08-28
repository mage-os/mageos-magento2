<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Customer\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class ValidateDobOnSave
{
    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var JsonSerializer
     */
    private $json;

    public function __construct(
        EavConfig $eavConfig,
        JsonSerializer $json
    ) {
        $this->eavConfig = $eavConfig;
        $this->json = $json;
    }

    public function aroundSave(
        CustomerRepositoryInterface $subject,
        callable $proceed,
        CustomerInterface $customer,
        $passwordHash = null
    ) {
        $dobRaw = $customer->getDob();

        $dobDate = $this->parseDate($dobRaw);
        if ($dobRaw !== null && $dobRaw !== '' && !$dobDate) {
            throw new InputException(__('Date of Birth is invalid.'));
        }

        if ($dobDate) {
            $attr = $this->eavConfig->getAttribute('customer', 'dob');

            $rules = $attr->getData('validate_rules');
            if (is_string($rules) && $rules !== '') {
                try {
                    $rules = $this->json->unserialize($rules);
                } catch (\InvalidArgumentException $e) {
                    $rules = [];
                }
            }
            if (!is_array($rules)) {
                $rules = (array)$attr->getValidateRules();
            }

            $min = $rules['date_range_min'] ?? $rules['min_date'] ?? null;
            $max = $rules['date_range_max'] ?? $rules['max_date'] ?? null;

            $minDate = $this->parseDate($min);
            $maxDate = $this->parseDate($max);

            $dobKey = $dobDate->format('Y-m-d');

            if ($minDate && $dobKey < $minDate->format('Y-m-d')) {
                throw new InputException(__('Date of Birth must be on or after %1.', $minDate->format('Y-m-d')));
            }
            if ($maxDate && $dobKey > $maxDate->format('Y-m-d')) {
                throw new InputException(__('Date of Birth must be on or before %1.', $maxDate->format('Y-m-d')));
            }
        }

        return $proceed($customer, $passwordHash);
    }

    /**
     * @param $value
     * @return \DateTimeImmutable|null
     * @throws \Exception
     */
    private function parseDate($value): ?\DateTimeImmutable
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }
        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            $intVal = (int)$value;
            if ($intVal <= 0) {
                return null;
            }
            $seconds = ($intVal >= 10000000000) ? intdiv($intVal, 1000) : $intVal;
            return (new \DateTimeImmutable('@' . $seconds))->setTimezone(new \DateTimeZone('UTC'));
        }

        try {
            return new \DateTimeImmutable((string)$value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
