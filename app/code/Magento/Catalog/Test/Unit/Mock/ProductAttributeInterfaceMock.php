<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Mock;

use Magento\Catalog\Api\Data\ProductAttributeInterface;

/**
 * Mock class for ProductAttributeInterface with additional methods
 */
class ProductAttributeInterfaceMock implements ProductAttributeInterface
{
    /**
     * Mock method for getId
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return null;
    }

    /**
     * Mock method for get
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return null;
    }

    /**
     * Mock method for getBackendTypeByInput
     *
     * @param string $inputType
     * @return string
     */
    public function getBackendTypeByInput(string $inputType): string
    {
        return 'varchar';
    }

    /**
     * Mock method for getDefaultValueByInput
     *
     * @param string $inputType
     * @return mixed
     */
    public function getDefaultValueByInput(string $inputType)
    {
        return null;
    }

    /**
     * Mock method for addData
     *
     * @param array $data
     * @return $this
     */
    public function addData(array $data): self
    {
        return $this;
    }

    /**
     * Mock method for save
     *
     * @return $this
     */
    public function save(): self
    {
        return $this;
    }

    // Required methods from ProductAttributeInterface
    public function getAttributeId(): ?int
    {
        return null;
    }

    public function setAttributeId($attributeId): self
    {
        return $this;
    }

    public function getAttributeCode(): ?string
    {
        return null;
    }

    public function setAttributeCode($attributeCode): self
    {
        return $this;
    }

    public function getFrontendInput(): ?string
    {
        return null;
    }

    public function setFrontendInput($frontendInput): self
    {
        return $this;
    }

    public function getEntityTypeId(): ?int
    {
        return null;
    }

    public function setEntityTypeId($entityTypeId): self
    {
        return $this;
    }

    public function getIsRequired(): ?bool
    {
        return null;
    }

    public function setIsRequired($isRequired): self
    {
        return $this;
    }

    public function getDefaultValue(): ?string
    {
        return null;
    }

    public function setDefaultValue($defaultValue): self
    {
        return $this;
    }

    public function getIsUnique(): ?bool
    {
        return null;
    }

    public function setIsUnique($isUnique): self
    {
        return $this;
    }

    public function getNote(): ?string
    {
        return null;
    }

    public function setNote($note): self
    {
        return $this;
    }

    public function getFrontendClass(): ?string
    {
        return null;
    }

    public function setFrontendClass($frontendClass): self
    {
        return $this;
    }

    public function getBackendType(): ?string
    {
        return null;
    }

    public function setBackendType($backendType): self
    {
        return $this;
    }

    public function getBackendModel(): ?string
    {
        return null;
    }

    public function setBackendModel($backendModel): self
    {
        return $this;
    }

    public function getSourceModel(): ?string
    {
        return null;
    }

    public function setSourceModel($sourceModel): self
    {
        return $this;
    }

    public function getOptions(): ?array
    {
        return null;
    }

    public function setOptions(?array $options = null): self
    {
        return $this;
    }

    public function getDefaultFrontendLabel(): ?string
    {
        return null;
    }

    public function setDefaultFrontendLabel($defaultFrontendLabel): self
    {
        return $this;
    }

    public function getFrontendLabels(): ?array
    {
        return null;
    }

    public function setFrontendLabels(?array $frontendLabels = null): self
    {
        return $this;
    }

    public function getValidationRules(): ?array
    {
        return null;
    }

    public function setValidationRules(?array $validationRules = null): self
    {
        return $this;
    }

    public function getIsUserDefined(): ?bool
    {
        return null;
    }

    public function setIsUserDefined($isUserDefined): self
    {
        return $this;
    }

    public function getSortOrder(): ?int
    {
        return null;
    }

    public function setSortOrder($sortOrder): self
    {
        return $this;
    }

    public function getFrontendLabel(): ?string
    {
        return null;
    }

    public function setFrontendLabel($frontendLabel): self
    {
        return $this;
    }

    // EavAttributeInterface methods
    public function getIsWysiwygEnabled(): ?bool
    {
        return null;
    }

    public function setIsWysiwygEnabled($isWysiwygEnabled): self
    {
        return $this;
    }

    public function getIsHtmlAllowedOnFront(): ?bool
    {
        return null;
    }

    public function setIsHtmlAllowedOnFront($isHtmlAllowedOnFront): self
    {
        return $this;
    }

    public function getUsedForSortBy(): ?bool
    {
        return null;
    }

    public function setUsedForSortBy($usedForSortBy): self
    {
        return $this;
    }

    public function getIsFilterable(): ?bool
    {
        return null;
    }

    public function setIsFilterable($isFilterable): self
    {
        return $this;
    }

    public function getIsFilterableInSearch(): ?bool
    {
        return null;
    }

    public function setIsFilterableInSearch($isFilterableInSearch): self
    {
        return $this;
    }

    public function getIsUsedInGrid(): ?bool
    {
        return null;
    }

    public function setIsUsedInGrid($isUsedInGrid): self
    {
        return $this;
    }

    public function getIsVisibleInGrid(): ?bool
    {
        return null;
    }

    public function setIsVisibleInGrid($isVisibleInGrid): self
    {
        return $this;
    }

    public function getIsFilterableInGrid(): ?bool
    {
        return null;
    }

    public function setIsFilterableInGrid($isFilterableInGrid): self
    {
        return $this;
    }

    public function getPosition(): ?int
    {
        return null;
    }

    public function setPosition($position): self
    {
        return $this;
    }

    public function getApplyTo(): ?array
    {
        return null;
    }

    public function setApplyTo($applyTo): self
    {
        return $this;
    }

    public function getIsSearchable(): ?string
    {
        return null;
    }

    public function setIsSearchable($isSearchable): self
    {
        return $this;
    }

    public function getIsVisibleInAdvancedSearch(): ?string
    {
        return null;
    }

    public function setIsVisibleInAdvancedSearch($isVisibleInAdvancedSearch): self
    {
        return $this;
    }

    public function getIsComparable(): ?string
    {
        return null;
    }

    public function setIsComparable($isComparable): self
    {
        return $this;
    }

    public function getIsUsedForPromoRules(): ?string
    {
        return null;
    }

    public function setIsUsedForPromoRules($isUsedForPromoRules): self
    {
        return $this;
    }

    public function getIsVisibleOnFront(): ?string
    {
        return null;
    }

    public function setIsVisibleOnFront($isVisibleOnFront): self
    {
        return $this;
    }

    public function getUsedInProductListing(): ?string
    {
        return null;
    }

    public function setUsedInProductListing($usedInProductListing): self
    {
        return $this;
    }

    public function getIsVisible(): ?bool
    {
        return null;
    }

    public function setIsVisible($isVisible): self
    {
        return $this;
    }

    public function getScope(): ?string
    {
        return null;
    }

    public function setScope($scope): self
    {
        return $this;
    }

    public function getExtensionAttributes(): ?\Magento\Eav\Api\Data\AttributeExtensionInterface
    {
        return null;
    }

    public function setExtensionAttributes(\Magento\Eav\Api\Data\AttributeExtensionInterface $extensionAttributes): self
    {
        return $this;
    }

    // CustomAttributesDataInterface methods
    public function getCustomAttribute($attributeCode): ?\Magento\Framework\Api\AttributeValueInterface
    {
        return null;
    }

    public function setCustomAttribute($attributeCode, $attributeValue): self
    {
        return $this;
    }

    public function getCustomAttributes(): ?array
    {
        return null;
    }

    public function setCustomAttributes(?array $attributes): self
    {
        return $this;
    }

    // MetadataObjectInterface methods
    public function getMetadata(): ?\Magento\Framework\Api\MetadataObjectInterface
    {
        return null;
    }

    public function setMetadata(?\Magento\Framework\Api\MetadataObjectInterface $metadata): self
    {
        return $this;
    }
}
