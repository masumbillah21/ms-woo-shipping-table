<?php

namespace MWST\Inc\WooCommerce;

use MWST\Inc\Database\Shipping_DB;

class Woo_Shipping_Method extends \WC_Shipping_Method {

    public function __construct() {
        $this->id = 'ms_woo_shipping_method';
        $this->method_title = __('MS Woo Shipping', 'mwst');
        $this->method_description = __('Shipping method calculating shipping as sum of product shipping rates.', 'mwst');
        $this->enabled = $this->get_option('enabled', 'yes');
        $this->title = $this->get_option('title', __('Shipping', 'mwst'));
        $this->init();
    }

    public function init() {
        $this->init_form_fields();
        $this->init_settings();
        add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
    }

    public function init_form_fields() {
        $this->form_fields = [
            'enabled' => [
                'title' => __('Enable/Disable', 'mwst'),
                'type' => 'checkbox',
                'label' => __('Enable this shipping method', 'mwst'),
                'default' => 'yes',
            ],
            'title' => [
                'title' => __('Method Title / Label', 'mwst'),
                'type' => 'text',
                'description' => __('This controls the title and label displayed in shipping selection and totals.', 'mwst'),
                'default' => __('Shipping', 'mwst'),
                'desc_tip' => true,
            ],
        ];
    }

    public function calculate_shipping($package = []) {
        $shipping_total = 0;

        if (!empty($package['contents'])) {
            $weight_without_rate = 0;

            foreach ($package['contents'] as $item_id => $values) {
                $product = $values['data'];
                $qty = $values['quantity'];

                $product_rate = floatval(get_post_meta($product->get_id(), '_ms_shipping_rate', true));

                if ($product_rate > 0) {
                    $shipping_total += $product_rate * $qty;
                } else {
                    $product_weight = floatval($product->get_weight());
                    $weight_without_rate += $product_weight * $qty;
                }
            }

            if ($weight_without_rate > 0) {
                $rate_from_table = Shipping_DB::get_rate_by_weight($weight_without_rate);
                if ($rate_from_table !== null) {
                    $shipping_total += $rate_from_table;
                }
            }
        }

        if ($shipping_total > 0) {
            $rate = [
                'id'       => $this->id,
                'label'    => $this->get_option('title', __('Shipping', 'mwst')),
                'cost'     => $shipping_total,
                'calc_tax' => 'per_order',
            ];
            $this->add_rate($rate);
        }
    }

}
