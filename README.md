# Woo Shipping Rate Table

**Contributors:** H. M. Masum Billah  
**Requires at least:** 5.0  
**Tested up to:** 6.x  
**Requires PHP:** 7.2  
**WC requires at least:** 4.0  
**WC tested up to:** 7.x  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

---

## Description

Woo Shipping Rate Table plugin provides an advanced shipping calculation system for WooCommerce stores. It allows you to:

- Define per-product shipping rates directly on the product edit page.
- Manage a flexible shipping rate table based on product weight via a dedicated admin interface.
- Automatically calculate shipping cost per order by combining:
  - Product-specific shipping rates (if set),
  - Weight-based shipping rates from the table for products without explicit shipping rates.

This plugin seamlessly integrates with WooCommerce's shipping methods API to provide a custom shipping method that dynamically computes the shipping charge based on the cart contents.

---

## Features

- Add a **Shipping Rate ($)** field to WooCommerce product edit pages.
- Manage a **shipping rate table** in WordPress admin with weight-to-rate mappings.
- Automatically calculate shipping during checkout considering both product rates and weight-based rates.
- Custom shipping method integrated in WooCommerce shipping zones.
- Clean and extendable code architecture using namespaces and class separation.
- Database versioning with upgrade support.

---

## Installation

1. Upload the plugin folder `ms-woo-shipping-table` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the ‘Plugins’ menu in WordPress.
3. The plugin will create a custom DB table for weight-based shipping rates automatically on activation.
4. Configure your shipping rates:
   - Edit WooCommerce products and add shipping rates under the **Shipping** tab.
   - Go to the **Shipping Table** menu in the WordPress admin to add/edit/delete weight-based shipping rates.
5. Go to **WooCommerce > Settings > Shipping > MS Woo Shippings**.
6. Add or edit a shipping zone and add the **MS Woo Shipping** method.
7. Configure the method’s title and enable it.
8. Shipping cost will be calculated during cart and checkout combining product rates and weight-based rates.

---

## Usage

### Product Shipping Rate

- Edit a product in WooCommerce admin.
- Under the **Shipping** tab, enter a **Shipping Rate ($)**.
- If no rate is entered, the plugin uses the weight-based table for that product’s total weight.

### Shipping Rate Table

- Go to **Shipping Table** in the WordPress admin menu.
- Add new rows defining:
  - **Weight** (maximum weight for this rate),
  - **Shipping Charge** (cost to charge if weight falls in this bracket).
- Edit or delete existing rows as needed.
- The plugin picks the best match for the weight when calculating shipping for products without product-specific rates.

### Checkout Shipping Calculation

- Products with a shipping rate use that value multiplied by quantity.
- Products without a shipping rate use the weight-based table rate for their total weight.
- The shipping method sums both parts and charges accordingly.

---

## Developer Notes

- The plugin uses a dedicated class `MWST\Inc\Services\Database\Shipping_DB` for all database interactions.
- The admin UI for managing weight-based shipping rates is in `MWST\Inc\Admin\Shipping_table`.
- The custom WooCommerce shipping method is implemented in `MWST\Inc\WooCommerce\Woo_Shipping_Method`.
- The code uses proper namespace and WordPress hooks to ensure compatibility.
- Shipping method registration happens on the `woocommerce_shipping_init` hook.
- Activation hook creates or updates the custom DB table.
- Shipping method label is configurable via WooCommerce shipping method settings.

---

## Changelog

### 1.0.0

- Initial release with product shipping rates, weight-based shipping table, and custom WooCommerce shipping method.

---

## License

This plugin is licensed under the GPLv2 or later.
