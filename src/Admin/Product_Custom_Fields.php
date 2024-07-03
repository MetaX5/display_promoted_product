<?php

namespace MM\WoocommercePromotedProduct\Admin;

/**
 * Adding new custom fields and saving them
 * Enqueue scripts and styles for datetime picker
 * Check if promoted product is only one
 */
class Product_Custom_Fields
{

    public function __construct()
    {
        // add the custom fields
        add_action('woocommerce_product_options_general_product_data', array($this, 'createCustomTextField1'));
        add_action('woocommerce_product_options_general_product_data', array($this, 'createCustomCheckbox1'));
        add_action('woocommerce_product_options_general_product_data', array($this, 'createCustomCheckbox2'));
        add_action('woocommerce_product_options_general_product_data', array($this, 'createCustomTextField2'));

        // save the custom fields
        add_action('woocommerce_process_product_meta',  array($this, 'customFieldsSave'));

        // enqueue date picker
        add_action('admin_enqueue_scripts', array($this, 'enqueueDatePicker'));

        // check if promoted product is only one
        add_action("wp_after_insert_post", array($this, 'setOnePromotedProduct'));
    }

    public function createCustomTextField1()
    {
        $args = array(
            'id' => 'wpp_text_field_title',
            'label' => esc_html__('Promotion Title', WPP_TEXT_DOMAIN),
            'class' => '',
            'desc_tip' => true,
            'description' => esc_html__('Enter the promotion product title.', WPP_TEXT_DOMAIN),
        );
        woocommerce_wp_text_input($args);
    }

    public function createCustomTextField2()
    {
        $args = array(
            'id' => 'wpp_date_to_remove_promotion',
            'label' => esc_html__('', WPP_TEXT_DOMAIN),
            'class' => 'wpp_date',
        );
        woocommerce_wp_text_input($args);
    }

    public function createCustomCheckbox1()
    {
        $args = array(
            'id' => 'wpp_checkbox_1',
            'label' => esc_html__('Promote this product', WPP_TEXT_DOMAIN),
            'class' => '',
            'desc_tip' => true,
            'description' => esc_html__('Check if you want to promote this product', WPP_TEXT_DOMAIN),
        );
        woocommerce_wp_checkbox($args);
    }

    public function createCustomCheckbox2()
    {
        $args = array(
            'id' => 'wpp_checkbox_2',
            'label' => esc_html__('Expiration date', WPP_TEXT_DOMAIN),
            'class' => '',
            'desc_tip' => true,
            'description' => esc_html__('Check if you want to set an expiration date', WPP_TEXT_DOMAIN),
        );
        woocommerce_wp_checkbox($args);
    }

    /**
     * Save the custom fields from the product
     * Save the promoted product id
     * Save the previous promoted product id
     * Save the date to remove promotion
     *
     * @param [type] $post_id
     * @return void
     */
    public function customFieldsSave($post_id)
    {
        // save the title for promoted product
        $wc_text = isset($_POST['wpp_text_field_title']) ? esc_textarea($_POST['wpp_text_field_title']) : null;
        update_post_meta($post_id, 'wpp_text_field_title', $wc_text);

        // save the checkbox for promoted product
        $wc_checkbox1 = isset($_POST['wpp_checkbox_1']) ? 'yes' : 'no';
        update_post_meta($post_id, 'wpp_checkbox_1', $wc_checkbox1);

        // save the checkbox for enabling date
        $wc_checkbox2 = isset($_POST['wpp_checkbox_2']) ? 'yes' : 'no';
        update_post_meta($post_id, 'wpp_checkbox_2', $wc_checkbox2);

        if ($wc_checkbox1 == 'yes') {
            if (!get_option('wpp_promoted_product_id')) {
                add_option('wpp_promoted_product_id', $post_id);
            } else {
                if (!get_option('wpp_previous_promoted_product_id')) {
                    add_option('wpp_previous_promoted_product_id', get_option('wpp_promoted_product_id'));
                } else {
                    if (get_option('wpp_promoted_product_id') != $post_id) {
                        update_option('wpp_previous_promoted_product_id', get_option('wpp_promoted_product_id'));
                        update_option('wpp_promoted_product_id', $post_id);
                    }
                }
            }
        }

        // save the expiration date
        if ($wc_checkbox2 == 'yes') {
            $wc_text = isset($_POST['wpp_date_to_remove_promotion']) ? esc_textarea($_POST['wpp_date_to_remove_promotion']) : '';
            update_post_meta($post_id, 'wpp_date_to_remove_promotion', $wc_text);
            if ($wc_checkbox1 == 'yes') {
                if (get_option('wpp_date_to_remove_promotion')) {
                    add_option('wpp_date_to_remove_promotion', $wc_text);
                } else {
                    update_option('wpp_date_to_remove_promotion', $wc_text);
                }
            }
        } else {
            if (metadata_exists('post', $post_id, 'wpp_date_to_remove_promotion')) {
                delete_post_meta($post_id, 'wpp_date_to_remove_promotion');
            }
            if (get_option('wpp_date_to_remove_promotion')) {
                delete_option('wpp_date_to_remove_promotion');
            }
        }
    }

    /**
     * Enqueue scripts and styles for datetime picker
     *
     * @param [type] $hook
     * @return void
     */
    public function enqueueDatePicker($hook)
    {
        if ('post.php' != $hook) {
            return;
        }

        wp_enqueue_script(
            'field-date',
            WPP_PLUGIN_DIR . '/assets/admin/js/field-date.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
            time(),
            true
        );

        wp_enqueue_script(
            'datetimepicker-js',
            WPP_PLUGIN_DIR . '/assets/admin/js/jquery.datetimepicker.full.min.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
            time(),
            true
        );

        wp_enqueue_style('jquery-ui-datepicker');
        wp_enqueue_style(
            'datetimepicker-css',
            WPP_PLUGIN_DIR . '/assets/admin/css/jquery.datetimepicker.min.css',
            array(),
            time(),
            'all'
        );
    }

    /**
     * Remove promotion from previous promoted product when new product is promoted
     *
     * @return void
     */
    public function setOnePromotedProduct()
    {
        if (get_option('wpp_previous_promoted_product_id') !== false) {
            $previousProduct_Id = get_option('wpp_previous_promoted_product_id');
            update_post_meta($previousProduct_Id, 'wpp_checkbox_1', 'no');
            update_post_meta($previousProduct_Id, 'wpp_checkbox_2', 'no');
            update_post_meta($previousProduct_Id, 'wpp_date_to_remove_promotion', '');
        }
    }
}
