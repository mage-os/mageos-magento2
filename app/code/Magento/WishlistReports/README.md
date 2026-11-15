# Magento_WishlistReports module

This module connects Magento_Wishlist to Magento_Reports. It records the "Product Added To Wish List" and "Share Wish List" events in the Reports event log, and adds the settings that enable them under **Stores > Configuration > General > Reports > General Options**.

Magento_Reports and Magento_Wishlist don't depend on each other. Either one can be removed, together with this module.

## Installation details

Before installing this module, note that this module is dependent on the following modules:

- Magento_Reports
- Magento_Wishlist

For information about enabling or disabling a module, see [Enable or disable modules](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/tutorials/manage-modules).

## Extensibility

Extension developers can interact with the Magento_WishlistReports module. For more information about the Magento extension mechanism, see [Magento plugins](https://developer.adobe.com/commerce/php/development/components/plugins/).

[The Magento dependency injection mechanism](https://developer.adobe.com/commerce/php/development/components/dependency-injection/) enables you to override the functionality of the Magento_WishlistReports module.

### Events

This module observes the following events:

- `wishlist_add_product` in `Magento\WishlistReports\Observer\WishlistAddProductObserver`
- `wishlist_share` in `Magento\WishlistReports\Observer\WishlistShareObserver`
