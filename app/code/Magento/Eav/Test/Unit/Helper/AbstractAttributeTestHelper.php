<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Eav\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

/**
 * Helper class for testing AbstractAttribute with custom methods
 *
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
class AbstractAttributeTestHelper extends AbstractAttribute
{
    /**
     * @var int|null
     */
    private $gridFilterConditionType;

    /**
     * @var bool
     */
    private $isVisible = false;

    /**
     * @var string|null
     */
    private $storeLabel;

    /**
     * @var string|array|null
     */
    private $validateRules;

    /**
     * @var array
     */
    private $usedInForms = [];

    /**
     * Constructor
     *
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Get grid filter condition type
     *
     * @return int|null
     */
    public function getGridFilterConditionType(): ?int
    {
        return $this->gridFilterConditionType;
    }

    /**
     * Set grid filter condition type
     *
     * @param int|null $gridFilterConditionType
     * @return $this
     */
    public function setGridFilterConditionType(?int $gridFilterConditionType): self
    {
        $this->gridFilterConditionType = $gridFilterConditionType;
        return $this;
    }

    /**
     * Get is visible
     *
     * @return bool
     */
    public function getIsVisible(): bool
    {
        return $this->isVisible;
    }

    /**
     * Set is visible
     *
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible(bool $isVisible): self
    {
        $this->isVisible = $isVisible;
        return $this;
    }

    /**
     * Get store label
     *
     * @return string|null
     */
    public function getStoreLabel(): ?string
    {
        return $this->storeLabel;
    }

    /**
     * Set store label
     *
     * @param string|null $storeLabel
     * @return $this
     */
    public function setStoreLabel(?string $storeLabel): self
    {
        $this->storeLabel = $storeLabel;
        return $this;
    }

    /**
     * Get validate rules
     *
     * @return string|array|null
     */
    public function getValidateRules()
    {
        return $this->validateRules;
    }

    /**
     * Set validate rules
     *
     * @param string|array|null $validateRules
     * @return $this
     */
    public function setValidateRules($validateRules): self
    {
        $this->validateRules = $validateRules;
        return $this;
    }

    /**
     * Get used in forms
     *
     * @return array
     */
    public function getUsedInForms(): array
    {
        return $this->usedInForms;
    }

    /**
     * Set used in forms
     *
     * @param array $usedInForms
     * @return $this
     */
    public function setUsedInForms(array $usedInForms): self
    {
        $this->usedInForms = $usedInForms;
        return $this;
    }
}
