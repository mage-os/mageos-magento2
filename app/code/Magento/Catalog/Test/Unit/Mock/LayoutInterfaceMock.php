<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Mock;

use Magento\Framework\View\LayoutInterface;

/**
 * Mock class for LayoutInterface with additional methods
 */
class LayoutInterfaceMock implements LayoutInterface
{
    /**
     * Mock method for initMessages
     *
     * @return void
     */
    public function initMessages(): void
    {
        // Mock implementation
    }

    // Required methods from LayoutInterface
    public function getBlock($name): ?\Magento\Framework\View\Element\BlockInterface
    {
        return null;
    }

    public function getChildName($parentName, $alias): ?string
    {
        return null;
    }

    public function setChild($parentName, $elementName, $alias): LayoutInterface
    {
        return $this;
    }

    public function reorderChild($parentName, $childName, $offsetOrSibling, $after = true): LayoutInterface
    {
        return $this;
    }

    public function unsetChild($parentName, $childName): LayoutInterface
    {
        return $this;
    }

    public function getChildNames($parentName): array
    {
        return [];
    }

    public function getChildBlocks($parentName): array
    {
        return [];
    }

    public function getChildHtml($name = '', $useCache = true, $sorted = false): string
    {
        return '';
    }

    public function getChildChildHtml($parentName, $childName = '', $useCache = true): string
    {
        return '';
    }

    public function getBlockHtml($name): string
    {
        return '';
    }

    public function insert($element, $siblingName = '', $after = true, $alias = ''): LayoutInterface
    {
        return $this;
    }

    public function append($element, $alias = ''): LayoutInterface
    {
        return $this;
    }

    public function prepend($element, $alias = ''): LayoutInterface
    {
        return $this;
    }

    public function addOutputElement($name): LayoutInterface
    {
        return $this;
    }

    public function removeOutputElement($name): LayoutInterface
    {
        return $this;
    }

    public function getOutput(): string
    {
        return '';
    }

    public function getMessagesBlock($group = ''): ?\Magento\Framework\View\Element\Messages
    {
        return null;
    }

    public function getUpdate(): ?\Magento\Framework\View\Layout\ProcessorInterface
    {
        return null;
    }

    public function generateXml(): LayoutInterface
    {
        return $this;
    }

    public function generateElements(): LayoutInterface
    {
        return $this;
    }

    public function addToParentGroup($blockName, $parentGroupName): bool
    {
        return true;
    }

    public function getElementProperty($name, $attribute): ?string
    {
        return null;
    }

    public function isBlock($name): bool
    {
        return false;
    }

    public function isContainer($name): bool
    {
        return false;
    }

    public function isManipulationAllowed($name): bool
    {
        return true;
    }

    public function setBlock($name, $block): LayoutInterface
    {
        return $this;
    }

    public function renameElement($oldName, $newName): LayoutInterface
    {
        return $this;
    }

    public function addContainer($name, $label, $options = [], $parent = '', $alias = ''): LayoutInterface
    {
        return $this;
    }

    public function getContainer($name): ?\Magento\Framework\View\Element\Container
    {
        return null;
    }

    public function renderElement($name, $useCache = true): string
    {
        return '';
    }

    public function renderNonCachedElement($name): string
    {
        return '';
    }

    public function addAdjustableRenderer($namespace, $xmlType, $blockType, $template = ''): LayoutInterface
    {
        return $this;
    }

    public function getRenderer($namespace, $xmlType): ?\Magento\Framework\View\Element\AbstractBlock
    {
        return null;
    }

    public function isCacheable(): bool
    {
        return true;
    }

    public function setIsCacheable($isCacheable): LayoutInterface
    {
        return $this;
    }

    public function getReaderContext(): ?\Magento\Framework\View\Layout\Reader\Context
    {
        return null;
    }

    public function setReaderContext($readerContext): LayoutInterface
    {
        return $this;
    }

    public function getGeneratorContext(): ?\Magento\Framework\View\Layout\Generator\Context
    {
        return null;
    }

    public function setGeneratorContext($generatorContext): LayoutInterface
    {
        return $this;
    }

    public function getXmlString(): string
    {
        return '';
    }

    public function setXmlString($xmlString): LayoutInterface
    {
        return $this;
    }

    public function getCacheableElements(): array
    {
        return [];
    }

    public function setCacheableElements($cacheableElements): LayoutInterface
    {
        return $this;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function hasElement($name): bool
    {
        return false;
    }

    public function unsetElement($name): LayoutInterface
    {
        return $this;
    }

    public function getAllBlocks(): array
    {
        return [];
    }

    public function getChildBlock($parentName, $alias)
    {
        return null;
    }

    public function getGroupChildNames($blockName, $groupName): array
    {
        return [];
    }

    public function getParentName($childName)
    {
        return false;
    }

    public function createBlock($type, $name = '', array $arguments = [])
    {
        return null;
    }

    public function addBlock($block, $name = '', $parent = '', $alias = '')
    {
        return null;
    }

    public function getElementAlias($name)
    {
        return false;
    }

    public function getBlockSingleton($type)
    {
        return null;
    }
}
