<?php
namespace MWST\Inc;

use MWST\Inc\Admin\Shipping_table;
use MWST\Inc\Database\Shipping_DB;
use MWST\Inc\WooCommerce\Woo_Init;

class MWST_Init {

    public function __construct() {
        $this->load_hooks();
        new Woo_Init();
        new Shipping_table();
    }


    private function load_hooks(){
        add_action('wp_enqueue_scripts', [$this, 'load_styles']);
        add_action('wp_enqueue_scripts', [$this, 'load_scripts']);
    }

    public function load_styles(){
        wp_enqueue_style(
            'mwst-plugin-style', 
            MWST_PATH_URL . 'assets/css/style.css', // Path to CSS file
            [],
            MWST_VERSION,
            'all'
        );
    }


    public function load_scripts() {
        wp_enqueue_script(
            'mwst-plugin-script',
            MWST_PATH_URL . 'assets/js/script.js',
            ['jquery'],
            MWST_VERSION, 
            true
        );

        $localized_data = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('mwst_plugin_nonce'),
            'plugin_url' => MWST_PATH_URL
        ];

        wp_localize_script('mwst-plugin-script', 'mwstPluginData', $localized_data);
    }

    /**
     * The activation hook for the plugin.
     * This method will run when the plugin is activated.
     */
    public static function activate() {

        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        Shipping_DB::maybe_upgrade();

        add_option( 'mwst_plugin_activated', true );

    }

    /**
     * The deactivation hook for the plugin.
     * This method will run when the plugin is deactivated.
     */
    public static function deactivate() {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        delete_option( 'mwst_plugin_activated' );
    }

    public static function uninstall() {

        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        Shipping_DB::drop_table();

        delete_option( 'mwst_plugin_activated' );
        
    }
}
