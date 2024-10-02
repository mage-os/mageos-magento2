<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Theme\Plugin;

use Magento\Config\Model\Config\PathValidator;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Framework\Exception\ValidatorException;
use Magento\Theme\Model\DesignConfigRepository;

class ValidateDesignConfig
{
    /**
     * @param Structure $structure
     * @param DesignConfigRepository $designConfigRepository
     */
    public function __construct(
        private readonly Structure $structure,
        private readonly DesignConfigRepository $designConfigRepository
    ) {
    }

    /**
     * Allow setting design configuration from cli
     *
     * @param PathValidator $subject
     * @param callable $proceed
     * @param string $path
     * @return true
     * @throws ValidatorException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundValidate(PathValidator $subject, callable $proceed, $path): bool
    {
        if (stripos($path, 'design/') !== 0) {
            return $proceed($path);
        }

        $element = $this->structure->getElementByConfigPath($path);
        if ($element instanceof Field && $element->getConfigPath()) {
            $path = $element->getConfigPath();
        }

        $allPaths = array_merge($this->structure->getFieldPaths(), $this->getDesignFieldPaths());

        if (!array_key_exists($path, $allPaths)) {
            throw new ValidatorException(__('The "%1" path doesn\'t exist. Verify and try again.', $path));
        }
        return true;
    }

    /**
     * Get design configuration field paths
     *
     * @return array
     */
    private function getDesignFieldPaths(): array
    {
        $designConfig = $this->designConfigRepository->getByScope('default', null);
        $fieldsData = $designConfig->getExtensionAttributes()->getDesignConfigData();
        $data = [];
        foreach ($fieldsData as $fieldData) {
            $data[$fieldData->getFieldConfig()['path']] = [$fieldData->getFieldConfig()['path']];
        }
        return $data;
    }
}
