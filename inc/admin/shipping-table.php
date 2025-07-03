<?php
namespace MWST\Inc\Admin;

use MWST\Inc\Database\Shipping_DB;

if (!defined('ABSPATH')) exit;

class Shipping_table {

    public function __construct() {
        // Add admin menu
        add_action('admin_menu', [$this, 'add_admin_menu']);

        // Handle form submissions
        add_action('admin_post_ms_shipping_save', [$this, 'handle_form']);
        add_action('admin_post_ms_shipping_delete', [$this, 'handle_delete']);
    }

    public function add_admin_menu() {
        add_menu_page(
            __('MS Shipping Table', 'mwst'),
            __('MS Shipping Table', 'mwst'),
            'manage_woocommerce',
            'ms-shipping-table',
            [$this, 'render_admin_page'],
            'dashicons-admin-generic',
            56
        );
    }

    public function render_admin_page() {
        // Handle edit row
        $edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
        $edit_row = null;
        if ($edit_id) {
            $edit_row = Shipping_DB::get_by_id($edit_id);
        }

        // Fetch all rows
        $rows = Shipping_DB::get_all();

        ?>
        <div class="wrap">
            <h1><?php echo $edit_id ? __('Edit Shipping Rate', 'mwst') : __('Add Shipping Rate', 'mwst'); ?></h1>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('ms_shipping_save', 'ms_shipping_nonce'); ?>
                <input type="hidden" name="action" value="ms_shipping_save">
                <input type="hidden" name="id" value="<?php echo esc_attr($edit_id); ?>">

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="weight"><?php _e('Weight', 'mwst'); ?></label></th>
                            <td><input name="weight" type="number" step="0.01" min="0" required id="weight" value="<?php echo esc_attr($edit_row['product_weight'] ?? ''); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="charge"><?php _e('Shipping Charge', 'mwst'); ?></label></th>
                            <td><input name="charge" type="number" step="0.01" min="0" required id="charge" value="<?php echo esc_attr($edit_row['shipping_rate'] ?? ''); ?>" class="regular-text"></td>
                        </tr>
                    </tbody>
                </table>

                <?php submit_button($edit_id ? __('Update Shipping Rate', 'mwst') : __('Add Shipping Rate', 'mwst')); ?>
            </form>

            <hr>

            <h2><?php _e('Shipping Rates List', 'mwst'); ?></h2>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('ID', 'mwst'); ?></th>
                        <th><?php _e('Weight', 'mwst'); ?></th>
                        <th><?php _e('Shipping Charge', 'mwst'); ?></th>
                        <th><?php _e('Actions', 'mwst'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rows) : ?>
                        <?php foreach ($rows as $row) : ?>
                            <tr>
                                <td><?php echo esc_html($row['id']); ?></td>
                                <td><?php echo esc_html($row['product_weight']); ?></td>
                                <td><?php echo wc_price($row['shipping_rate']); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(add_query_arg(['page' => 'ms-shipping-table', 'edit_id' => $row['id']])); ?>"><?php _e('Edit', 'mwst'); ?></a> |
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=ms_shipping_delete&id=' . $row['id']), 'ms_shipping_delete_' . $row['id'])); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete this shipping rate?', 'mwst'); ?>');"><?php _e('Delete', 'mwst'); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="4"><?php _e('No shipping rates found.', 'mwst'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    // Handle add/update form submission
    public function handle_form() {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('Unauthorized user', 'mwst'));
        }

        check_admin_referer('ms_shipping_save', 'ms_shipping_nonce');

        if (empty($_POST['weight']) || !is_numeric($_POST['weight'])) {
            wp_die(__('Invalid weight value', 'mwst'));
        }

        if (empty($_POST['charge']) || !is_numeric($_POST['charge'])) {
            wp_die(__('Invalid shipping charge value', 'mwst'));
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $weight = floatval($_POST['weight']);
        $charge = floatval($_POST['charge']);

        if ($id > 0) {
            Shipping_DB::update($id, $weight, $charge);
        } else {
            Shipping_DB::insert($weight, $charge);
        }

        wp_redirect(add_query_arg('page', 'ms-shipping-table', admin_url('admin.php')));
        exit;
    }

    // Handle delete request
    public function handle_delete() {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('Unauthorized user', 'mwst'));
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$id) {
            wp_die(__('Invalid ID', 'mwst'));
        }

        check_admin_referer('ms_shipping_delete_' . $id);

        Shipping_DB::delete($id);

        wp_redirect(add_query_arg('page', 'ms-shipping-table', admin_url('admin.php')));
        exit;
    }
}
