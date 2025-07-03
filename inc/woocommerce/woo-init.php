<?php
namespace MWST\Inc\WooCommerce;

class Woo_Init {

    public function __construct() {
        new Woo_Shipping_Option();

        // Initialize shipping method class after WooCommerce shipping is loaded
        add_action('woocommerce_shipping_init', [$this, 'shipping_method_init']);

        add_filter('woocommerce_shipping_methods', [$this, 'register_shipping_method']);
    }

    public function shipping_method_init() {
        new Woo_Shipping_Method();
    }

    public function register_shipping_method($methods) {
        $methods['ms_woo_shipping_method'] = __NAMESPACE__ . '\\Woo_Shipping_Method';
        return $methods;
    }
}
