# Magento_CustomerDownloadableGraphQl module

This module provides type and resolver information for the GraphQl module to generate downloadable product information.

## Installation

Before installing this module, note that the Magento_CustomerDownloadableGraphQl module is dependent on the following modules:

- `Magento_GraphQl`
- `Magento_DownloadableGraphQl`

For information about a module installation, see [Enable or disable modules](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/tutorials/manage-modules).

## Extensibility

Extension developers can interact with the Magento_CatalogGraphQl module. For more information about the extension mechanism, see [Plugins](https://developer.adobe.com/commerce/php/development/components/plugins/).

[The dependency injection mechanism](https://developer.adobe.com/commerce/php/development/components/dependency-injection/) enables you to override the functionality of the Magento_CustomerDownloadableGraphQl module.

## Additional information

You can get more information about GraphQl in the [Adobe Developer documentation](https://developer.adobe.com/commerce/webapi/graphql/).

### GraphQl Query

- `customerDownloadableProducts` query - retrieve the list of purchased downloadable products for the logged-in customer

[Learn more about customerDownloadableProducts query](https://developer.adobe.com/commerce/webapi/graphql/schema/customer/queries/downloadable-products/).
