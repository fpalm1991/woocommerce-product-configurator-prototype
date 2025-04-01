<?php

/**
 * Handles enqueueing the Vue Product Configurator
 * and integrating custom product details into the WooCommerce cart and order.
 */

class ProductConfigurator
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, "enqueue_scripts"]);
        add_action('after_setup_theme', [$this, 'add_woocommerce_support']);
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

        add_action('init', function() {

            // Use shortcode [woocommerce_cart] to display custom data in cart.
            add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id, $variation_id) {
                if (isset($_POST['custom_text'])) {
                    $cart_item_data['custom_text'] = sanitize_text_field($_POST['custom_text']);
                }

                if (!empty($_FILES['custom_image']) && !empty($_FILES['custom_image']['tmp_name'])) {
                    // Upload custom image
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    $upload = wp_handle_upload($_FILES['custom_image'], ['test_form' => false]);

                    if (!isset($upload['error'])) {
                        $cart_item_data['custom_image'] = esc_url_raw($upload['url']);
                    }
                }

                return $cart_item_data;
            }, 10, 3);

            add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {
                if (!empty($cart_item['custom_text'])) {
                    $item_data[] = [
                        'name' => 'Custom Text',
                        'value' => $cart_item['custom_text'],
                    ];
                }

                if (!empty($cart_item['custom_image'])) {
                    $item_data[] = [
                        'name' => 'Custom Image',
                        'value' => '<img src="' . esc_url($cart_item['custom_image']) . '" style="max-width:100px;" />',
                    ];
                }

                return $item_data;
            }, 10, 2);

            add_action('woocommerce_new_order_item', function ($item_id, $values) {
                if (!empty($values['custom_text'])) {
                    wc_add_order_item_meta($item_id, 'Custom Text', $values['custom_text']);
                }

                if (!empty($values['custom_image'])) {
                    wc_add_order_item_meta($item_id, 'Custom Image', $values['custom_image']);
                }
            }, 10, 2);

        });
    }

    public function enqueue_scripts(): void
    {
        wp_enqueue_style("main-style", get_template_directory_uri() . "/style/style.css");

        // Product Configurator
        wp_enqueue_style("prod-config-style", get_template_directory_uri() . "/product-configurator-vue/dist/assets/index-CBkn8FHV.css", [], null);
        wp_enqueue_script('prod-config-app', get_template_directory_uri() . "/product-configurator-vue/dist/assets/index-DJqoOEJl.js", [], null, true);
    }

    public function add_woocommerce_support()
    {
        add_theme_support('woocommerce');
    }
}

new ProductConfigurator();
