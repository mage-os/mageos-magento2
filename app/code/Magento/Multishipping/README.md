# Magento_Multishipping module

This module provides functionality that allows customer to request shipping to more than one address using different carriers. The module provides alternative to standard checkout flow.

## Installation

For information about a module installation, see [Enable or disable modules](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/tutorials/manage-modules).

## Structure

For information about a typical file structure of a module,
 see [Module file structure](https://developer.adobe.com/commerce/php/development/build/component-file-structure/#module-file-structure).

## Extensibility

Developers can interact with the module and change behavior using type configuration feature.

Namely, we can change `paymentSpecification` for `Magento\Multishipping\Block\Checkout\Billing` and `Magento\Multishipping\Model\Checkout\Type\Multishipping` classes.
As result, we will get changed behavior, new logic or something what our business need.

For example:

```xml
<type name="Magento\Multishipping\Model\Checkout\Type\Multishipping">
     <arguments>
         <argument name="paymentSpecification" xsi:type="object">multishippingPaymentSpecification</argument>
     </arguments>
</type>
```

You can check this configuration and find more examples in the `etc/frontend/di.xml` file.

More information about [type configuration](https://developer.adobe.com/commerce/php/development/build/dependency-injection-file/).

Extension developers can interact with the Magento_Multishipping module. For more information about the extension mechanism, see [Plugins](https://developer.adobe.com/commerce/php/development/components/plugins/).

[The dependency injection mechanism](https://developer.adobe.com/commerce/php/development/components/dependency-injection/) enables you to override the functionality of the Magento_Msrp module.

### Events

This module observes the following event:

`etc/frontend/`

- `checkout_cart_save_before` in the `Magento\Multishipping\Observer\DisableMultishippingObserver` file.

The module dispatches the following events:

- `multishipping_checkout_controller_success_action` event in the
  class `\Magento\Multishipping\Controller\Checkout\Success::execute()` method. Parameters:
    - `order_ids` is order ids created during checkout
- `checkout_controller_multishipping_shipping_post` event in the
  class `\Magento\Multishipping\Controller\Checkout\ShippingPost::execute()` method. Parameters:
    - `request` is a request object `Magento\Framework\App\RequestInterface`.
    - `quote` is a quote object for current checkout `Magento\Quote\Model\Quote`.
- `checkout_type_multishipping_set_shipping_items` event in the
  class `\Magento\Multishipping\Model\Checkout\Type\Multishipping::setShippingItemsInformation()` method. Parameters:
    - `quote` is a quote object for current checkout `Magento\Quote\Model\Quote`.
- `checkout_type_multishipping_create_orders_single` event in the
  class `\Magento\Multishipping\Model\Checkout\Type\Multishipping::createOrders()` method. Parameters:
    - `order` is a prepared order object for creating `\Magento\Sales\Model\Order`.
    - `address` is an address array.
    - `quote` is a quote object for current checkout `Magento\Quote\Model\Quote`.
- `checkout_submit_all_after` event in the
  class `\Magento\Multishipping\Model\Checkout\Type\Multishipping::createOrders()` method. Parameters:
    - `orders` is order object array `\Magento\Sales\Model\Order`  that was created.
    - `quote` is a quote object for current checkout `Magento\Quote\Model\Quote`.
- `checkout_multishipping_refund_all` event in the
  class `\Magento\Multishipping\Model\Checkout\Type\Multishipping::createOrders()` method. Parameters:
    - `orders` is order object array `\Magento\Sales\Model\Order`  that was created.

For information about an event, see [Events and observers](https://developer.adobe.com/commerce/php/development/components/events-and-observers/#events).

### Layouts

The module interacts with the following layout handles:

`view/frontend/layout` directory:

- `checkout_cart_index`

This module introduces the following layouts and layout handles:

`view/frontend/layout` directory:

- `multishipping_checkout`
- `multishipping_checkout_address_editaddress`
- `multishipping_checkout_address_editbilling`
- `multishipping_checkout_address_editshipping`
- `multishipping_checkout_address_newbilling`
- `multishipping_checkout_address_newshipping`
- `multishipping_checkout_address_select`
- `multishipping_checkout_address_selectbilling`
- `multishipping_checkout_addresses`
- `multishipping_checkout_billing`
- `multishipping_checkout_customer_address`
- `multishipping_checkout_login`
- `multishipping_checkout_overview`
- `multishipping_checkout_register`
- `multishipping_checkout_results`
- `multishipping_checkout_shipping`
- `multishipping_checkout_success`

## Additional information

### ACL

This module introduces the following resources:

- `Magento_Multishipping::config_multishipping` - Multishipping Settings Section

More information about [Access Control List rule](https://developer.adobe.com/commerce/php/tutorials/backend/create-access-control-list-rule/).

### Page Types

This module introduces the new pages:

`etc/frontend/page_types.xml` file.

- `checkout_cart_multishipping` - Catalog Quick Search Form Suggestion
- `checkout_cart_multishipping_address_editaddress` - Multishipping Checkout One Address Edit Form
- `checkout_cart_multishipping_address_editbilling` - Multishipping Checkout Billing Address Edit Form
- `checkout_cart_multishipping_address_editshipping` - Multishipping Checkout Shipping Address Edit Form
- `checkout_cart_multishipping_address_newbilling` - Multishipping Checkout Billing Address Creation
- `checkout_cart_multishipping_address_newshipping` - Multishipping Checkout Shipping Address Creation
- `checkout_cart_multishipping_address_selectbilling` - Multishipping Checkout Billing Address Selection
- `checkout_cart_multishipping_addresses` - Multishipping Checkout Address (Any) Form
- `checkout_cart_multishipping_billing` - Multishipping Checkout Billing Information Step
- `checkout_cart_multishipping_customer_address` - Multishipping Checkout Customer Address Edit Form
- `checkout_cart_multishipping_login` - Multishipping Checkout Login User Form
- `checkout_cart_multishipping_overview` - Multishipping Checkout Overview
- `checkout_cart_multishipping_register` - Multishipping Checkout Register User Form
- `checkout_cart_multishipping_shipping` - Multishipping Checkout Shipping Information Step
- `checkout_cart_multishipping_success` - Multishipping Checkout Success

More information about [layout types](https://developer.adobe.com/commerce/frontend-core/guide/layouts/types/).

For information about significant changes in patch releases, see [Release information](https://experienceleague.adobe.com/en/docs/commerce-operations/release/notes/overview).
