<?php 
namespace MWST\Inc\WooCommerce;

class Woo_Init {
    public function __construct() {
        new Woo_Shipping_Option();

        add_filter('woocommerce_shipping_methods', [$this, 'register_shipping_method']);
    }


    public function register_shipping_method($methods) {
        $methods['ms_woo_shipping_method'] = __NAMESPACE__ . '\\Woo_Shipping_Method';
        return $methods;
    }
}