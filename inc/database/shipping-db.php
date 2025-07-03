<?php
namespace MWST\Inc\Database;

class Shipping_DB {

    private static $table_name;
    private static $version = '1.0'; // Current DB schema version
    private static $option_name = 'mwst_shipping_db_version';

    /**
     * Initialize static properties
     */
    private static function init() {
        global $wpdb;
        if (!isset(self::$table_name)) {
            self::$table_name = $wpdb->prefix . 'mwst_shipping_table';
        }
    }

    /**
     * Create or update the table structure if version changes
     */
    public static function maybe_upgrade() {
        self::init();
        if (!self::check_version()) {
            self::create_table();
            update_option(self::$option_name, self::$version);
        }
    }

    /**
     * Create table
     */
    private static function create_table() {
        global $wpdb;
        self::init();

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE " . self::$table_name . " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            product_weight float NOT NULL,
            shipping_rate float NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Drop table
     */
    public static function drop_table() {
        global $wpdb;
        self::init();

        $sql = "DROP TABLE IF EXISTS " . self::$table_name . ";";
        $wpdb->query($sql);

        delete_option(self::$option_name);
    }

    /**
     * Insert new row
     */
    public static function insert($product_weight, $shipping_rate) {
        global $wpdb;
        self::init();

        $wpdb->insert(
            self::$table_name,
            array(
                'product_weight' => $product_weight,
                'shipping_rate' => $shipping_rate,
                'created_at' => current_time('mysql'),
            ),
            array('%f', '%f', '%s')
        );
    }

    /**
     * Get all rows
     */
    public static function get_all() {
        global $wpdb;
        self::init();

        return $wpdb->get_results("SELECT * FROM " . self::$table_name, ARRAY_A);
    }

    /**
     * Get by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        self::init();

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM " . self::$table_name . " WHERE id = %d", $id), ARRAY_A);
    }

    /**
     * Update row by ID
     */
    public static function update($id, $product_weight, $shipping_rate) {
        global $wpdb;
        self::init();

        $wpdb->update(
            self::$table_name,
            array(
                'product_weight' => $product_weight,
                'shipping_rate' => $shipping_rate,
                'created_at' => current_time('mysql'),
            ),
            array('id' => $id),
            array('%f', '%f', '%s'),
            array('%d')
        );
    }

    public static function get_rate_by_weight(float $weight) {
        global $wpdb;
        self::init();

        $sql = $wpdb->prepare(
            "SELECT shipping_rate FROM " . self::$table_name . " WHERE product_weight <= %f ORDER BY product_weight DESC LIMIT 1",
            $weight
        );

        $rate = $wpdb->get_var($sql);
        if ($rate === null) {
            return null;
        }
        return floatval($rate);
    }

    /**
     * Delete row by ID
     */
    public static function delete($id) {
        global $wpdb;
        self::init();

        $wpdb->delete(self::$table_name, array('id' => $id), array('%d'));
    }

    /**
     * Check DB version
     */
    private static function check_version() {
        $installed_version = get_option(self::$option_name);

        return $installed_version === self::$version;
    }
}
