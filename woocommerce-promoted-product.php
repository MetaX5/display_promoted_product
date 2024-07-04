<?php

/**
 * Plugin Name: WooCommerce Promoted Product
 * Description:
 * Author: Mateusz Minkiewicz
 * Version: 1.0
 * Text Domain: woocommerce-promoted-product
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 */

/**
 * If this file is called directly, then abort execution.
 */
if (!defined('ABSPATH')) {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use MM\WoocommercePromotedProduct\Admin\ProductCustomFields;
use MM\WoocommercePromotedProduct\Admin\WoocommerceCustomSettings;
use MM\WoocommercePromotedProduct\Frontend\PromotionDivShortcode;
use MM\WoocommercePromotedProduct\Cron\ProductPromotionTime;
use MM\WoocommercePromotedProduct\Frontend\EnqueueAssets;

/**
 * Global constant for text domain
 */
if (!defined('WPP_TEXT_DOMAIN')) {
    define('WPP_TEXT_DOMAIN', 'woocommerce-promoted-product');
}

/**
 * Global constant for plugin directory url
 */
if (!defined('WPP_PLUGIN_DIR')) {
    define('WPP_PLUGIN_DIR', plugin_dir_url(__FILE__));
}

final class Woocommerce_Promoted_Product
{
    public function __construct()
    {
        register_deactivation_hook(__FILE__, 'wppDeactivate');
        register_uninstall_hook(__FILE__, 'wppUninstall');

        new ProductCustomFields();
        new WoocommerceCustomSettings();
        new PromotionDivShortcode();
        new ProductPromotionTime();
        new EnqueueAssets();
    }

    /**
     * Remove the scheduled event when deactivating the plugin
     *
     * @return void
     */
    public function wppDeactivate()
    {
        $timestamp = wp_next_scheduled('wpp_cron_hook');
        wp_unschedule_event($timestamp, 'wpp_cron_hook');
    }

    /**
     * Clean up when uninstalling the plugin
     */
    public function wppUninstall()
    {
        delete_option('wpp_promoted_product_id');
        delete_option('wpp_previous_promoted_product_id');
        delete_option('wpp_date_to_remove_promotion');
    }
}

new Woocommerce_Promoted_Product();
