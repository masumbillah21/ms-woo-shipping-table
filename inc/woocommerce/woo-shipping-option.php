<?php 
namespace MWST\Inc\WooCommerce;

class Woo_Shipping_Option {
    public function __construct() {
        // Add product shipping rate field
        add_action('woocommerce_product_options_shipping', [$this, 'add_shipping_rate_field']);
        add_action('woocommerce_process_product_meta', [$this, 'save_shipping_rate_field']);
    }

    public function add_shipping_rate_field() {
        woocommerce_wp_text_input([
            'id' => '_ms_shipping_rate',
            'label' => __('Shipping Rate ($)', 'mwst'),
            'desc_tip' => true,
            'description' => __('Enter shipping rate for this product.', 'mwst'),
            'type' => 'number',
            'custom_attributes' => [
                'step' => '0.01',
                'min' => '0',
            ],
        ]);
    }

    public function save_shipping_rate_field($post_id) {
        $rate = isset($_POST['_ms_shipping_rate']) ? wc_clean(wp_unslash($_POST['_ms_shipping_rate'])) : '';
        if ($rate !== '') {
            update_post_meta($post_id, '_ms_shipping_rate', floatval($rate));
        } else {
            delete_post_meta($post_id, '_ms_shipping_rate');
        }
    }
}