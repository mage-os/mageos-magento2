<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\TestFramework\Helper;

use Magento\Catalog\Helper\Product\View;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Cache\Type\Layout;
use Magento\Framework\View\EntitySpecificHandlesList;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\Layout\ScheduledStructure;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Model\Layout\Merge;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Assert;

/**
 * Helper for testing layout handles in integration tests with proper isolation
 *
 * This helper provides utilities for testing product layout handles while ensuring
 * proper isolation between test cases by clearing shared Layout instances and cache.
 */
class LayoutHandles
{
    /**
     * Handle prefix for full format attribute set handles
     */
    private const HANDLE_PREFIX_ATTRIBUTE_SET_FULL = 'catalog_product_view_attribute_set_';

    /**
     * Handle prefix for shorthand format attribute set handles
     */
    private const HANDLE_PREFIX_ATTRIBUTE_SET_SHORT = '___attribute_set_';

    /**
     * Handle prefix for full format type handles
     */
    private const HANDLE_PREFIX_TYPE_FULL = 'catalog_product_view_type_';

    /**
     * Handle prefix for shorthand format type handles
     */
    private const HANDLE_PREFIX_TYPE_SHORT = '___type_';

    /**
     * Flag to track if isolation has been performed in current test
     *
     * @var bool
     */
    private static bool $isolationPerformed = false;
    /**
     * Get layout handles for a product with proper isolation
     *
     * This method ensures each product test starts with a clean slate by:
     * 1. Validating product has required data
     * 2. Removing shared Layout instances (if not already done)
     * 3. Removing EntitySpecificHandlesList (accumulates handles)
     * 4. Removing Layout\Merge (caches layout updates)
     * 5. Clearing layout cache
     * 6. Creating fresh Page and Layout instances
     *
     * @param Product $product Product must be saved with ID and attribute set
     * @param bool $forceIsolation Force isolation even if already performed
     * @return string[] Array of layout handle names (e.g., ['default', '___attribute_set_10', ...])
     * @throws \InvalidArgumentException If product is not properly initialized
     */
    public static function getProductLayoutHandles(Product $product, bool $forceIsolation = true): array
    {
        // Validate product has required data
        if (!$product->getId()) {
            throw new \InvalidArgumentException(
                'Product must be saved with an ID before layout handles can be generated'
            );
        }

        if (!$product->getAttributeSetId()) {
            throw new \InvalidArgumentException(
                'Product must have an attribute set ID before layout handles can be generated'
            );
        }

        $objectManager = Bootstrap::getObjectManager();

        // Perform isolation if forced or not yet done
        if ($forceIsolation || !self::$isolationPerformed) {
            self::performIsolation($objectManager);
            self::$isolationPerformed = true;
        }

        // Now create fresh instances
        $viewHelper = $objectManager->get(View::class);
        $resultPage = $objectManager->create(PageFactory::class)->create();

        // Initialize layout for this product
        $viewHelper->initProductLayout($resultPage, $product);

        // Get handles
        return $resultPage->getLayout()->getUpdate()->getHandles();
    }

    /**
     * Perform isolation by removing shared instances and clearing cache
     *
     * This is extracted to allow reuse and avoid repeating expensive operations.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @return void
     */
    private static function performIsolation(\Magento\Framework\ObjectManagerInterface $objectManager): void
    {
        // CRITICAL: Remove ALL shared layout-related instances
        // These accumulate state across multiple initProductLayout calls
        $objectManager->removeSharedInstance(LayoutInterface::class);
        $objectManager->removeSharedInstance(\Magento\Framework\View\Layout::class);
        $objectManager->removeSharedInstance(EntitySpecificHandlesList::class);
        $objectManager->removeSharedInstance(Merge::class);
        $objectManager->removeSharedInstance(ProcessorInterface::class);
        $objectManager->removeSharedInstance(ReaderPool::class);
        $objectManager->removeSharedInstance(GeneratorPool::class);
        $objectManager->removeSharedInstance(ScheduledStructure::class);

        // Remove Result\Page and View helper instances
        $objectManager->removeSharedInstance(PageFactory::class);
        $objectManager->removeSharedInstance(View::class);

        // Clear layout cache to prevent stale data
        /** @var Layout $layoutCache */
        $layoutCache = $objectManager->get(Layout::class);
        $cleanResult = $layoutCache->clean();

        if ($cleanResult === false) {
            trigger_error(
                'Failed to clean layout cache - layout handle tests may be unreliable',
                E_USER_WARNING
            );
        }
    }

    /**
     * Reset the isolation flag for the next test method
     *
     * This should be called in tearDown() to ensure isolation is performed
     * for each test method.
     *
     * @return void
     */
    public static function resetIsolationFlag(): void
    {
        self::$isolationPerformed = false;
    }

    /**
     * Get attribute set handle name for a product (full format)
     *
     * Returns the full handle name. Magento may use shorthand notation internally
     * (e.g., '___attribute_set_10' instead of 'catalog_product_view_attribute_set_10').
     *
     * @param Product $product
     * @return string Example: 'catalog_product_view_attribute_set_10'
     */
    public static function getAttributeSetHandleName(Product $product): string
    {
        return self::HANDLE_PREFIX_ATTRIBUTE_SET_FULL . $product->getAttributeSetId();
    }

    /**
     * Get attribute set handle shorthand name for a product
     *
     * Magento uses shorthand notation where 'catalog_product_view' is replaced with '__'.
     *
     * @param Product $product
     * @return string Example: '___attribute_set_10'
     */
    public static function getAttributeSetHandleShorthand(Product $product): string
    {
        return self::HANDLE_PREFIX_ATTRIBUTE_SET_SHORT . $product->getAttributeSetId();
    }

    /**
     * Get product type handle name (full format)
     *
     * Returns the full handle name for the product's type.
     *
     * @param Product $product
     * @return string Example: 'catalog_product_view_type_simple' or 'catalog_product_view_type_configurable'
     */
    public static function getProductTypeHandleName(Product $product): string
    {
        return self::HANDLE_PREFIX_TYPE_FULL . $product->getTypeId();
    }

    /**
     * Get product type handle shorthand name
     *
     * Magento uses shorthand notation where 'catalog_product_view' is replaced with '__'.
     *
     * @param Product $product
     * @return string Example: '___type_simple' or '___type_configurable'
     */
    public static function getProductTypeHandleShorthand(Product $product): string
    {
        return self::HANDLE_PREFIX_TYPE_SHORT . $product->getTypeId();
    }

    /**
     * Assert product has its attribute set handle
     *
     * Checks for both full handle name and shorthand notation.
     * Magento may use either format depending on context.
     *
     * @param Product $product
     * @param array $handles
     * @param string $message
     * @return void
     */
    public static function assertHasAttributeSetHandle(
        Product $product,
        array $handles,
        string $message = ''
    ): void {
        $expectedHandleFull = self::getAttributeSetHandleName($product);
        $expectedHandleShort = self::getAttributeSetHandleShorthand($product);

        $hasFullHandle = in_array($expectedHandleFull, $handles, true);
        $hasShortHandle = in_array($expectedHandleShort, $handles, true);

        if (empty($message)) {
            $message = sprintf(
                "Product '%s' (ID: %d, AttributeSet: %d) should have handle '%s' or '%s'. "
                . "Available handles: %s",
                $product->getSku(),
                $product->getId(),
                $product->getAttributeSetId(),
                $expectedHandleFull,
                $expectedHandleShort,
                implode(', ', $handles)
            );
        }

        Assert::assertTrue(
            $hasFullHandle || $hasShortHandle,
            $message
        );
    }

    /**
     * Assert product does NOT have another product's attribute set handle
     *
     * Checks that neither the full handle name nor shorthand notation is present.
     *
     * @param Product $productToCheck Product whose handles to verify
     * @param Product $otherProduct Product whose handles should NOT be present
     * @param array $handles Handles to check
     * @param string $message Custom assertion message
     * @return void
     */
    public static function assertDoesNotHaveOtherAttributeSetHandle(
        Product $productToCheck,
        Product $otherProduct,
        array $handles,
        string $message = ''
    ): void {
        $otherHandleFull = self::getAttributeSetHandleName($otherProduct);
        $otherHandleShort = self::getAttributeSetHandleShorthand($otherProduct);

        $hasFullHandle = in_array($otherHandleFull, $handles, true);
        $hasShortHandle = in_array($otherHandleShort, $handles, true);

        if (empty($message)) {
            $message = sprintf(
                "Product '%s' should NOT have handle '%s' or '%s' from product '%s'",
                $productToCheck->getSku(),
                $otherHandleFull,
                $otherHandleShort,
                $otherProduct->getSku()
            );
        }

        Assert::assertFalse(
            $hasFullHandle || $hasShortHandle,
            $message
        );
    }

    /**
     * Assert product has its product type handle
     *
     * Checks for both full handle name and shorthand notation.
     * Works with any product type (simple, configurable, bundle, etc.).
     *
     * @param Product $product Product to check
     * @param array $handles Layout handles to verify
     * @param string $message Custom assertion message
     * @return void
     */
    public static function assertHasProductTypeHandle(
        Product $product,
        array $handles,
        string $message = ''
    ): void {
        $expectedHandleFull = self::getProductTypeHandleName($product);
        $expectedHandleShort = self::getProductTypeHandleShorthand($product);

        $hasFullHandle = in_array($expectedHandleFull, $handles, true);
        $hasShortHandle = in_array($expectedHandleShort, $handles, true);

        if (empty($message)) {
            $message = sprintf(
                "Product '%s' (Type: %s) should have type handle '%s' or '%s'. "
                . "Available handles: %s",
                $product->getSku(),
                $product->getTypeId(),
                $expectedHandleFull,
                $expectedHandleShort,
                implode(', ', $handles)
            );
        }

        Assert::assertTrue(
            $hasFullHandle || $hasShortHandle,
            $message
        );
    }
}
