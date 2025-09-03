# Magento_MsrpGroupedProduct module

This module provides type and resolver information for the Magento_Msrp module from the Magento_GroupedProduct module.
Provides implementation of MSRP price calculation for Grouped Product.

## Installation

For information about a module installation, see [Enable or disable modules](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/tutorials/manage-modules)

## Structure

`Pricing\` - directory contains implementation of MSRP price calculation
for Configurable Product (`Magento\MsrpConfigurableProduct\Pricing\MsrpPriceCalculator` class).

For information about a typical file structure of a module,
 see [Module file structure](https://developer.adobe.com/commerce/php/development/build/component-file-structure/#module-file-structure).

## Extensibility

Extension developers can interact with the Magento_Msrp module. For more information about the extension mechanism, see [Plugins](https://developer.adobe.com/commerce/php/development/components/plugins/).

[The dependency injection mechanism](https://developer.adobe.com/commerce/php/development/components/dependency-injection/) enables you to override the functionality of the Magento_Msrp module.

### Layouts

For more information about a layout, see the [Layout documentation](https://developer.adobe.com/commerce/frontend-core/guide/layouts/).

### UI components

For information about a UI component, see [Overview of UI components](https://developer.adobe.com/commerce/frontend-core/ui-components/).

## Additional information

### collection attributes

This module adds attribute `msrp` to select for the `Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection`
in `Magento\MsrpGroupedProduct\Plugin\Model\Product\Type\Grouped` plugin.

For information about significant changes in patch releases, see [Release information](https://experienceleague.adobe.com/en/docs/commerce-operations/release/notes/overview).
