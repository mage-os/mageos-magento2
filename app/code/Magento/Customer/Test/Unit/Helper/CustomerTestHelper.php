<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Customer;

class CustomerTestHelper extends Customer
{
    /**
     * @var array<string, mixed>
     */
    private $data = [];

    /**
     * Constructor - skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get telephone
     *
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->data['telephone'] ?? null;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone(string $telephone)
    {
        $this->data['telephone'] = $telephone;
        return $this;
    }

    /**
     * Get street
     *
     * @return array<int, string>|null
     */
    public function getStreet()
    {
        return $this->data['street'] ?? null;
    }

    /**
     * Set street
     *
     * @param array<int, string> $street
     * @return $this
     */
    public function setStreet(array $street)
    {
        $this->data['street'] = $street;
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->data['firstname'] ?? null;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return $this
     */
    public function setFirstname(string $firstname)
    {
        $this->data['firstname'] = $firstname;
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string|null
     */
    public function getLastname()
    {
        return $this->data['lastname'] ?? null;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return $this
     */
    public function setLastname(string $lastname)
    {
        $this->data['lastname'] = $lastname;
        return $this;
    }

    /**
     * Get middlename
     *
     * @return string|null
     */
    public function getMiddlename()
    {
        return $this->data['middlename'] ?? null;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     * @return $this
     */
    public function setMiddlename(string $middlename)
    {
        $this->data['middlename'] = $middlename;
        return $this;
    }

    /**
     * Get city
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->data['city'] ?? null;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return $this
     */
    public function setCity(string $city)
    {
        $this->data['city'] = $city;
        return $this;
    }
}
