<?php 

/*
 * Plugin Name:       MS Woo Shipping Table
 * Plugin Slug:       ms-woo-shipping-table
 * Plugin URI:        http://masum-billah.com
 * Description:       WooCommerce Shipping Table Plugin
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.2
 * Author:            H M Masum Billah
 * Author URI:        http://masum-billah.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        http://masum-billah.com
 * Text Domain:       mwst
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/autoloader.php';

if( ! defined('MWST_VERSION') ) define( 'MWST_VERSION', '1.0.0' );

if( ! defined('MWST_DIR_PATH') ) define( 'MWST_DIR_PATH', plugin_dir_path(__FILE__) );

if( ! defined('MWST_PATH_URL') ) define( 'MWST_PATH_URL', plugin_dir_url(__FILE__) );

function mwst_plugin_init() {

    new MWST\Inc\MWST_Init();
}
add_action( 'init', 'mwst_plugin_init' );

register_activation_hook( __FILE__, [ 'MWST\Inc\MWST_Init', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'MWST\Inc\MWST_Init', 'deactivate' ] );
register_uninstall_hook( __FILE__, [ 'MWST\Inc\MWST_Init', 'uninstall' ] );